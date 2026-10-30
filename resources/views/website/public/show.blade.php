@extends('website.public.layout')
@section('title', $page->title . ' — ' . $company->name)
@section('meta_description', $page->excerpt ?: $page->title)
@section('content')
@php
    $builderBlocks = is_array($page->builder_content) ? $page->builder_content : [];
    $hasBuilder = count($builderBlocks) > 0;
@endphp
@if($hasBuilder)
    @include('website.public.blocks', ['builderBlocks' => $builderBlocks])
@else
    <section class="ws-page-banner"><div class="ws-container">
        <a class="ws-breadcrumb" href="{{ route('website.public.index', ['company' => $company->slug]) }}">Accueil</a>
        <span aria-hidden="true"> / </span><span>{{ $page->title }}</span>
        <h1>{{ $page->title }}</h1>
        @if($page->excerpt)<p>{{ $page->excerpt }}</p>@endif
    </div></section>
    <article class="ws-container ws-content">
        @if(filled($page->content))
            <div class="ws-richtext">{!! \App\Support\WebsiteHtml::clean($page->content ?? '') !!}</div>
        @else
            <p class="ws-empty">Le contenu de cette page sera bientôt disponible.</p>
        @endif
    </article>
@endif
@endsection
