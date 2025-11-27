<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class PulauController extends Controller
{
    public function index()
    {
        return view('pulau.index');
    }

    public function about()
    {
        return view('pulau.about');
    }

    public function packages()
    {
        return view('pulau.packages');
    }

    public function contact()
    {
        return view('pulau.contact');
    }

    public function gallery()
    {
        return view('pulau.gallery');
    }

    public function itinerary()
    {
        return view('pulau.itinerary');
    }

    public function faq()
    {
        return view('pulau.faq');
    }
}
