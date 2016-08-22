<?php

namespace App\Http\Controllers;

use App\Models\Repository;
use Illuminate\Support\Facades\Artisan;

class HookController extends BaseController
{
    public $results = ['Event Log:'];

    public $level = 'info';

    public function index(Repository $repository)
    {
        $this->addResults('GitHub event received');

        $signature = 'sha1=' . hash_hmac('sha1', file_get_contents('php://input'), env('GITHUB_SECRET'), false);

        if ($signature == request()->header('X-Hub-Signature')) {
            $this->runArtisanCommands($repository);
        } else {
            $this->addError('GitHub signature did not match');
        }

        logger()->{$this->level}(implode("\n", $this->results));
    }

    private function addResults($message)
    {
        $this->results[] = $message;
    }

    private function addError($message)
    {
        $this->results[] = $message;
        $this->level     = 'error';
    }

    /**
     * Runs the artisan commands for the event repository.
     *
     * @param \App\Models\Repository $repository
     *
     * @return bool
     */
    private function runArtisanCommands(Repository $repository)
    {
        $this->addResults('GitHub event signature matched');
        $payload     = request()->json();
        $packageName = $payload->get('repository')['name'];
        $event       = request()->header('X-GitHub-Event');

        $repository = $repository->where('name', $packageName)->first();

        if (is_null($repository)) {
            $this->addError('No repository named ' . $packageName .' found in the system');
            return false;
        }

        $this->addResults('Repository set to ' . $repository->name);

        if (in_array($event, ['release', 'push'])) {
            try {
                $this->addResults('Running add-repo');
                Artisan::call('docs:add-repo', ['name' => $repository->name, 'icon' => $repository->icon]);
                $this->addResults('Running get-docs');
                Artisan::call('docs:get-docs', ['name' => $repository->name]);
            } catch (\Exception $e) {
                $this->addResults('Artisan commands failed.');
            }

            $this->addResults('GitHub event processed.');
        } else {
            $this->addError('GitHub event ' . $event . ' is not in accepted types [release, push]');
        }
    }
}
