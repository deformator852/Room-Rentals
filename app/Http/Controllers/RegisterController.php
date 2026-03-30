<?php

namespace App\Http\Controllers;

class RegisterController extends Controller
{
    public function index()
    {
        return view('pages.auth.register');
    }
}
