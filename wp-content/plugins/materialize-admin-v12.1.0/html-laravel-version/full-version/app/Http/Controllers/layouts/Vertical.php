<?php

namespace App\Http\Controllers\layouts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Vertical extends Controller
{
  public function index()
  {

    $pageConfigs = ['myLayout' => 'vertical'];

    return view('content.dashboard.dashboards-analytics', ['pageConfigs' => $pageConfigs]);
  }
}
