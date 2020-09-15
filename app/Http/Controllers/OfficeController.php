<?php

namespace App\Http\Controllers;

use App\Office;

class OfficeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function index()
    {
        return app('db')->select("SELECT * FROM classicmodels.offices");
    }


}
