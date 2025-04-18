<?php
namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\SlaveTrader;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $slaves = SlaveTrader::with('order')->get(); // assuming 'order' is a relationship, or remove ->with if not defined
        return view('customer.index', compact('slaves'));
    }

    public function toggleStatus(Request $request, $id)
    {
        $slave = SlaveTrader::findOrFail($id);
        $slave->status = !$slave->status;
        $slave->save();

        return response()->json(['message' => 'Status updated', 'status' => $slave->status]);
    }
}
