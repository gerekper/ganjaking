<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InvoiceList extends Controller
{
  public function index()
  {
    return view('content.apps.app-invoice-list');
  }
}
