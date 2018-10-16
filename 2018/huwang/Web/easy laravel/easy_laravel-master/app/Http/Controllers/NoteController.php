<?php

namespace App\Http\Controllers;

use Auth;
use DB;
use App\Note;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Note $note)
    {
        $username = Auth::user()->name;
        $notes = DB::select("SELECT * FROM `notes` WHERE `author`='{$username}'");
        return view('note', compact('notes'));
    }
}
