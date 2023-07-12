<?php

namespace App\Http\Controllers\form_elements;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Extras extends Controller
{
  public function index()
  {
    return view('content.form-elements.forms-extras');
  }
}
