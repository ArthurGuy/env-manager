<?php

namespace App\Http\Controllers;

use Rollbar\Rollbar;
use Rollbar\Payload\Level;
use Illuminate\Http\Request;
use GrahamCampbell\GitHub\GitHubManager;

class GitHubWebhookController extends Controller
{
    protected $github;

    public function __construct(GitHubManager $github)
    {
        $this->github = $github;
    }

    public function handle(Request $request)
    {
        $logged = false;
        $payload = json_decode($request->getContent(), true);
        if (isset($payload['pull_request']) && $payload['action'] == 'closed' && $payload['pull_request']['merged']) {
            $prNumber = $payload['number'];

            // PR has just been merged
            Rollbar::log(Level::info(), 'Github PR has been merged #' . $prNumber, $payload);
            $logged = true;


            $reviews = $this->github->pullRequest()->reviews()->all(env('REVIEW_GITHUB_ORG'), env('REVIEW_GITHUB_REPO'), $prNumber);
            Rollbar::log(Level::info(), 'Github PR reviews', $reviews);
        }

        return response()->json(['status' => 'ok', 'logged' => $logged]);
    }
}
