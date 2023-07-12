<?php

namespace App\Http\Controllers\form_elements;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomOptions extends Controller
{
  public function index()
  {
    return view('content.form-elements.forms-custom-options');
  }
}
