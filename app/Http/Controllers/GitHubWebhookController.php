<?php

namespace App\Http\Controllers;

use Rollbar\Rollbar;
use Lcobucci\JWT\Builder;
use Rollbar\Payload\Level;
use Lcobucci\JWT\Signer\Key;
use Illuminate\Http\Request;
use Lcobucci\JWT\Signer\Rsa\Sha256;

class GitHubWebhookController extends Controller
{

    public function handle(Request $request)
    {
        $logged = false;
        $payload = json_decode($request->getContent(), true);
        if (isset($payload['pull_request']) && $payload['action'] == 'closed' && $payload['pull_request']['merged']) {
            $prNumber = $payload['number'];

            // PR has just been merged
            Rollbar::log(Level::info(), 'Github PR has been merged #' . $prNumber, $payload);
            $logged = true;

            $github = $this->createAuthenticatedGitHubConnection();

            $reviews = collect($github->pullRequest()->reviews()->all(env('REVIEW_GITHUB_ORG'), env('REVIEW_GITHUB_REPO'), $prNumber));

            $approvals = $reviews->filter(function ($review) {
                return $review['state'] == 'APPROVED';
            });

            if ($approvals->isEmpty()) {
                Rollbar::warning('PR #' . $prNumber . ' doesnt have any approvals');
            }

            Rollbar::log(Level::info(), 'Github PR reviews', $reviews->toArray());
        }

        return response()->json(['status' => 'ok', 'logged' => $logged]);
    }

    private function createAuthenticatedGitHubConnection()
    {
        $builder = new \Github\HttpClient\Builder();
        $github = new \Github\Client($builder, 'machine-man-preview');


        $privateKey = file_get_contents(base_path('github-private-key.pem'));
        $jwt = (new Builder)
            ->setIssuer(env('GITHUB_ISSUER_ID'))
            ->setIssuedAt(time())
            ->setExpiration(time() + 60)
            ->sign(new Sha256(),  new Key($privateKey))
            ->getToken();

        $github->authenticate($jwt, null, \Github\Client::AUTH_JWT);

        $token = $github->api('apps')->createInstallationToken(env('GITHUB_INTEGRATION_ID'));
        $github->authenticate($token['token'], null, \Github\Client::AUTH_HTTP_TOKEN);

        return $github;
    }
}
