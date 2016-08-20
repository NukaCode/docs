@foreach ($version->chapters as $chapter)
  <h5>{{ $chapter->name }}</h5>
  <ul class="list-unstyled">
    @foreach ($chapter->sections as $section)
      <li>
        <a href="{{ route('docs.section', [$version->name, $version->repository->name, $section->name]) }}">
          {{ $section->name }}
        </a>
      </li>
    @endforeach
  </ul>
@endforeach
