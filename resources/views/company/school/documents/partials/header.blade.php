
@php
    $schoolDocument = $schoolDocument ?? [];
@endphp

<div class="school-document-header">

    <div class="school-document-logo">
        @if(!empty($schoolDocument['logo_url']))
            <img
                src="{{ $schoolDocument['logo_url'] }}"
                alt="Logo de l'établissement"
            >
        @endif
    </div>

    <div class="school-document-identity">
        <h2>
            {{ $schoolDocument['name'] ?? 'Établissement scolaire' }}
        </h2>

        @if(!empty($schoolDocument['motto']))
            <p class="school-document-motto">
                {{ $schoolDocument['motto'] }}
            </p>
        @endif

        <p>
            {{ collect([
                $schoolDocument['address'] ?? null,
                $schoolDocument['city'] ?? null,
                $schoolDocument['region'] ?? null,
                $schoolDocument['country'] ?? null,
            ])->filter()->implode(' — ') }}
        </p>

        @if(!empty($schoolDocument['phone']))
            <p>Tél. : {{ $schoolDocument['phone'] }}</p>
        @endif

        @if(!empty($schoolDocument['email']))
            <p>Email : {{ $schoolDocument['email'] }}</p>
        @endif

        @if(!empty($schoolDocument['authorization_number']))
            <p>
                Autorisation :
                {{ $schoolDocument['authorization_number'] }}
            </p>
        @endif
    </div>

</div>
