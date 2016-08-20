<?php

namespace App\Http\Controllers;

use App\Models\Repository;
use GrahamCampbell\GitHub\GitHubManager;
use Illuminate\Support\Facades\File;

class HomeController extends BaseController
{
    public function index()
    {
        //
    }

    public function test(GitHubManager $github)
    {
        $repos = Repository::all();

        $this->setViewData(compact('repos'));
        //$docs = collector($github->api('repo')->contents()->show('NukaCode', 'core', 'docs'));
        //
        //if (count($docs) > 0) {
        //    $docs = $docs->map(function ($doc) use ($github) {
        //        $files = collect($github->api('repo')->contents()->show('NukaCode', 'core', $doc['path']))
        //            ->map(function ($file) use ($github) {
        //                $markdown = new \ParsedownExtra;
        //                return collect([
        //                    'name'    => $file['name'],
        //                    'content' => $markdown->text(file_get_contents($file['download_url'])),
        //                ]);
        //            });
        //
        //        return collect([
        //            'name'     => $doc['name'],
        //            'chapters' => $files,
        //        ]);
        //    });
        //}
        //
        //$this->setViewData(compact('docs'));
    }
}
