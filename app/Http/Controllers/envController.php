<?php

namespace App\Http\Controllers;

use App\Sites;
use App\AccessLog;
use App\Http\Requests;
use Illuminate\Http\Request;

class envController extends Controller
{
    public function get(Request $request, $name)
    {
        $site = Sites::findByName($name);

        AccessLog::recordAccess($site->id, 'access', null, $request->getClientIp());

        return response(base64_decode($site->env))->header('content-type', 'text/plain');
    }
}
