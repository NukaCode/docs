<?php

namespace App\Http\Controllers;

use App\Models\Repository;
use App\Models\Section;
use App\Models\Version;
use Illuminate\Filesystem\Filesystem;

class DocumentationController extends BaseController
{
    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    private $filesystem;

    /**
     * @var \App\Models\Version
     */
    private $version;

    /**
     * @var \App\Models\Repository
     */
    private $repository;

    /**
     * @var \ParsedownExtra
     */
    private $markdown;

    /**
     * @param \Illuminate\Filesystem\Filesystem $filesystem
     * @param \App\Models\Version               $version
     * @param \App\Models\Repository            $repository
     * @param \ParsedownExtra                   $markdown
     */
    public function __construct(Filesystem $filesystem, Version $version, Repository $repository, \ParsedownExtra $markdown)
    {
        parent::__construct();

        $this->filesystem = $filesystem;
        $this->version    = $version;
        $this->repository = $repository;
        $this->markdown   = $markdown;
    }

    public function index($version, $name)
    {
        $version = $this->repository->byName($name)
                                    ->first()
                                    ->versions()
                                    ->with('chapters')
                                    ->byName($version)
                                    ->first();
        $section = Section::whereIn('chapter_id', $version->chapters->id->toArray())
                          ->first();

        return redirect(route('docs.section', [$version->name, $name, $section->name]));
    }

    public function section($version, $name, $section)
    {
        $allVersions = $this->repository->byName($name)
                                        ->first()
                                        ->versions()
                                        ->has('chapters')
                                        ->with('chapters.sections')
                                        ->get();
        $versions    = $allVersions->name;
        $version     = $allVersions->getWhereFirst('name', $version);

        $section = Section::whereIn('chapter_id', $version->chapters->id->toArray())
                          ->byName($section)
                          ->first();

        $content = $this->markdown->text(file_get_contents($section->path));

        $this->setViewData(compact('version', 'versions', 'content', 'section'));
    }
}
