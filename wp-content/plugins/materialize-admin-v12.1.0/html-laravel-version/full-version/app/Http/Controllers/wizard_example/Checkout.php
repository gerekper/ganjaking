<?php

namespace App\Http\Controllers\wizard_example;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Checkout extends Controller
{
  public function index()
  {
    return view('content.wizard-example.wizard-ex-checkout');
  }
}
