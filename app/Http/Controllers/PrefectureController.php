<?php

namespace App\Http\Controllers;

use App\Prefecture;
use Illuminate\Http\Request;

class PrefectureController extends Controller
{
		public function __invoke()
    {
        return Prefecture::all();
    }
}
