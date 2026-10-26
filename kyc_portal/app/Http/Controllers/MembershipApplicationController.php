<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMembershipApplicationRequest;
use App\Models\MembershipApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MembershipApplicationController extends Controller
{
    public function create(): View
    {
        return view('public.join');
    }

    public function store(StoreMembershipApplicationRequest $request): RedirectResponse
    {
        $application = MembershipApplication::create([
            ...$request->safe()->except(['privacy_agreement', 'website']),
            'reference' => $this->reference(),
            'privacy_agreed_at' => now(),
            'submitted_ip' => $request->ip(),
        ]);

        return redirect()
            ->route('public.join')
            ->with('application_reference', $application->reference);
    }

    private function reference(): string
    {
        do {
            $reference = 'KYC-'.now()->format('ymd').'-'.Str::upper(Str::random(6));
        } while (MembershipApplication::query()->where('reference', $reference)->exists());

        return $reference;
    }
}
