<div class="dropdown">
  <button class="btn btn-default dropdown-toggle" type="button" id="versionDropDown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
    {{ $version->name }}
    <span class="caret"></span>
  </button>
  <ul class="dropdown-menu" aria-labelledby="versionDropDown">
    @foreach ($versions as $availableVersion)
      <li>
        <a href="{{ route('docs.section', [$availableVersion, $version->repository->name, $section->name]) }}">
          {{ $availableVersion }}
        </a>
      </li>
    @endforeach
  </ul>
</div>

@foreach ($version->chapters as $chapter)
  <h5>{{ $chapter->name }}</h5>
  <ul class="list-unstyled">
    @foreach ($chapter->sections as $chapterSection)
      <li>
        <a href="{{ route('docs.section', [$version->name, $version->repository->name, $chapterSection->name]) }}">
          {{ $chapterSection->name }}
        </a>
      </li>
    @endforeach
  </ul>
@endforeach
