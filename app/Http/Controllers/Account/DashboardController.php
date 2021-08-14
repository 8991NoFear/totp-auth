<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;

use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    public function index(Request $request)
    {
        $userId = $request->session()->get('user.userId');
        $user = User::find($userId);
        return view('account.dashboard', compact('user'));
    }
}
