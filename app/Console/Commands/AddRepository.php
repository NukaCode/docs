<?php

namespace App\Console\Commands;

use App\Models\Repository;
use GrahamCampbell\GitHub\GitHubManager;
use Illuminate\Console\Command;

class AddRepository extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'docs:add-repo {name} {icon : Font Awesome icon (part after fa-)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds a new repository to the database.';

    /**
     * @var \GrahamCampbell\GitHub\GitHubManager
     */
    private $github;

    /**
     * @var string
     */
    private $packageNamespace;

    /**
     * Create a new command instance.
     */
    public function __construct(GitHubManager $github)
    {
        parent::__construct();

        $this->github           = $github;
        $this->packageNamespace = env('PACKAGE_NAMESPACE');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $data = $this->getRepositoryDetails();

        $tagData = $this->getRepositoryVersions($data);

        $this->updateDatabase($data, $tagData);

        $this->comment('Finished!');
    }

    /**
     * @return array
     */
    private function getRepositoryDetails()
    {
        $data = [
            'name' => $this->argument('name'),
            'icon' => $this->argument('icon'),
        ];

        // Get Details
        $this->comment('Gathering details from github...');
        $details = $this->github->repositories()->show($this->packageNamespace, $data['name']);

        $data['description']   = $details['description'];
        $data['github_url']    = $details['html_url'];
        $data['git_url']       = $details['git_url'];
        $data['packagist_url'] = 'https://packagist.org/packages'. $this->$this->packageNamespace .'/' . $data['name'];

        return $data;
    }

    /**
     * @param $data
     *
     * @return array
     */
    private function getRepositoryVersions($data)
    {
        $tags = $this->github->repositories()->tags($this->packageNamespace, $data['name']);
        $this->comment(count($tags) . ' tags found.  Searching for latest tags per minor version...');

        $commits = $this->github->repositories()->commits()->all($this->packageNamespace, $data['name'], ['sha' => 'master']);

        $docs = collect($this->github->repositories()->contents()->show($this->packageNamespace, $data['name']))
            ->filter(function ($directory) {
                return $directory['path'] === 'docs';
            })
            ->first();

        $tagData = [
            'master' => [
                'name'           => 'master',
                'latest_release' => 'master',
                'sha'            => $docs['sha'],
                'commit_hash'    => head($commits)['sha'],
            ],
        ];

        $bar = $this->output->createProgressBar(count($tags));

        foreach ($tags as $tag) {
            $docs = collect($this->github->repositories()->contents()->show($this->packageNamespace, $data['name'], null, $tag['commit']['sha']))
                ->filter(function ($directory) {
                    return $directory['path'] === 'docs';
                })
                ->first();

            if (! is_null($docs)) {
                $semantics    = explode('.', $tag['name']);
                $minorRelease = $semantics[0] . '.' . $semantics[1];

                if (array_key_exists($minorRelease, $tagData)) {
                    if ($semantics[2] > $tagData[$minorRelease]['patch']) {
                        $tagData[$minorRelease]['latest_release'] = $tag['name'];
                        $tagData[$minorRelease]['patch']          = $semantics[2];
                        $tagData[$minorRelease]['sha']            = $docs['sha'];
                        $tagData[$minorRelease]['commit_hash']    = $tag['commit']['sha'];
                    }
                } else {
                    $tagData[$minorRelease] = [
                        'name'           => $minorRelease,
                        'latest_release' => $tag['name'],
                        'patch'          => $semantics[2],
                        'sha'            => $docs['sha'],
                        'commit_hash'    => $tag['commit']['sha'],
                    ];
                }
            }

            $bar->advance();
        }

        $bar->finish();

        return $tagData;
    }

    /**
     * @param $data
     * @param $tagData
     */
    private function updateDatabase($data, $tagData)
    {
        $this->comment("\n" . 'Updating database...');

        $repo = Repository::updateOrCreate(
            array_only($data, 'name'),
            $data
        );

        collector($tagData)->map(function ($tag) use ($repo) {
            $repo->versions()->updateOrCreate(
                array_only($tag, ['repository_id', 'name']),
                $tag
            );
        });
    }
}
