<?php

namespace App\Http\Controllers\layouts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ContentNavSidebar extends Controller
{
  public function index()
  {
    return view('content.layouts-example.layouts-content-navbar-with-sidebar');
  }
}
