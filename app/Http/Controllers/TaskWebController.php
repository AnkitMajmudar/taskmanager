<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TaskWebController extends Controller
{
    
    public function index()
    {
        
        return view('tasks.index');
    }
}
