<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>job-finder</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 2rem; max-width: 1100px; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 2rem; }
        th, td { text-align: left; padding: .4rem .6rem; border-bottom: 1px solid #ddd; font-size: .9rem; }
        th { background: #f5f5f5; }
        .score { font-weight: bold; }
        form { display: inline; }
        button { font-size: .75rem; padding: .15rem .4rem; cursor: pointer; }
        h2 { margin-top: 2rem; }
        .stat { font-size: 1.1rem; }
    </style>
</head>
<body>
    <h1>job-finder</h1>
    <p class="stat">Applied this week: <strong>{{ $appliedThisWeek }}</strong></p>

    <h2>Queue (top {{ $queue->count() }} by score)</h2>
    <table>
        <tr><th>Score</th><th>Source</th><th>Company</th><th>Title</th><th>Location</th><th>Actions</th></tr>
        @foreach ($queue as $posting)
            <tr>
                <td class="score">{{ $posting->score }}</td>
                <td>{{ $posting->source }}</td>
                <td>{{ $posting->company ?? '—' }}</td>
                <td><a href="{{ $posting->url }}" target="_blank" rel="noopener">{{ $posting->title }}</a></td>
                <td>{{ $posting->location ?? ($posting->remote ? 'remote' : '—') }}</td>
                <td>
                    <form method="post" action="{{ route('postings.status', $posting) }}">
                        @csrf
                        <input type="hidden" name="status" value="applied">
                        <button>applied</button>
                    </form>
                    <form method="post" action="{{ route('postings.status', $posting) }}">
                        @csrf
                        <input type="hidden" name="status" value="rejected">
                        <button>skip</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>

    <h2>Pipeline</h2>
    @forelse ($pipeline as $status => $applications)
        <h3>{{ ucfirst($status) }} ({{ $applications->count() }})</h3>
        <table>
            <tr><th>Company</th><th>Title</th><th>Applied</th><th>Move to</th></tr>
            @foreach ($applications as $application)
                <tr>
                    <td>{{ $application->posting->company ?? '—' }}</td>
                    <td><a href="{{ $application->posting->url }}" target="_blank" rel="noopener">{{ $application->posting->title }}</a></td>
                    <td>{{ $application->applied_at?->format('M j') ?? '—' }}</td>
                    <td>
                        @foreach (\App\Models\Application::STATUSES as $next)
                            @if ($next !== $application->status && $next !== 'queued')
                                <form method="post" action="{{ route('postings.status', $application->posting) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="{{ $next }}">
                                    <button>{{ $next }}</button>
                                </form>
                            @endif
                        @endforeach
                    </td>
                </tr>
            @endforeach
        </table>
    @empty
        <p>No applications yet.</p>
    @endforelse

    <p><small>Postings sourced from Larajobs, HN Who's Hiring, We Work Remotely, and <a href="https://remoteok.com">Remote OK</a>.</small></p>
</body>
</html>
