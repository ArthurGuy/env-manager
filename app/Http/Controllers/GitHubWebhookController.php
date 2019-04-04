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
            $repoName = $payload['repository']['name'];
            $orgName  = $payload['organization']['login'];
            $prUrl    = $payload['pull_request']['_links']['html']['href'];
            $prTitle  = $payload['pull_request']['title'];
            $mergedBy = $payload['pull_request']['merged_by']['login'];

            // PR has just been merged
            Rollbar::log(Level::info(), 'Github PR has been merged #' . $prNumber . ' Repo: ' . $repoName, $payload);
            $logged = true;

            $github = $this->createAuthenticatedGitHubConnection();

            $reviews  = collect($github->pullRequest()->reviews()->all($orgName, $repoName, $prNumber, ['per_page' => 100]));

            $approvals = $reviews->filter(function ($review) {
                return $review['state'] == 'APPROVED';
            });

            if ($orgName == env('REVIEW_GITHUB_ORG') && $repoName == env('REVIEW_GITHUB_REPO')) {
                // This is the org and repo we are monitoring

                if (env('REVIEW_SLACK_ENDPOINT')) {
                    $client = new \Maknz\Slack\Client(env('REVIEW_SLACK_ENDPOINT'), ['link_names' => true]);

                    if ($approvals->isEmpty()) {
                        //Rollbar::warning('PR #' . $prNumber . ' doesn\'t have any approvals');

                        $client->createMessage()->to('#' . env('REVIEW_SLACK_CHANNEL'))->attach([
                            'title'      => $prTitle,
                            'title_link' => $prUrl,
                            'color'      => 'danger',
                        ])->send('@channel PR #' . $prNumber . ' has been merged in by ' . $mergedBy . ' without being approved, an explanation needs to be provided.');
                    }
                }
            }
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
