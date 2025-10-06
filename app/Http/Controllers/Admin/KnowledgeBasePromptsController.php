<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KnowledgeBasePromptsController extends Controller
{
    /**
     * Display the knowledge base prompts management page
     */
    public function index()
    {
        $user = Auth::user();
        
        return view('admin.knowledge-base-prompts', compact('user'));
    }
}
