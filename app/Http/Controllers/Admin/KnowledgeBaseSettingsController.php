<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KnowledgeBaseSettingsController extends Controller
{
    /**
     * Display the knowledge base settings page
     */
    public function index()
    {
        $user = Auth::user();
        
        return view('admin.knowledge-base-settings', compact('user'));
    }
}
