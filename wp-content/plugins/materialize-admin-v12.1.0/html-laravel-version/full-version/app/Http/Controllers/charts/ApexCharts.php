<?php

namespace App\Http\Controllers\charts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApexCharts extends Controller
{
  public function index()
  {
    return view('content.charts.charts-apex');
  }
}
