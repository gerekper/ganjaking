<?php

namespace App\Http\Controllers\cards;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CardGamifications extends Controller
{
  public function index()
  {
    return view('content.cards.cards-gamifications');
  }
}
