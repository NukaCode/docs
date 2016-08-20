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
     * Create a new command instance.
     */
    public function __construct(GitHubManager $github)
    {
        parent::__construct();

        $this->github = $github;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $data = [
            'name' => $this->argument('name'),
            'icon' => $this->argument('icon'),
        ];

        // Get Details
        $details = $this->github->repositories()->show('nukacode', $data['name']);

        $data['description']   = $details['description'];
        $data['github_url']    = $details['html_url'];
        $data['git_url']       = $details['git_url'];
        $data['packagist_url'] = 'https://packagist.org/packages/nukacode/' . $data['name'];

        // Get releases
        $releases = $this->github->repositories()->releases()->all('nukacode', $data['name']);

        $releaseData = [];

        foreach ($releases as $release) {
            $semantics    = explode('.', $release['tag_name']);
            $minorRelease = $semantics[0] . '.' . $semantics[1];

            if (array_key_exists($minorRelease, $releaseData)) {
                if ($semantics[2] > $releaseData[$minorRelease]['patch']) {
                    $releaseData[$minorRelease]['latest_release'] = $release['tag_name'];
                    $releaseData[$minorRelease]['patch']          = $semantics[2];
                }
            } else {
                $releaseData[$minorRelease] = [
                    'name'           => $minorRelease,
                    'latest_release' => $release['tag_name'],
                    'patch'          => $semantics[2],
                ];
            }
        }

        $repo = Repository::create($data);

        collector($releaseData)->map(function ($release) use ($repo) {
            $repo->versions()->create($release);
        });
    }
}
