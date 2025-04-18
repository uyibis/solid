<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Trader;
use App\Models\UserTrader;
use Illuminate\Http\Request;
use App\Mail\MasterTraderAccountCreated;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class MasterController extends Controller
{
    public function create(Request $request){
        return view('trader.create');
    }



    public function process_create(Request $request)
    {
        try {
            $request->validate([
                'trader_code' => 'required|string',
                'name' => 'required|string|max:255|unique:user_traders,name',
                'email' => 'nullable|email',
                'phone' => 'nullable|string|max:20',
            ]);

            // Split trader_code by semicolon and trim whitespace
            $codes = array_filter(array_map('trim', explode(';', $request->trader_code)));

            if (empty($codes)) {
                return response()->json(['success' => false, 'message' => 'No valid trader code found'], 400);
            }

            // Fetch traders by those codes
            $traders = Trader::whereIn('code', $codes)->get();

            if ($traders->count() !== count($codes)) {
                return response()->json(['success' => false, 'message' => 'One or more trader codes are invalid'], 404);
            }

            // Create user_trader
            $userTrader = UserTrader::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
            ]);

            // Associate each trader with this user trader
            foreach ($traders as $trader) {
                $trader->user_trader_id = $userTrader->id;
                $trader->save();
            }

            $userTrader = UserTrader::with('trader')->find($userTrader->id);


            //dd($userTrader->trader);

            // Send mail if email was provided
            if ($userTrader->email) {
                Mail::to($userTrader->email)->send(new MasterTraderAccountCreated($userTrader));
            }

            return response()->json([
                'success' => true,
                'message' => 'User Trader added and associated with traders successfully!',
                'data' => $userTrader,
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating user trader: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred while processing the request.',
                'error' => $e->getMessage(), // optional: remove in production
            ], 500);
        }
    }



}
