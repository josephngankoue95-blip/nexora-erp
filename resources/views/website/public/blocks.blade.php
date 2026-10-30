
{{-- NEXORA WebStudio Premium — Rendu public --}}
{{-- Compatible avec les blocs existants et la médiathèque --}}

@foreach($builderBlocks as $block)

@php
    $t = $block['type'] ?? '';

    $d = is_array($block['data'] ?? null)
        ? $block['data']
        : [];

    /*
    |--------------------------------------------------------------------------
    | Validation des couleurs
    |--------------------------------------------------------------------------
    */
    $color = static function ($key, $fallback) use ($d) {
        $value = $d[$key] ?? '';

        return is_string($value)
            && preg_match('/^#[a-fA-F0-9]{6}$/D', $value)
                ? $value
                : $fallback;
    };

    /*
    |--------------------------------------------------------------------------
    | Validation des URLs
    |--------------------------------------------------------------------------
    */
    $safeUrl = static function ($value) {
        if (!is_string($value) || $value === '') {
            return null;
        }

        if (preg_match('/[[:cntrl:][:space:]]/', $value)) {
            return null;
        }

        if (strpbrk($value, '<>"\'`\\') !== false) {
            return null;
        }

        if (str_starts_with($value, '/')) {
            return str_starts_with($value, '//')
                ? null
                : $value;
        }

        $parts = parse_url($value);

        if (!is_array($parts)) {
            return null;
        }

        return in_array(
            strtolower($parts['scheme'] ?? ''),
            ['http', 'https'],
            true
        )
        && !empty($parts['host'])
        && !isset($parts['user'])
        && !isset($parts['pass'])
            ? $value
            : null;
    };

    /*
    |--------------------------------------------------------------------------
    | Animations
    |--------------------------------------------------------------------------
    */
    $animate = in_array(
        $d['animation'] ?? '',
        [
            'fade',
            'fade-up',
            'slide-up',
            'slide-left',
            'slide-right',
            'zoom',
            'zoom-out',
            'rotate'
        ],
        true
    ) ? $d['animation'] : '';

    $animationDuration = min(2500, max(200, (int) ($d['duration'] ?? 700)));
    $animationDelay = min(2000, max(0, (int) ($d['delay'] ?? 0)));
    $parallax = in_array($d['parallax'] ?? false, [true, 1, '1', 'true', 'oui', 'on'], true);
    $hover = in_array($d['hover'] ?? true, [true, 1, '1', 'true', 'oui', 'on'], true);


    /*
    |--------------------------------------------------------------------------
    | Paramètres carrousel
    |--------------------------------------------------------------------------
    */
    $isAutoplay = in_array(
        $d['autoplay'] ?? false,
        [true, 1, '1', 'true', 'oui', 'on'],
        true
    );

$interval = min(
    15000,
    max(1500, (int) ($d['interval'] ?? 4500))
);
@endphp

{{-- ==========================================================
     HERO / CALL TO ACTION
========================================================== --}}

@if(in_array($t, ['hero', 'cta'], true))

    @php
        $heroImage = $safeUrl($d['image'] ?? '');
    @endphp

    <section
        class="wbp-hero wbp-animate {{ $hover ? 'wbp-hover-enabled' : 'wbp-hover-disabled' }} {{ $heroImage ? 'has-image' : 'no-image' }}"
        data-animation="{{ $animate }}"
        data-duration="{{ $animationDuration }}"
        data-delay="{{ $animationDelay }}"
        style="
            --wbp-bg:{{ $color('background', '#142d50') }};
            --wbp-fg:{{ $color('color', '#ffffff') }};
            @if($heroImage)
                background-image:
                    linear-gradient(
                        90deg,
                        rgba(7,18,39,.78),
                        rgba(7,18,39,.18)
                    ),
                    url('{{ $heroImage }}');
            @endif
        "
    >
        <div class="ws-container wbp-hero-inner">

            <span class="wbp-eyebrow">
                {{ $company->name ?? 'Bienvenue' }}
            </span>

            <h1>{{ $d['title'] ?? '' }}</h1>

            <p>{{ $d['subtitle'] ?? '' }}</p>

            @if(
                $t === 'cta' &&
                $safeUrl($d['url'] ?? '')
            )
                <a
                    class="wbp-button"
                    href="{{ $safeUrl($d['url']) }}"
                >
                    {{ $d['label'] ?: 'Nous contacter' }}
                    <span aria-hidden="true">→</span>
                </a>
            @endif

        </div>
    </section>

{{-- ==========================================================
     CARROUSELS / BANNIÈRES / TÉMOIGNAGES
========================================================== --}}

@elseif(in_array(
    $t,
    ['carousel', 'banner_slider', 'testimonials'],
    true
))

    @php
        $slides = [];

        foreach (range(0, 2) as $i) {

            $imageKey = [
                'image', 'image2', 'image3'
            ][$i];

            $captionKey = [
                'caption', 'caption2', 'caption3'
            ][$i];

            $titleKey = [
                'title', 'title2', 'title3'
            ][$i];

            $textKey = [
                'left', 'right', 'third'
            ][$i];

            $authorKey = [
                'author', 'author2', 'author3'
            ][$i];

            $src = $safeUrl($d[$imageKey] ?? '');

            $headline = $t === 'banner_slider'
                ? ($d[$titleKey] ?? '')
                : ($d[$captionKey] ?? '');

            $quote = trim(
                (string) ($d[$textKey] ?? '')
            );

            if ($t === 'testimonials') {

                if ($quote !== '') {
                    $slides[] = [
                        'quote' => $quote,
                        'author' => $d[$authorKey] ?? '',
                    ];
                }

            } elseif (
                $src ||
                trim((string) $headline) !== ''
            ) {

                $slides[] = [
                    'src' => $src,
                    'headline' => $headline,
                    'subtitle' =>
                        $i === 0 &&
                        $t === 'banner_slider'
                            ? ($d['subtitle'] ?? '')
                            : '',
                ];
            }
        }
    @endphp

    <section
        class="wbp-section wbp-slider-wrap wbp-animate {{ $t === 'banner_slider' ? 'wbp-slider-wide' : '' }}"
        data-animation="{{ $animate }}"
        data-duration="{{ $animationDuration }}"
        data-delay="{{ $animationDelay }}"
    >

        {{-- Conteneur pleine largeur --}}
        <div class="wbp-full-container">

            @if(
                $t !== 'banner_slider' &&
                filled($d['title'] ?? '')
            )
                <div class="wbp-section-heading">

                    <span class="wbp-eyebrow">
                        À découvrir
                    </span>

                    <h2>{{ $d['title'] }}</h2>

                </div>
            @endif

            @if(count($slides))

                <div
                    class="wbp-slider {{ $t === 'testimonials' ? 'wbp-testimonials' : '' }}"
                    data-autoplay="{{ $isAutoplay ? 'true' : 'false' }}"
                    data-interval="{{ $interval }}"
                    aria-roledescription="carrousel"
                    aria-label="{{ $d['title'] ?? 'Carrousel' }}"
                >

                    @foreach($slides as $slide)

                        <article
                            class="wbp-slide {{ $loop->first ? 'is-active' : '' }} {{ !empty($slide['src']) ? 'has-image' : 'no-image' }}"
                            aria-hidden="{{ $loop->first ? 'false' : 'true' }}"
                        >

                            @if($t === 'testimonials')

                                <blockquote>

                                    <span
                                        class="wbp-quote-mark"
                                        aria-hidden="true"
                                    >“</span>

                                    <p>{{ $slide['quote'] }}</p>

                                    <footer>
                                        {{ $slide['author'] }}
                                    </footer>

                                </blockquote>

                            @else

                                @if($slide['src'])

                                    <img
                                        src="{{ $slide['src'] }}"
                                        alt="{{ $slide['headline'] ?: 'Image du carrousel' }}"
                                        loading="{{ $loop->first ? 'eager' : 'lazy' }}"
                                    >

                                @endif

                                <div class="wbp-slide-copy">

                                    <span class="wbp-eyebrow">
                                        {{ $company->name ?? 'Découvrez-nous' }}
                                    </span>

                                    <h3>
                                        {{ $slide['headline'] }}
                                    </h3>

                                    @if($slide['subtitle'])
                                        <p>
                                            {{ $slide['subtitle'] }}
                                        </p>
                                    @endif

                                </div>

                            @endif

                        </article>

                    @endforeach

                    @if(count($slides) > 1)

                        <button
                            type="button"
                            class="wbp-prev"
                            aria-label="Diapositive précédente"
                        >‹</button>

                        <button
                            type="button"
                            class="wbp-next"
                            aria-label="Diapositive suivante"
                        >›</button>

                        <div
                            class="wbp-dots"
                            aria-label="Navigation du carrousel"
                        ></div>

                    @endif

                </div>

            @else

                <div class="wbp-empty">

                    <h3>
                        {{ $d['title'] ?: 'Votre prochain visuel' }}
                    </h3>

                    <p>
                        Ajoutez des images depuis la
                        médiathèque WebStudio.
                    </p>

                </div>

            @endif

        </div>
    </section>

{{-- ==========================================================
     TITRE
========================================================== --}}

@elseif($t === 'heading')

    <section
        class="wbp-section wbp-animate wbp-full-container"
        data-animation="{{ $animate }}"
        data-duration="{{ $animationDuration }}"
        data-delay="{{ $animationDelay }}"
    >
        <h2 style="color:{{ $color('color', '#172033') }}">
            {{ $d['title'] ?? '' }}
        </h2>
    </section>

{{-- ==========================================================
     TEXTE
========================================================== --}}

@elseif($t === 'text')

    <section class="wbp-section wbp-full-container wbp-animate"
        data-animation="{{ $animate }}"
        data-duration="{{ $animationDuration }}"
        data-delay="{{ $animationDelay }}">

        <p
            class="wbp-body"
            style="color:{{ $color('color', '#172033') }}"
        >
            {{ $d['text'] ?? '' }}
        </p>

    </section>

{{-- ==========================================================
     IMAGE
========================================================== --}}

@elseif($t === 'image')

    @if($src = $safeUrl($d['image'] ?? ''))

        <section class="wbp-section wbp-full-container wbp-animate"
        data-animation="{{ $animate }}"
        data-duration="{{ $animationDuration }}"
        data-delay="{{ $animationDelay }}">

            <div class="wbp-image-frame {{ $parallax ? 'wbp-parallax-image' : '' }}" @if($parallax) data-parallax="true" @endif>
            <img
                class="wbp-image {{ $hover ? 'wbp-hover-enabled' : 'wbp-hover-disabled' }}"
                src="{{ $src }}"
                alt="{{ $d['alt'] ?? '' }}"
                loading="lazy"
            >
            </div>

        </section>

    @endif

{{-- ==========================================================
     GALERIE
========================================================== --}}

@elseif($t === 'gallery')

    <section class="wbp-section wbp-full-container wbp-animate"
        data-animation="{{ $animate }}"
        data-duration="{{ $animationDuration }}"
        data-delay="{{ $animationDelay }}">

        <div class="wbp-gallery">

            @foreach(
                ['image', 'image2', 'image3'] as $key
            )

                @if($src = $safeUrl($d[$key] ?? ''))

                    <img
                        src="{{ $src }}"
                        alt="{{ $d['alt'] ?? 'Galerie' }}"
                        loading="lazy"
                    >

                @endif

            @endforeach

        </div>

    </section>

{{-- ==========================================================
     BOUTON
========================================================== --}}

@elseif($t === 'button')

    @if($url = $safeUrl($d['url'] ?? ''))

        <section class="wbp-section wbp-full-container wbp-animate"
        data-animation="{{ $animate }}"
        data-duration="{{ $animationDuration }}"
        data-delay="{{ $animationDelay }}">

            <a
                class="wbp-button"
                style="
                    background:{{ $color('background', '#2563eb') }};
                    color:{{ $color('color', '#ffffff') }};
                "
                href="{{ $url }}"
            >
                {{ $d['label'] ?? 'En savoir plus' }}
                →
            </a>

        </section>

    @endif

{{-- ==========================================================
     LABEL
========================================================== --}}

@elseif($t === 'label')

    <section class="wbp-section wbp-full-container wbp-animate"
        data-animation="{{ $animate }}"
        data-duration="{{ $animationDuration }}"
        data-delay="{{ $animationDelay }}">

        <span
            class="wbp-label"
            style="
                background:{{ $color('background', '#dbeafe') }};
                color:{{ $color('color', '#1e40af') }};
            "
        >
            {{ $d['label'] ?? '' }}
        </span>

    </section>

{{-- ==========================================================
     COLONNES
========================================================== --}}

@elseif($t === 'columns')

    <section class="wbp-section wbp-full-container wbp-animate"
        data-animation="{{ $animate }}"
        data-duration="{{ $animationDuration }}"
        data-delay="{{ $animationDelay }}">

        <div class="wbp-grid two">

            <div>{{ $d['left'] ?? '' }}</div>

            <div>{{ $d['right'] ?? '' }}</div>

        </div>

    </section>

{{-- ==========================================================
     CARTES ET STATISTIQUES
========================================================== --}}

@elseif(in_array($t, ['cards', 'stats'], true))

    <section class="wbp-section wbp-full-container wbp-animate"
        data-animation="{{ $animate }}"
        data-duration="{{ $animationDuration }}"
        data-delay="{{ $animationDelay }}">

        <div class="wbp-section-heading">

            <h2>{{ $d['title'] ?? '' }}</h2>

        </div>

        <div class="wbp-grid three" data-stagger="true">

            @foreach(
                ['left', 'right', 'third'] as $key
            )

                <div class="wbp-card wbp-animate {{ $hover ? 'wbp-hover-enabled' : 'wbp-hover-disabled' }}"
                    data-animation="{{ $animate ?: 'fade-up' }}"
                    data-duration="{{ $animationDuration }}"
                    data-delay="{{ min(2000, $animationDelay + $loop->index * 150) }}">
                    @if($t === 'stats' && preg_match('/^([0-9]+(?:[.,][0-9]+)?)\s*([+%]?)$/D', trim((string) ($d[$key] ?? '')), $matches))
                        <strong class="wbp-stat-number" data-wbp-counter="{{ str_replace(',', '.', $matches[1]) }}" data-suffix="{{ $matches[2] }}">{{ $d[$key] }}</strong>
                    @else
                        {{ $d[$key] ?? '' }}
                    @endif
                </div>

            @endforeach

        </div>

    </section>

{{-- ==========================================================
     CITATION
========================================================== --}}

@elseif($t === 'quote')

    <section class="wbp-section wbp-full-container wbp-animate"
        data-animation="{{ $animate }}"
        data-duration="{{ $animationDuration }}"
        data-delay="{{ $animationDelay }}">

        <blockquote class="wbp-quote">

            “{{ $d['text'] ?? '' }}”

            <footer>
                {{ $d['title'] ?? '' }}
            </footer>

        </blockquote>

    </section>

{{-- ==========================================================
     FAQ
========================================================== --}}

@elseif($t === 'faq')

    <section class="wbp-section wbp-full-container wbp-animate"
        data-animation="{{ $animate }}"
        data-duration="{{ $animationDuration }}"
        data-delay="{{ $animationDelay }}">

        <h2>
            {{ $d['title'] ?? 'Questions fréquentes' }}
        </h2>

        <details class="wbp-faq">

            <summary>
                {{ $d['left'] ?? '' }}
            </summary>

            <p>
                {{ $d['right'] ?? '' }}
            </p>

        </details>

    </section>

{{-- ==========================================================
     SÉPARATEUR
========================================================== --}}

@elseif($t === 'divider')

    <div class="wbp-section wbp-full-container wbp-animate"
        data-animation="{{ $animate }}"
        data-duration="{{ $animationDuration }}"
        data-delay="{{ $animationDelay }}">
        <hr>
    </div>

{{-- ==========================================================
     ESPACEMENT
========================================================== --}}

@elseif($t === 'spacer')

    <div
        aria-hidden="true"
        style="height:{{ min(400, max(8, (int) ($d['height'] ?? 48))) }}px"
    ></div>

{{-- ==========================================================
     VIDÉO
========================================================== --}}

@elseif($t === 'video')

    @php
        $url = $safeUrl($d['url'] ?? '');

        $host = $url
            ? strtolower(parse_url($url, PHP_URL_HOST) ?? '')
            : '';

        $path = $url
            ? trim(parse_url($url, PHP_URL_PATH) ?? '', '/')
            : '';

        $videoId = null;
        $embed = null;

        if (
            in_array(
                $host,
                ['youtu.be', 'www.youtu.be'],
                true
            ) &&
            preg_match('/^[A-Za-z0-9_-]{11}$/D', $path)
        ) {
            $videoId = $path;
        }

        if (
            in_array(
                $host,
                [
                    'youtube.com',
                    'www.youtube.com',
                    'm.youtube.com'
                ],
                true
            )
        ) {
            parse_str(
                parse_url($url, PHP_URL_QUERY) ?? '',
                $params
            );

            if (
                is_string($params['v'] ?? null) &&
                preg_match(
                    '/^[A-Za-z0-9_-]{11}$/D',
                    $params['v']
                )
            ) {
                $videoId = $params['v'];
            }
        }

        if ($videoId) {

            $embed =
                'https://www.youtube-nocookie.com/embed/' .
                $videoId;

        } elseif (
            in_array(
                $host,
                ['vimeo.com', 'www.vimeo.com'],
                true
            ) &&
            preg_match('/^\d+$/D', $path)
        ) {

            $embed =
                'https://player.vimeo.com/video/' .
                $path;
        }
    @endphp

    @if($embed)

        <section class="wbp-section wbp-full-container wbp-animate"
        data-animation="{{ $animate }}"
        data-duration="{{ $animationDuration }}"
        data-delay="{{ $animationDelay }}">

            <div class="wbp-video">

                <iframe
                    src="{{ $embed }}"
                    title="{{ $d['title'] ?? 'Vidéo' }}"
                    loading="lazy"
                    allow="accelerometer; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                    referrerpolicy="strict-origin-when-cross-origin"
                ></iframe>

            </div>

        </section>

    @endif

@endif

@endforeach
