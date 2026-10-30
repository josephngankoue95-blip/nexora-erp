
@extends('layouts.website')

@section('title', 'Médiathèque')
@section('page-title', 'Médiathèque')
@section('page-description', 'Images de votre site vitrine')

@section('content')

<div class="wm-page">

    {{-- MESSAGES DE SUCCÈS --}}
    @if(session('success'))
        <p class="wm-alert" role="status">
            {{ session('success') }}
        </p>
    @endif

    {{-- ERREURS --}}
    @if($errors->any())
        <div class="wm-error" role="alert">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    {{-- IMPORTATION --}}
    <form
        class="wm-upload"
        action="{{ route('company.website.media.store') }}"
        method="POST"
        enctype="multipart/form-data"
    >
        @csrf

        <label for="image">
            Importer une image
            (JPG, PNG, WebP ou GIF, 5 Mo maximum)
        </label>

        <input
            id="image"
            name="image"
            type="file"
            accept="image/jpeg,image/png,image/webp,image/gif"
            required
        >

        <button type="submit">
            Ajouter l'image
        </button>
    </form>

    {{-- GALERIE --}}
    <div class="wm-grid">

        @forelse($media as $item)

            @php
                $imageUrl = \Illuminate\Support\Facades\Storage::disk('public')
                    ->url($item->path);

                $imageUrl = url($imageUrl);
            @endphp

            <article class="wm-card">

                {{-- IMAGE --}}
                <img
                    src="{{ $imageUrl }}"
                    alt="{{ $item->original_name ?? 'Image du site' }}"
                    class="wm-image"
                    loading="lazy"
                >

                <div class="wm-info">

                    <strong title="{{ $item->original_name }}">
                        {{ $item->original_name }}
                    </strong>

                    <small>
                        {{ number_format(
                            ($item->size ?? 0) / 1024,
                            0,
                            ',',
                            ' '
                        ) }} Ko
                    </small>

                    {{-- URL PUBLIQUE --}}
                    <label for="media-url-{{ $item->id }}">
                        Adresse publique
                    </label>

                    <input
                        id="media-url-{{ $item->id }}"
                        aria-label="Adresse de l'image"
                        type="text"
                        readonly
                        value="{{ $imageUrl }}"
                        onclick="this.select()"
                    >

                    {{-- SUPPRESSION --}}
                    <form
                        action="{{ route(
                            'company.website.media.destroy',
                            $item->id
                        ) }}"
                        method="POST"
                        onsubmit="return confirm('Supprimer cette image ? Elle peut être utilisée sur une page publiée.')"
                    >
                        @csrf
                        @method('DELETE')

                        <button
                            class="wm-delete"
                            type="submit"
                        >
                            Supprimer
                        </button>
                    </form>

                </div>

            </article>

        @empty

            <p>Aucune image pour le moment.</p>

        @endforelse

    </div>

    {{-- PAGINATION --}}
    @if($media->hasPages())
        <div class="wm-pagination">
            {{ $media->links() }}
        </div>
    @endif

</div>

@endsection

@push('styles')
    @vite('resources/css/website-media.css')
@endpush
