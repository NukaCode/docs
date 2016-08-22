<?php

namespace App\Console\Commands;

use App\Models\Repository;
use App\Models\Version;
use GrahamCampbell\GitHub\GitHubManager;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;

class GetDocs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'docs:get-docs {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get a copy of the docs for a package.';

    private $sha = null;

    /**
     * @var \GrahamCampbell\GitHub\GitHubManager
     */
    private $github;

    /**
     * @var \App\Models\Repository
     */
    private $repository;

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    private $filesystem;

    /**
     * Create a new command instance.
     *
     * @param \GrahamCampbell\GitHub\GitHubManager $github
     * @param \App\Models\Repository               $repository
     * @param \Illuminate\Filesystem\Filesystem    $filesystem
     */
    public function __construct(GitHubManager $github, Repository $repository, Filesystem $filesystem)
    {
        parent::__construct();

        $this->github     = $github;
        $this->repository = $repository;
        $this->filesystem = $filesystem;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Exception
     */
    public function handle()
    {
        $name       = $this->argument('name');
        $repository = $this->repository->with('versions')->where('name', $name)->first();

        if (is_null($repository)) {
            throw new \Exception('Repository with name {' . $name . '} was not found.');
        }

        $this->downloadDocs($repository, $name);

        Version::all()->groupBy('sha')->each(function ($versionGroup) {
            $versionGroup->each(function ($version) use ($versionGroup) {
                $docsPath = $versionGroup->last()->path;

                $chapters = collect($this->filesystem->directories($docsPath))
                    ->map(function ($chapter) use ($docsPath) {
                        preg_match_all('/([\d]+)\-([\w\s]+)/', str_replace($docsPath . '/', '', $chapter), $matches);

                        if (isset($matches[2][0])) {
                            return [
                                'name'   => $matches[2][0],
                                'number' => (int)$matches[1][0],
                                'path'   => $chapter,
                            ];
                        }

                        return null;
                    })->filter();

                $versionGroup->each(function ($version) use ($chapters) {
                    $chapters->each(function ($chapter) use ($version) {
                        $chapter = $version->chapters()->updateOrCreate(['version_id' => $version->id, 'name' => $chapter['name']], $chapter);

                        $sections = collect($this->filesystem->files($chapter->path))
                            ->map(function ($section) use ($chapter) {
                                preg_match_all('/([\d]+)\-([\w\s]+)/', str_replace($chapter->path . '/', '', $section), $matches);

                                if (isset($matches[2][0])) {
                                    return [
                                        'name'   => $matches[2][0],
                                        'number' => (int)$matches[1][0],
                                        'path'   => $section,
                                    ];
                                }

                                return null;
                            })->filter();

                        $sections->each(function ($section) use ($chapter) {
                            $chapter->sections()->updateOrCreate(['chapter_id' => $chapter->id, 'name' => $section['name']], $section);
                        });
                    });
                });
            });
        });
    }

    /**
     * @param $repository
     * @param $name
     */
    private function downloadDocs($repository, $name)
    {
        $repository->versions
            ->sortBy('name')
            ->each(function ($version) use ($name, $repository) {
                $path = base_path('resources/docs/' . $name . '/' . $version->name);

                if ($version->sha !== $this->sha) {
                    $this->sha = $version->sha;

                    if (! $this->filesystem->isDirectory($path)) {
                        $this->filesystem->makeDirectory($path, 0755, true);
                    }

                    $commands = [
                        'cd ' . $path,
                        'git init',
                        'git remote add origin ' . $repository->git_url,
                        'git config core.sparsecheckout true',
                        'cp ' . base_path('build/sparse-checkout') . ' ' . $path . '/.git/info/sparse-checkout',
                        'git pull origin master',
                        'git checkout ' . $version->commit_hash,
                    ];

                    passthru(implode(';', $commands));
                }
            });
    }
}
