<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TradePortalController extends Controller
{
    public function dashboard()
    {
        return view('trade.dashboard');
    }

    public function projects()
    {
        return view('trade.projects');
    }

    public function quotes()
    {
        return view('trade.quotes');
    }

    public function samples()
    {
        return view('trade.samples');
    }

    public function orders()
    {
        return view('trade.orders');
    }

    public function account()
    {
        return view('trade.account');
    }
}
