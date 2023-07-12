<?php

namespace App\Http\Controllers\maps;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Leaflet extends Controller
{
  public function index()
  {
    return view('content.maps.maps-leaflet');
  }
}
