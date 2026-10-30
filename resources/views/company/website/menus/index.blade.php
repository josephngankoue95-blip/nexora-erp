@extends('layouts.website')
@section('title', 'Menu du site')
@section('page-title', 'Menu du site')
@section('page-description', 'Personnalisez les liens de navigation de votre site vitrine')
@section('content')
<div class="wm-menu-page">
    @if(session('success'))<p class="wm-menu-success">{{ session('success') }}</p>@endif
    @if($errors->any())<div class="wm-menu-errors">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
    <section class="wm-menu-panel">
        <h2>Ajouter un lien</h2>
        <form method="POST" action="{{ route('company.website.menus.store') }}" class="wm-menu-form">
            @csrf
            <label>Libellé <input name="label" required maxlength="100" value="{{ old('label') }}" placeholder="Ex. : Nos services"></label>
            <label>Type de lien
                <select name="type" class="wm-menu-type" data-form="create">
                    <option value="page">Page WebStudio</option>
                    <option value="external" @selected(old('type') === 'external')>URL externe</option>
                </select>
            </label>
            <label data-page-field>Page
                <select name="website_page_id"><option value="">Choisir une page</option>@foreach($pages as $page)<option value="{{ $page->id }}" @selected(old('website_page_id') == $page->id)>{{ $page->title }} ({{ $page->status }})</option>@endforeach</select>
            </label>
            <label data-url-field>URL externe <input name="url" value="{{ old('url') }}" placeholder="https://exemple.com"></label>
            <label class="wm-menu-check"><input type="checkbox" name="is_visible" value="1" @checked(old('is_visible', true))> Afficher dans le menu</label>
            <button type="submit">Ajouter au menu</button>
        </form>
    </section>
    <section class="wm-menu-panel">
        <h2>Éléments du menu</h2>
        @forelse($items as $item)
            <div class="wm-menu-item">
                <form method="POST" action="{{ route('company.website.menus.update', $item->id) }}" class="wm-menu-form">
                    @csrf @method('PUT')
                    <label>Libellé <input name="label" value="{{ $item->label }}" required maxlength="100"></label>
                    <label>Type
                        <select name="type" class="wm-menu-type">
                            <option value="page" @selected($item->type === 'page')>Page WebStudio</option>
                            <option value="external" @selected($item->type === 'external')>URL externe</option>
                        </select>
                    </label>
                    <label data-page-field>Page
                        <select name="website_page_id"><option value="">Choisir une page</option>@foreach($pages as $page)<option value="{{ $page->id }}" @selected($item->website_page_id == $page->id)>{{ $page->title }} ({{ $page->status }})</option>@endforeach</select>
                    </label>
                    <label data-url-field>URL externe <input name="url" value="{{ $item->url }}" placeholder="https://exemple.com"></label>
                    <label class="wm-menu-check"><input type="checkbox" name="is_visible" value="1" @checked($item->is_visible)> Visible</label>
                    <button type="submit">Enregistrer</button>
                </form>
                <div class="wm-menu-actions">
                    <form method="POST" action="{{ route('company.website.menus.move', [$item->id, 'up']) }}">@csrf<button type="submit">↑ Monter</button></form>
                    <form method="POST" action="{{ route('company.website.menus.move', [$item->id, 'down']) }}">@csrf<button type="submit">↓ Descendre</button></form>
                    <form method="POST" action="{{ route('company.website.menus.destroy', $item->id) }}" onsubmit="return confirm('Supprimer ce lien du menu ?')">@csrf @method('DELETE')<button type="submit" class="danger">Supprimer</button></form>
                </div>
            </div>
        @empty
            <p>Votre menu est vide. Ajoutez un premier lien ci-dessus.</p>
        @endforelse
    </section>
</div>
@endsection
@push('styles')
<style>
.wm-menu-page{max-width:1100px;margin:auto;display:grid;gap:22px}.wm-menu-panel{background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:24px}.wm-menu-panel h2{margin:0 0 18px;font-size:20px}.wm-menu-form{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:15px}.wm-menu-form label{display:grid;gap:6px;font-weight:600;font-size:13px;color:#334155}.wm-menu-form input:not([type=checkbox]),.wm-menu-form select{width:100%;min-width:0;padding:11px;border:1px solid #cbd5e1;border-radius:9px;font:inherit}.wm-menu-form button,.wm-menu-actions button{padding:10px 16px;border:0;border-radius:9px;background:#2563eb;color:#fff;cursor:pointer}.wm-menu-form>button{align-self:end}.wm-menu-check{display:flex!important;align-items:center;gap:8px!important}.wm-menu-item{padding:18px 0;border-top:1px solid #e2e8f0}.wm-menu-actions{display:flex;flex-wrap:wrap;gap:8px;margin-top:12px}.wm-menu-actions button{background:#475569}.wm-menu-actions button.danger{background:#dc2626}.wm-menu-success{color:#166534;background:#dcfce7;padding:12px;border-radius:9px}.wm-menu-errors{color:#991b1b;background:#fee2e2;padding:12px;border-radius:9px}@media(max-width:680px){.wm-menu-panel{padding:15px}.wm-menu-form{grid-template-columns:1fr}}
</style>
@endpush
@push('scripts')
<script>
document.querySelectorAll('.wm-menu-form').forEach(form=>{
    const type=form.querySelector('.wm-menu-type');
    const page=form.querySelector('[data-page-field]');
    const url=form.querySelector('[data-url-field]');
    function sync(){const external=type.value==='external';page.hidden=external;url.hidden=!external;page.querySelector('select').disabled=external;url.querySelector('input').disabled=!external;}
    type.addEventListener('change',sync);sync();
});
</script>
@endpush
