<?php

namespace App\Http\Controllers\layouts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Helpers\Helpers;

class Horizontal extends Controller
{
  public function index()
  {

    $pageConfigs = ['myLayout' => 'horizontal'];

    return view('content.dashboard.dashboards-analytics',['pageConfigs'=> $pageConfigs]);
  }
}
