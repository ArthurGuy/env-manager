<?php

namespace App\Http\Controllers;

use App\AccessLog;
use Auth;
use Validator;
use App\Sites;
use Illuminate\Http\Request;

use App\Http\Requests;

class SitesController extends Controller
{

    public function index(Request $request)
    {
        $sites = Sites::all();
        return view('sites.index', ['sites' => $sites]);
    }

    public function show(Request $request, $id)
    {
        $site = Sites::findOrFail($id);

        $site->recordViewedBy(Auth::id());
        
        AccessLog::recordAccess($site->id, 'view', Auth::id());

        $accessLog = AccessLog::where('site_id', $site->id)->orderBy('created_at', 'desc')->take(50)->get();

        return view('sites.show', ['site' => $site, 'accessLog' => $accessLog]);
    }

    public function update(Request $request, $id)
    {
        $site = Sites::findOrFail($id);

        $this->validate($request, [
            'name' => 'required',
            'env'  => 'required'
        ]);

        $site->edit($request->get('name'), $request->get('env'), Auth::id());

        AccessLog::recordAccess($site->id, 'edit', Auth::id());

        $request->session()->put('status', 'Saved');
        return redirect()->route('sites.show', $site->id);
    }

    public function store(Request $request)
    {
        /*
        $this->validate($request, [
            'name' => 'required',
        ]);
        */

        $validator = Validator::make($request->all(), [
            'name' => 'required|min:5|max:255',
        ]);
        if ($validator->fails()) {
            return redirect('sites')
                ->withInput()
                ->withErrors($validator);
        }

        $site = Sites::recordNew($request->get('name'), Auth::id());

        AccessLog::recordAccess($site->id, 'create', Auth::id());

        return redirect()->route('sites');
    }
}
