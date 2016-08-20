<?php

namespace App\Http\Controllers;

use Illuminate\Filesystem\Filesystem;

class DocumentationController extends BaseController
{
    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    private $filesystem;

    /**
     * @param \Illuminate\Filesystem\Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();

        $this->filesystem = $filesystem;
    }

    public function index($version, $name)
    {
        dd($this->filesystem->directories(base_path('resources/docs/'. $name .'/'. $version .'/docs')));
    }
}
