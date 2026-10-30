@extends('layouts.ecommerce')
@section('title', 'Livraisons')
@section('content')
<div class="delivery-page">
    <header class="delivery-heading">
        <div><span class="delivery-eyebrow">LOGISTIQUE</span><h1>Livraisons</h1><p>Préparez, expédiez et suivez les commandes de votre boutique.</p></div>
        <a class="delivery-btn delivery-btn-primary" href="{{ route('company.ecommerce.deliveries.create') }}">+ Nouvelle livraison</a>
    </header>
    @if(session('success'))<div class="delivery-alert success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="delivery-alert error">@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>@endif
    <div class="delivery-stats">
        <div class="delivery-stat"><span>Total livraisons</span><strong>{{ $stats['total'] }}</strong></div>
        <div class="delivery-stat"><span>À traiter</span><strong>{{ $stats['pending'] }}</strong></div>
        <div class="delivery-stat"><span>Expédiées</span><strong>{{ $stats['shipped'] }}</strong></div>
        <div class="delivery-stat"><span>Livrées</span><strong>{{ $stats['delivered'] }}</strong></div>
    </div>
    <form class="delivery-filters" method="GET" action="{{ route('company.ecommerce.deliveries.index') }}">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Référence, destinataire, téléphone, ville…">
        <select name="status"><option value="">Tous les statuts</option>@foreach($statuses as $key => $label)<option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>@endforeach</select>
        <button class="delivery-btn" type="submit">Filtrer</button>
        <a class="delivery-btn delivery-btn-light" href="{{ route('company.ecommerce.deliveries.index') }}">Réinitialiser</a>
    </form>
    <div class="delivery-table-wrap"><table class="delivery-table">
        <thead><tr><th>Référence</th><th>Destinataire</th><th>Destination</th><th>Frais</th><th>Statut</th><th>Créée le</th><th></th></tr></thead>
        <tbody>
        @forelse($deliveries as $delivery)
            <tr>
                <td><strong>{{ $delivery->reference }}</strong><small>Commande #{{ $delivery->order_id }}</small></td>
                <td>{{ $delivery->recipient_name }}<small>{{ $delivery->recipient_phone }}</small></td>
                <td>{{ $delivery->city }}<small>{{ $delivery->zone ?: $delivery->address_line }}</small></td>
                <td>{{ number_format((float)$delivery->delivery_fee, 0, ',', ' ') }} FCFA</td>
                <td><span class="delivery-status status-{{ $delivery->status }}">{{ $delivery->status_label }}</span></td>
                <td>{{ $delivery->created_at?->format('d/m/Y') }}</td>
                <td><a class="delivery-link" href="{{ route('company.ecommerce.deliveries.show', $delivery) }}">Détails →</a></td>
            </tr>
        @empty<tr><td colspan="7" class="delivery-empty">Aucune livraison. Créez-en une à partir d'une commande.</td></tr>@endforelse
        </tbody>
    </table></div>
    <div class="delivery-pagination">{{ $deliveries->links() }}</div>
</div>
<style>
.delivery-page{--dp:#315bea;--dt:#182235;--dm:#748096;color:var(--dt)}.delivery-heading{display:flex;justify-content:space-between;align-items:center;gap:18px;margin-bottom:24px}.delivery-eyebrow{font-size:11px;letter-spacing:1.3px;font-weight:800;color:var(--dp)}.delivery-heading h1{margin:5px 0;font-size:28px}.delivery-heading p{margin:0;color:var(--dm);font-size:13px}.delivery-btn{display:inline-flex;align-items:center;justify-content:center;border:1px solid #dce3ee;border-radius:9px;padding:10px 14px;background:#fff;color:#263449;text-decoration:none;font-weight:700;font-size:13px;cursor:pointer}.delivery-btn-primary{background:var(--dp);color:white;border-color:var(--dp)}.delivery-btn-light{background:#f7f9fc}.delivery-stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin-bottom:20px}.delivery-stat{background:#fff;border:1px solid #e5eaf2;border-radius:13px;padding:18px;box-shadow:0 4px 12px #17255408}.delivery-stat span{display:block;color:var(--dm);font-size:12px;margin-bottom:8px}.delivery-stat strong{font-size:27px}.delivery-filters{display:flex;gap:10px;flex-wrap:wrap;background:#fff;border:1px solid #e5eaf2;border-radius:12px;padding:14px;margin-bottom:16px}.delivery-filters input{flex:1;min-width:220px}.delivery-filters input,.delivery-filters select{border:1px solid #dce3ee;border-radius:8px;padding:11px 12px;background:#fff;font:inherit;font-size:13px}.delivery-table-wrap{overflow-x:auto;background:#fff;border:1px solid #e5eaf2;border-radius:13px}.delivery-table{width:100%;border-collapse:collapse;min-width:850px}.delivery-table th{text-align:left;background:#f8faff;color:#77839a;font-size:10px;text-transform:uppercase;letter-spacing:.6px;padding:14px}.delivery-table td{padding:15px 14px;border-top:1px solid #edf0f5;font-size:13px}.delivery-table small{display:block;color:var(--dm);font-size:11px;margin-top:5px}.delivery-status{display:inline-block;border-radius:20px;padding:5px 9px;font-size:11px;font-weight:800;background:#f1f5f9;color:#475569}.status-delivered{background:#dcfce7;color:#15803d}.status-shipped{background:#dbeafe;color:#1d4ed8}.status-preparing{background:#fef3c7;color:#92400e}.status-failed,.status-cancelled{background:#fee2e2;color:#b91c1c}.delivery-link{color:var(--dp);font-weight:700;text-decoration:none;white-space:nowrap}.delivery-empty{text-align:center;color:var(--dm);padding:36px!important}.delivery-pagination{margin-top:18px}.delivery-alert{padding:12px 15px;border-radius:9px;margin-bottom:15px;font-size:13px}.delivery-alert.success{background:#dcfce7;color:#166534}.delivery-alert.error{background:#fee2e2;color:#991b1b}@media(max-width:900px){.delivery-stats{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:600px){.delivery-heading{align-items:flex-start;flex-direction:column}.delivery-heading h1{font-size:23px}.delivery-stats{gap:9px}.delivery-stat{padding:13px}.delivery-stat strong{font-size:23px}.delivery-filters>*{width:100%;min-width:0!important}}
</style>
@endsection
