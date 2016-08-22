<?php

namespace App\Http\Controllers;

use App\Models\Repository;
use Illuminate\Support\Facades\Artisan;

class HookController extends BaseController
{
    public function index(Repository $repository)
    {
        logger()->info('GitHub event received');
        $signature = 'sha1=' . hash_hmac('sha1', file_get_contents('php://input'), env('GITHUB_SECRET'), false);

        if ($signature == request()->header('X-Hub-Signature')) {
            logger()->info('GitHub event signature matched');
            $payload = request()->json();
            $event   = request()->header('X-GitHub-Event');

            $package = $repository->where('name', $payload->get('repository')['name'])->first();

            if (in_array($event, ['release', 'push'])) {
                logger()->info('GitHub event is ' . $event);

                logger()->info('Processing add repo');
                Artisan::call('docs:add-repo', ['name' => $package->name, 'icon' => $package->icon]);
                logger()->info('done');

                logger()->info('Processing get docs');
                Artisan::call('docs:get-docs', ['name' => $package->name]);
                logger()->info('done');

                logger()->info('GitHub event processed.');
            } else {
                logger()->error('GitHub event ' . $event . ' is not in accepted types [release, push]');
            }
        } else {
            logger()->error('GitHub signature did not match');
        }
    }
}
