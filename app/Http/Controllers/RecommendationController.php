<?php

namespace App\Http\Controllers;

use App\Recommendation;
use Illuminate\Http\Request;

class RecommendationController extends Controller
{
    public function __invoke()
    {
        return Recommendation::all();
    }
}