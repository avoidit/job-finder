<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Posting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('dashboard', [
            'queue' => Posting::doesntHave('application')
                ->orderByDesc('score')
                ->limit(20)
                ->get(),
            'pipeline' => Application::with('posting')
                ->where('status', '!=', 'queued')
                ->orderByDesc('updated_at')
                ->get()
                ->groupBy('status'),
            'appliedThisWeek' => Application::where('applied_at', '>=', now()->startOfWeek())->count(),
        ]);
    }

    public function setStatus(Request $request, Posting $posting): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:'.implode(',', Application::STATUSES),
        ]);

        $application = Application::firstOrNew(['posting_id' => $posting->id]);
        $application->status = $validated['status'];

        if ($validated['status'] === 'applied' && ! $application->applied_at) {
            $application->applied_at = now();
        }

        $application->save();

        return redirect()->route('dashboard');
    }
}
