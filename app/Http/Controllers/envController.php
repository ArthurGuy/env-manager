<?php

namespace App\Http\Controllers;

use App\Sites;

use App\Http\Requests;

class envController extends Controller
{
    public function get($name)
    {
        $site = Sites::findByName($name);
        return response(base64_decode($site->env))->header('content-type', 'text/plain');
    }
}
