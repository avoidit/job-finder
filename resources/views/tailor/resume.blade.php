# {!! $profile['name'] !!} — {!! $profile['title'] !!}

{!! $profile['contact'] !!}

{!! $profile['summary'] !!}

## Technical Skills

@foreach ($profile['skills'] as $group => $skills)
- **{!! $group !!}:** {!! $skills !!}
@endforeach

## Work Experience

@foreach ($profile['jobs'] as $job)
### {!! $job['company'] !!} — {!! $job['role'] !!} | {!! $job['location'] !!} | {!! $job['dates'] !!}

@if ($job['note'])
_{!! $job['note'] !!}_

@endif
@foreach ($job['bullets'] as $bullet)
- {!! $bullet['text'] !!}
@endforeach

@endforeach
## Education

{!! $profile['education'] !!}
