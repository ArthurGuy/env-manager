<?php

namespace App\Http\Controllers;

use Rollbar\Rollbar;
use Rollbar\Payload\Level;
use Illuminate\Http\Request;

class GitHubWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = json_decode($request->getContent(), true);
        if (isset($payload['pull_request']) && $payload['action'] == 'closed' && $payload['pull_request']['merged']) {
            // PR has just been merged
            Rollbar::log(Level::info(), 'Github PR has been merged', $payload);
        }
    }
}
