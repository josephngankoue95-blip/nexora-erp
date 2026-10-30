
<div class="school-document-signatures">

    <div class="school-document-signature">
        <strong>Le responsable pédagogique</strong>

        <span>
            {{ $schoolDocument['principal'] ?? '' }}
        </span>
    </div>

    <div class="school-document-signature">
        <strong>La direction de l'établissement</strong>

        @if(!empty($schoolDocument['stamp_url']))
            <img
                class="school-document-stamp"
                src="{{ $schoolDocument['stamp_url'] }}"
                alt="Cachet de l'établissement"
            >
        @endif

        <div>
            {{ $schoolDocument['director'] ?? '' }}
        </div>
    </div>

</div>
