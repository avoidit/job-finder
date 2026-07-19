Dear {!! $posting->company ?? 'Hiring' !!} team,

I'm a full stack developer with 10+ years building production web applications, and I'm applying for the {!! $posting->title !!} role. Three things from my background that map directly to it:

@foreach ($profile['achievements'] as $achievement)
- I {!! $achievement['text'] !!}.
@endforeach

I build end-to-end — schema design, business logic, frontend — and I automate anything manual and recurring. I'd welcome the chance to talk about what you're building.

Best,
{!! $profile['name'] !!}
{!! $profile['contact'] !!}
