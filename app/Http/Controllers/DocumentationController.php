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
        $version = $this->repository->where('name', $name)->first()
                                    ->versions()->with('chapters.sections')->where('name', $version)->first();

        $this->setViewData(compact('version'));
    }

    public function section($version, $name, $section)
    {
        $version = $this->repository->where('name', $name)->first()
                                    ->versions()->with('chapters.sections')->where('name', $version)->first();

        $section = Section::whereIn('chapter_id', $version->chapters->id->toArray())->where('name', $section)->first();

        $content = $this->markdown->text(file_get_contents($section->path));

        $this->setViewData(compact('version', 'content'));
    }
}
