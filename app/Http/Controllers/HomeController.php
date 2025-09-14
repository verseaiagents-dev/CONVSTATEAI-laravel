<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plan;

class HomeController extends Controller
{
    /**
     * Show the homepage
     */
    public function index()
    {
        // Aktif planları getir (trial hariç, sadece satış için)
        $plans = Plan::where('is_active', true)
            ->where('billing_cycle', '!=', 'trial')
            ->orderBy('price', 'asc')
            ->get();

        return view('index', compact('plans'));
    }
}