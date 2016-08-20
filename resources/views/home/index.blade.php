<div class="container-fluid">
  @foreach ($repos->chunk(4) as $repoGroup)
    <div class="row">
      @foreach ($repoGroup as $repo)
        <div class="col-lg-3">
          <div class="panel">
            <div class="panel-body text-black">
              <div class="clearfix">
                <div class="pull-left">
                  <strong><i class="fa fa-fw fa-{{ $repo->icon }}"></i>&nbsp;{{ $repo->name }}</strong>
                </div>
                <div class="pull-right">
                  v{{ $repo->latest_release }}
                </div>
              </div>
              {{ $repo->description }}
            </div>
            <div class="panel-footer white clearfix">
              <div class="pull-left">
                <a class="text-gray" href="{{ $repo->github_url }}" target="_blank">
                  <i class="fa fa-fw fa-github"></i>&nbsp;Github
                </a>
              </div>
              <div class="pull-right">
                <a class="text-gray" href="{{ $repo->packagist_url }}" target="_blank">
                  Packagist&nbsp;<i class="fa fa-fw fa-archive"></i>
                </a>
              </div>
            </div>
            <div class="panel-footer text-center white">
              <a class="text-gray" href="{{ route('docs.index', [$repo->latest_version, $repo->name]) }}">Documentation</a>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  @endforeach
</div>
