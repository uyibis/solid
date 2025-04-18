<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserTrader; // assuming you have a UserTrader model

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Fetch user trader data along with the count of associated traders
        $userTraders = UserTrader::withCount('trader')->withCount('slaves')->get();  // Fetch count of related traders (Master IDs)

        return view('home', compact('userTraders'));
    }

    // Method to toggle the status (block/unblock)
    public function toggleStatus($id)
    {
        $userTrader = UserTrader::findOrFail($id);
        $userTrader->status = !$userTrader->status; // Toggle status (assuming status is a boolean)
        $userTrader->save();

        return redirect()->route('home')->with('status', 'Status updated successfully!');
    }
}
