<x-filament-panels::page>
    @php
        $d = $this->decision;
    @endphp

    <div style="border:1px solid #e5e7eb; background:#fff; border-radius:0.25rem; padding:1rem; margin-bottom:1rem;">
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.75rem; font-size:0.875rem;">
            <div>
                <div style="font-weight:600; color:#6b7280; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.05em;">Decision ID</div>
                <div style="color:#111827; font-family:monospace; font-weight:500;">#{{ $d->id }}</div>
            </div>
            <div>
                <div style="font-weight:600; color:#6b7280; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.05em;">Category</div>
                <div style="color:#111827;">{{ $d->category_slug }}</div>
            </div>
            <div>
                <div style="font-weight:600; color:#6b7280; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.05em;">Granted</div>
                <span style="font-size:0.75rem; font-weight:500; background:{{ $d->granted ? '#f0fdf4' : '#fef2f2' }}; color:{{ $d->granted ? '#166534' : '#991b1b' }}; padding:0.125rem 0.5rem; border-radius:0.25rem;">{{ $d->granted ? 'Yes' : 'No' }}</span>
            </div>
            <div>
                <div style="font-weight:600; color:#6b7280; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.05em;">Version</div>
                <div style="color:#111827;">{{ $d->version }}</div>
            </div>
            <div>
                <div style="font-weight:600; color:#6b7280; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.05em;">Consented At</div>
                <div style="color:#111827;">{{ $d->consented_at?->format('M j, Y H:i:s') ?? '—' }}</div>
            </div>
            <div>
                <div style="font-weight:600; color:#6b7280; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.05em;">Created At</div>
                <div style="color:#111827;">{{ $d->created_at?->format('M j, Y H:i:s') ?? '—' }}</div>
            </div>
        </div>
    </div>

    <div style="border:1px solid #e5e7eb; background:#fff; border-radius:0.25rem; padding:1rem;">
        <div style="font-weight:600; color:#6b7280; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.5rem;">Identifier</div>
        <div style="display:grid; grid-template-columns:1fr; gap:0.75rem; font-size:0.875rem;">
            <div>
                <div style="font-weight:600; color:#6b7280; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.05em;">User ID</div>
                <div style="color:#111827;">{{ $d->user_id ?? '— (guest)' }}</div>
            </div>
            <div>
                <div style="font-weight:600; color:#6b7280; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.05em;">Guest Token</div>
                <div style="color:#111827; font-family:monospace; word-break:break-all;">{{ $d->guest_token ?? '—' }}</div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
