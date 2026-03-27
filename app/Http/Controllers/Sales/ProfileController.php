<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Entities\Master\Customer;
use Carbon\Carbon;
use Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::guard('superuser')->user();

        $today = Carbon::today();

        $totalProspect = Customer::whereDate('created_at', $today)
            ->where('created_by', $user->id)
            ->count();

        return view('sales.profile.index', compact('user', 'totalProspect'));
    }
}