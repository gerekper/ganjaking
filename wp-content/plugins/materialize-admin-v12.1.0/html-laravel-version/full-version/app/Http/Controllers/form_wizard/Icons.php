<?php

namespace App\Http\Controllers\form_wizard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Icons extends Controller
{
  public function index()
  {
    return view('content.form-wizard.form-wizard-icons');
  }
}
