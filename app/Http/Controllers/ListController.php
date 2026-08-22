<?php

namespace App\Http\Controllers;

class ListController extends Controller
{
    public function index()
    {
        return view('lists.index');
    }

    public function show($list_id)
    {
        return view('lists.show', compact('list_id'));
    }
}
