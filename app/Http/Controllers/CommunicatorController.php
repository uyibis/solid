<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Position;
use App\Models\SlaveTrader;
use App\Models\Trade;
use App\Models\Trader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\TraderActivityLog;
use App\Events\NewTradeEvent;
use Illuminate\Support\Str;
use Pusher\Pusher;


class CommunicatorController extends Controller
{
    /**
     * Handles trade data posted by NinjaTrader strategy.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postTrade(Request $request)
    {
        try {
            // Validate the incoming request structure
            $validatedData = $request->validate([
                'PendingOrders' => 'required|array',
                'PendingOrders.*.OrderId' => 'required|string|max:255',
                'PendingOrders.*.Instrument' => 'required|string|max:255',
                'PendingOrders.*.OrderType' => 'required|string|max:50',
                'PendingOrders.*.OrderState' => 'required|string|max:50',
                'PendingOrders.*.Quantity' => 'required|integer',
                'PendingOrders.*.LimitPrice' => 'required|numeric',
                'PendingOrders.*.StopPrice' => 'required|numeric',

                'OpenPositions' => 'required|array',
                'OpenPositions.*.Instrument' => 'required|string|max:255',
                'OpenPositions.*.Quantity' => 'required|integer',
                'OpenPositions.*.AveragePrice' => 'required|numeric',
                'OpenPositions.*.PositionType' => 'required|string|in:Buy,Sell',
                'OpenPositions.*.StopLoss' => 'nullable|numeric',
                'OpenPositions.*.TakeProfit' => 'nullable|numeric',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Validation failed',
                'error'   => $e->getMessage(),
            ], 422);
        }

        try {
            $pendingOrders = [];
            $openPositions = [];

            // Save Pending Orders
            foreach ($validatedData['PendingOrders'] as $order) {
                $pendingOrders[] = Order::create([
                    'order_id' => $order['OrderId'],
                    'instrument' => $order['Instrument'],
                    'order_type' => $order['OrderType'],
                    'order_state' => $order['OrderState'],
                    'quantity' => $order['Quantity'],
                    'price' => $order['LimitPrice']!=0?$order['LimitPrice']:$order['StopPrice'],
                ]);
            }

            // Save Open Positions
            foreach ($validatedData['OpenPositions'] as $position) {
                $openPositions[] = Position::create([
                    'instrument' => $position['Instrument'],
                    'quantity' => $position['Quantity'],
                    'average_price' => $position['AveragePrice'],
                    'position_type' => $position['PositionType'],
                    'stop_loss' => $position['StopLoss'] ?? null,
                    'take_profit' => $position['TakeProfit'] ?? null,
                ]);
            }

            return response()->json([
                'message' => 'Trade data saved successfully.',
                'pending_orders' => $pendingOrders,
                'open_positions' => $openPositions,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to save trade data.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }



    public function exportMasterTrades()
    {
        $orders = Order::all();
        $positions = Position::all();

        $csvHeader = "Instrument,OrderType,OrderState,Quantity,Price,PositionType,StopLoss,TakeProfit\n";
        $csvBody = "";

        foreach ($orders as $order) {
            $csvBody .= "{$order->instrument},{$order->order_type},{$order->order_state},{$order->quantity},{$order->price},,,\n";
        }

        foreach ($positions as $position) {
            $csvBody .= "{$position->instrument},,,,{$position->quantity},{$position->position_type},{$position->stop_loss},{$position->take_profit}\n";
        }

        $csvData = $csvHeader . $csvBody;

        return response($csvData, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="master_trades.csv"');
    }

    public function recordOrder(Request $request)
    {
        $data = [
            'type' => $request->input('Type'),
            'master_id' => $request->input('MasterId'),
            'order_id' => $request->input('OrderId'),
            'instrument' => $request->input('Instrument'),
            'order_type' => $request->input('OrderType'),
            'quantity' => $request->input('Quantity'),
            'price' => $request->input('Price'),
            'status' => $request->input('Status'),
            'time' => $request->input('Time'),
        ];

        // Check if an order with the same order_id already exists
        $existingOrder = Order::where('order_id', $data['order_id'])->first();

        if ($existingOrder) {
            return response()->json(['message' => 'Duplicate order detected'], 409);
        }

        $order = Order::create($data);
        return response()->json($order, 201);
    }



    public function recordPosition(Request $request)
    {
        $data = [
            'type' => $request->input('Type'),
            'master_id' => $request->input('MasterId'),
            'instrument' => $request->input('Instrument'),
            'market_position' => $request->input('MarketPosition'),
            'quantity' => $request->input('Quantity'),
            'average_price' => $request->input('AveragePrice'),
            'unrealized_pnl' => $request->input('UnrealizedPnL'),
            'stop_loss' => $request->input('StopLoss'),
            'take_profit' => $request->input('TakeProfit'),
            'time' => $request->input('Time'),
        ];

        // ✅ Validate MasterId (trader code)
        if (!$this->isValidMasterTrader($data['master_id'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or inactive Master ID.',
            ], 400);
        }

        // Check if a record with the same instrument and master_id already exists
        $existingPosition = Position::where('master_id', $data['master_id'])
            ->where('instrument', $data['instrument'])
            ->first();

        if ($existingPosition) {
            // Update the quantity of the existing position
            $existingPosition->quantity = $data['quantity'];
            $existingPosition->save();

            return response()->json([
                'message' => 'Position updated successfully.',
                'position' => $existingPosition
            ], 200);
        }

        // Create new position
        $position = Position::create($data);
        return response()->json($position, 201);
    }

    protected function isValidMasterTrader($traderCode): bool
    {
        $trader = Trader::where('code', $traderCode)->with('userTrader')->first();

        return $trader && $trader->userTrader && $trader->userTrader->status === true;
    }

    public function deactivateInactiveSlaveTraders()
    {
        // Get the current time
        $currentTime = now();

        // Find all SlaveTraders where 'required' is more than 3 minutes ago
        $inactiveSlaveTraders = SlaveTrader::where('connection_status', true)
            ->where('updated_at', '<', $currentTime->subMinutes(3)) // Subtract 3 minutes
            ->get();

        // Loop through and set their connection_status to false
        foreach ($inactiveSlaveTraders as $slaveTrader) {
            $slaveTrader->connection_status = false;
            $slaveTrader->save();
        }

        // Return a response indicating how many records were updated
        return response()->json([
            'success' => true,
            'message' => count($inactiveSlaveTraders) . ' SlaveTrader(s) deactivated.',
        ]);
    }

    public function recordClosingPosition(Request $request){
        Log::alert('position',$request->all());
    }

    /*public function ping(Request $request){
         $masterId = $request->input('MasterId');

         // Find the trader by MasterId and update updated_at
         $trader = Trader::where('code', $masterId)->first();

         if ($trader) {
             $trader->touch(); // Updates the updated_at timestamp
             return response()->json(['message' => 'Trader updated successfully'], 200);
         } else {
             return response()->json(['message' => 'Trader not found'], 404);
         }
     }*/
    /*public function ping(Request $request)
    {
        $masterId = $request->input('MasterId');

        // Find the trader by MasterId
        $trader = Trader::where('code', $masterId)->first();

        if ($trader) {
            // Check if updated_at is more than 30 seconds ago
            if ($trader->updated_at < now()->subSeconds(30)) {
                // Remove all positions belonging to the master
                Position::where('master_id', $masterId)->delete();
            }

            // Update the updated_at timestamp
            $trader->touch();

            return response()->json(['message' => 'Trader updated successfully'], 200);
        } else {
            return response()->json(['message' => 'Trader not found'], 404);
        }
    }*/


    public function ping(Request $request)
    {
        $masterId = $request->input('MasterId');

        // Find the master log entry
        $masterLog = DB::table('master_logs')->where('master_id', $masterId)->first();

        if ($masterLog) {
            // Check if updated_at is more than 30 seconds ago
            /* if (strtotime($masterLog->updated_at) < now()->subSeconds(30)->timestamp) {
                 // Remove all positions belonging to the master
                 DB::table('positions')->where('master_id', $masterId)->delete();
             }

             // Update the updated_at timestamp
             DB::table('master_logs')->where('master_id', $masterId)->update(['updated_at' => now()]);*/

            DB::table('master_logs')->where('master_id', $masterId)->update([
                'status' => 'open',
                'updated_at' => now(),
            ]);

            return response()->json(['message' => 'Master log updated successfully'], 200);
        } else {
            // Create a new master log entry
            DB::table('master_logs')->insert([
                'master_id' => $masterId,
                'status' => 'open',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json(['message' => 'Master log created successfully'], 201);
        }
    }



    public function closeMasterStatus(Request $request)
    {
        Log::alert('Closing master status', $request->all());

        $masterId = $request->input('MasterId');

        if (!$masterId) {
            return response()->json(['message' => 'MasterId is required'], 400);
        }

        DB::beginTransaction();

        try {
            // Delete all positions belonging to the master
            Position::where('master_id', $masterId)->delete();

            // Update the master log status to 'closed'
            $updated = DB::table('master_logs')
                ->where('master_id', $masterId)
                ->update([
                    'status' => 'close',
                    'updated_at' => now(),
                ]);

            if ($updated) {
                DB::commit();
                return response()->json(['message' => 'Master status closed and positions deleted successfully'], 200);
            } else {
                DB::rollBack();
                return response()->json(['message' => 'Master log not found'], 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error closing master status: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while closing master status', 'error' => $e->getMessage()], 500);
        }
    }





    public function getRecentPositions(Request $request)
    {
        $masterId = $request->input('MasterId');
        $slaveId = $request->input('SlaveId'); // Nullable
        $excludedIds = $request->input('ExcludedIds', []); // Expecting an array of IDs

        $slaveValidation = $this->validateSlaveTrader($slaveId);
        if ($slaveValidation !== true) {
            return $slaveValidation; // Return the error response if validation fails
        }

        // Track if it's a new slave trader
        $isNewSlave = false;

        // Generate a random alphanumeric string if SlaveId is null
        if (!$slaveId) {
            $slaveId = Str::random(10); // Generates a random 10-character string
            $isNewSlave = true; // Flag to trigger the event
        }

        // Get the client's IP address
        $ipAddress = $request->ip();

        // Find the trader whose updated_at is within the last 2 minutes
        $trader = DB::table('master_logs')
            ->where('master_id', $masterId)
            ->where('status', 'open') // Ensure status is "open"
            ->first();

        if (!$trader) {
            return response()->json([
                'message' => 'No recent trader activity found',
                'slave_id' => $slaveId, // Include slaveId in response even if trader is not found
            ], 404);
        }

        $myMaster = DB::table('traders')->where('code',$masterId)->first();
        // Log the IP and Slave ID
        TraderActivityLog::create([
            'trader_id' => $myMaster->id,
            'slave_id' => $slaveId,
            'ip_address' => $ipAddress,
        ]);

        // Raise event if a new slave was created
        if ($isNewSlave) {
            /* Log::info("Dispatching NewTradeEvent", [
                 'master_id' => $trader->id,
                 'slave_id' => $slaveId,
                 'ip_address' => $ipAddress,
             ]);

             event(new NewTradeEvent([
                 'master_id' => $trader->id,
                 'slave_id' => $slaveId,
                 'ip_address' => $ipAddress,
                 'created_at' => now(),
             ]));*/
            if ($isNewSlave) {
                $this->notifyNewTrade($trader->id, $slaveId, $ipAddress);
            }
        }

        // Retrieve positions that belong to the master trader and are NOT in the excluded list
        $positions = Position::where('master_id', $masterId)
            ->whereNotIn('id', $excludedIds)
            ->get();

        /* return response()->json([
             'message' => 'Recent positions retrieved successfully',
             'slave_id' => $slaveId, // Send slaveId in response
             'positions' => $positions,
         ], 200);*/
        // Define CSV headers (added 'ID' column)
        // Define CSV headers (Added 'Slave ID' column)
        $csvHeaders = ['ID', 'Slave ID', 'Type', 'Instrument', 'Market Position', 'Quantity', 'Average Price', 'Unrealized PnL', 'Stop Loss', 'Take Profit', 'Status', 'Time'];

        // Create CSV output
        $csvData = implode(',', $csvHeaders) . "\n"; // Header row

        foreach ($positions as $position) {
            $csvData .= implode(',', [
                    $position->id,
                    $slaveId,  // Added Slave ID
                    $position->type,
                    $position->instrument,
                    $position->market_position,
                    $position->quantity,
                    $position->average_price,
                    $position->unrealized_pnl,
                    $position->stop_loss,
                    $position->take_profit,
                    $position->status,
                    $position->time->format('Y-m-d H:i:s')
                ]) . "\n";
        }

        // Return CSV response
        return response($csvData, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="positions.csv"');
    }

    public function validateSlaveTrader($slaveId)
    {
        // Check if the SlaveTrader exists and the status is true
        $slaveTrader = SlaveTrader::where('code', $slaveId)->where('status', true)->first();

        // If no valid slave trader is found, return a response indicating failure
        if (!$slaveTrader) {
            return response()->json([
                'success' => false,
                'message' => 'The provided SlaveTrader does not exist or is inactive.',
            ], 404);
        }

        // If valid, set the connection_status to true and save the record
        $slaveTrader->connection_status = true;
        $slaveTrader->save();

        // Return true to indicate validation success
        return true;
    }


    /**
     * Private method to send Pusher notification for a new trade.
     */
    private function notifyNewTrade($masterId, $slaveId, $ipAddress)
    {
        return;
        try {
            $options = [
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'useTLS' => true,
            ];

            $pusher = new Pusher(
                env('PUSHER_APP_KEY'),
                env('PUSHER_APP_SECRET'),
                env('PUSHER_APP_ID'),
                $options
            );

            $data = [
                'master_id' => $masterId,
                'slave_id' => $slaveId,
                'ip_address' => $ipAddress,
                'created_at' => now(),
            ];

            // Log before triggering the event
            Log::info("Triggering Pusher notification for new trade", $data);

            // Trigger the Pusher event
            $response = $pusher->trigger('trade-channel', 'new-trade', $data);

            if (!$response) {
                Log::error("Pusher notification failed for NewTradeEvent", $data);
            }
        } catch (\Exception $e) {
            Log::error("Pusher error: " . $e->getMessage());
        }
    }


    /* public function checkMasterStatus(Request $request)
     {
         $masterId = $request->input('MasterId');

         // Find the trader by MasterId
         $trader = Trader::where('code', $masterId)->first();

         if (!$trader) {
             return response('close', 404);
         }

         // Check if updated_at is more than 3 minutes ago
         return $trader->updated_at < now()->subSeconds(20) ? 'close' : 'open';
     }*/

    public function checkMasterStatus(Request $request)
    {
        $masterId = $request->input('MasterId');

        // Find the master log entry
        $masterLog = DB::table('master_logs')
            ->where('master_id', $masterId)
            ->first();


        if (!$masterLog) {
            return response('close', 404);
        }

        try {
            if( $masterLog->status !== 'open'){
                Position::where('master_id', $masterId)->delete();
            }
        } catch (\Exception $e) {
            Log::error("error from close position" . $e->getMessage());
        }


        // Check if the master status is "open" and updated within the last 20 seconds
        // return $masterLog;
        return ($masterLog->status === 'open')
            ? 'open'
            : 'close';
    }

    function createTrader()
    {
        return Trader::create();
    }

    public function removeMasterTrader($masterId)
    {
        // Find and delete the master trader from the master_logs table
        $deleted = DB::table('master_logs')->where('master_id', $masterId)->delete();

        if ($deleted) {
            return response()->json(['message' => 'Master trader removed successfully'], 200);
        } else {
            return response()->json(['message' => 'Master trader not found'], 404);
        }
    }
}
