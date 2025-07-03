<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Payment;
use App\Models\Look;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'total_users'       => User::count(),
            'total_payments'    => Payment::sum('amount'),
            'total_looks'       => Look::count(),
            'today_payments'    => Payment::whereDate('created_at', Carbon::today())->sum('amount'),
        ];

        return view('admin.dashboard', compact('data'));
    }
}
