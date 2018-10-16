<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FlagController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function showFlag()
    {
        $flag = file_get_contents('/th1s1s_F14g_2333333');
        return view('auth.flag')->with('flag', $flag);
    }
}
