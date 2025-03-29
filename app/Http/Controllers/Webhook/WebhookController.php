<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\ShopifyOrder;
use App\Models\ShopifyOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Log the incoming request for debugging
        Log::info('Zapier Webhook Data:', $request->all());

        // Validate required fields
        $validatedData = $request->validate([
            'order_id' => 'required|integer',
            'email' => 'required|email',
            'total_price' => 'required|numeric',
            'currency' => 'required|string',
            'created_at' => 'required|date',
            'customer_first_name' => 'required|string',
            'customer_last_name' => 'required|string',
            'line_items' => 'required|array',
        ]);

        // Save the order to the database
        $order = ShopifyOrder::create([
            'order_id' => $validatedData['order_id'],
            'email' => $validatedData['email'],
            'total_price' => $validatedData['total_price'],
            'currency' => $validatedData['currency'],
            'created_at' => $validatedData['created_at'],
            'customer_name' => $validatedData['customer_first_name'] . ' ' . $validatedData['customer_last_name'],
        ]);

        // Save order items
        foreach ($validatedData['line_items'] as $item) {
            ShopifyOrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'name' => $item['name'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);
        }

        return response()->json(['message' => 'Webhook received successfully'], 200);
    }
}
