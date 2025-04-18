<?php
namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\ShopifyOrder;
use App\Models\ShopifyOrderItem;
use App\Models\UserTrader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Models\SlaveTrader;
use Illuminate\Support\Str;
use App\Mail\SlaveAccountPurchased; // Import the Mailable
use Illuminate\Support\Facades\Mail; // Import the Mail facade

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        Log::alert(json_encode($request->all()));
        try {
            // Validate required fields
            $validatedData = $request->validate([
                'order_id' => 'required|integer',
                'email' => 'required|email',
                'total_price' => 'required|numeric',
                'currency' => 'required|string',
                'created_at' => 'required|date',
                'customer_first_name' => 'required|string',
                'customer_last_name' => 'required|string',
                'product_1_name' => 'required|string',
                'product_1_price' => 'required|numeric',
                'product_1_quantity' => 'required|integer|min:1',
            ]);

            // Fetch UserTrader with its associated traders
            $userTrader = UserTrader::with('trader')->where('name', $validatedData['product_1_name'])->first();

// Check if UserTrader exists and has associated traders
            if (!$userTrader || $userTrader->trader->isEmpty()) {
                return response()->json([
                    'message' => 'UserTrader or Trader not found for the given product name'
                ], 404);
            }

// Get all trader codes
            $masterIds = $userTrader->trader->pluck('code');  // This will return a collection of all trader codes


            // Save the order to the database
            $order = ShopifyOrder::create([
                'order_id' => $validatedData['order_id'],
                'email' => $validatedData['email'],
                'total_price' => $validatedData['total_price'],
                'currency' => $validatedData['currency'],
                'created_at' => $validatedData['created_at'],
                'customer_name' => $validatedData['customer_first_name'] . ' ' . $validatedData['customer_last_name'],
            ]);

            // Create SlaveTrader record
            $slave_trader = SlaveTrader::create([
                'name' => $userTrader->name,
                'code' => 'SLAVE-' . strtoupper(Str::random(15)),
                'order_id' =>1,// $order->id,
                'connection_status' => false, // or false if initially disconnected
                'status' => true, // assuming default status is active
            ]);

            // Save the single order item
            ShopifyOrderItem::create([
                'order_id' => $order->id,
                'product_id' => $userTrader->id, // Using UserTrader id as product_id
                'name' => $userTrader->name, // Using UserTrader name
                'quantity' => $validatedData['product_1_quantity'],
                'price' => $validatedData['product_1_price'],
                'slave_traders_id' => $slave_trader->id,
            ]);

            // Send email with all master IDs (codes)
            \Illuminate\Support\Facades\Mail::to($validatedData['email'])->send(new \App\Mail\SlaveAccountPurchased($masterIds,$slave_trader->code,$slave_trader->name));

            // Send email to the user about the successful slave account purchase
            //Mail::to($validatedData['email'])->send(new SlaveAccountPurchased($slave_trader, $userTrader));

            return response()->json(['message' => 'Webhook received successfully'], 200);
        } catch (ValidationException $e) {
            // Log validation errors
            Log::error('Validation Error:', $e->errors());

            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // Log unexpected errors
            Log::error('Webhook Processing Error:', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'An error occurred while processing the webhook',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
