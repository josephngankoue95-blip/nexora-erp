@extends('layouts.ecommerce')
@section('title', 'Détails de la livraison')
@section('content')
<div class="delivery-page">
    <header class="delivery-heading"><div><span class="delivery-eyebrow">SUIVI LOGISTIQUE</span><h1>{{ $delivery->reference }}</h1><p>Commande #{{ $delivery->order_id }} · Créée le {{ $delivery->created_at?->format('d/m/Y à H:i') }}</p></div><div class="delivery-head-actions"><a class="delivery-btn delivery-btn-light" href="{{ route('company.ecommerce.deliveries.index') }}">← Liste</a><a class="delivery-btn delivery-btn-primary" href="{{ route('company.ecommerce.deliveries.edit', $delivery) }}">Modifier</a></div></header>
    @if(session('success'))<div class="delivery-alert success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="delivery-alert error">@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>@endif
    <div class="delivery-detail-grid">
        <section class="delivery-card"><div class="delivery-card-head"><h2>Informations du destinataire</h2><span class="delivery-status status-{{ $delivery->status }}">{{ $delivery->status_label }}</span></div>
            <dl class="delivery-details">
                <div><dt>Destinataire</dt><dd>{{ $delivery->recipient_name }}</dd></div><div><dt>Téléphone</dt><dd>{{ $delivery->recipient_phone }}</dd></div>
                <div><dt>E-mail</dt><dd>{{ $delivery->recipient_email ?: '—' }}</dd></div><div><dt>Adresse</dt><dd>{{ $delivery->address_line }}</dd></div>
                <div><dt>Ville / zone</dt><dd>{{ $delivery->city }}{{ $delivery->zone ? ' — '.$delivery->zone : '' }}</dd></div><div><dt>Frais</dt><dd>{{ number_format((float)$delivery->delivery_fee, 0, ',', ' ') }} FCFA</dd></div>
                <div><dt>Livreur</dt><dd>{{ $delivery->carrier_name ?: 'Non assigné' }}</dd></div><div><dt>Téléphone livreur</dt><dd>{{ $delivery->carrier_phone ?: '—' }}</dd></div>
                <div><dt>Date prévue</dt><dd>{{ $delivery->scheduled_at?->format('d/m/Y') ?: 'Non définie' }}</dd></div><div><dt>Expédiée le</dt><dd>{{ $delivery->shipped_at?->format('d/m/Y H:i') ?: '—' }}</dd></div>
                <div><dt>Livrée le</dt><dd>{{ $delivery->delivered_at?->format('d/m/Y H:i') ?: '—' }}</dd></div><div class="full"><dt>Notes</dt><dd>{{ $delivery->notes ?: 'Aucune note.' }}</dd></div>
            </dl>
        </section>
        <section class="delivery-card"><h2>Mettre à jour le suivi</h2>
            <form method="POST" action="{{ route('company.ecommerce.deliveries.status', $delivery) }}">
                @csrf @method('PATCH')
                <div class="delivery-field"><label for="status">Nouveau statut</label><select id="status" name="status">@foreach($statuses as $key => $label)<option value="{{ $key }}" @selected($delivery->status === $key)>{{ $label }}</option>@endforeach</select></div>
                <div class="delivery-field" style="margin-top:14px"><label for="notes">Note de suivi</label><textarea id="notes" name="notes" rows="4">{{ old('notes', $delivery->notes) }}</textarea></div>
                <button class="delivery-btn delivery-btn-primary" style="margin-top:15px" type="submit">Enregistrer le statut</button>
            </form>
            @if($delivery->status !== 'delivered')
            <form method="POST" action="{{ route('company.ecommerce.deliveries.destroy', $delivery) }}" onsubmit="return confirm('Supprimer cette livraison ?')">
                @csrf @method('DELETE')<button class="delivery-btn delivery-btn-danger" type="submit">Supprimer la livraison</button>
            </form>
            @endif
        </section>
    </div>
</div>
<style>
.delivery-page{color:#182235}.delivery-heading{display:flex;justify-content:space-between;align-items:center;gap:18px;margin-bottom:22px}.delivery-eyebrow{font-size:11px;letter-spacing:1.3px;font-weight:800;color:#315bea}.delivery-heading h1{margin:5px 0;font-size:27px}.delivery-heading p{margin:0;color:#748096;font-size:13px}.delivery-head-actions{display:flex;gap:8px}.delivery-btn{display:inline-flex;justify-content:center;align-items:center;padding:10px 14px;border:1px solid #dce3ee;border-radius:9px;background:#fff;color:#263449;text-decoration:none;font-size:13px;font-weight:700;cursor:pointer}.delivery-btn-primary{background:#315bea;color:white;border-color:#315bea}.delivery-btn-light{background:#f7f9fc}.delivery-btn-danger{margin-top:18px;color:#b91c1c;border-color:#fecaca;background:#fff7f7}.delivery-alert{padding:12px;border-radius:9px;margin-bottom:14px}.delivery-alert.success{background:#dcfce7;color:#166534}.delivery-alert.error{background:#fee2e2;color:#991b1b}.delivery-detail-grid{display:grid;grid-template-columns:minmax(0,1.5fr) minmax(280px,1fr);gap:17px}.delivery-card{background:#fff;border:1px solid #e5eaf2;border-radius:13px;padding:22px}.delivery-card h2{font-size:17px;margin:0 0 20px}.delivery-card-head{display:flex;align-items:center;justify-content:space-between;gap:10px}.delivery-details{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px;margin:0}.delivery-details .full{grid-column:1/-1}.delivery-details dt{font-size:11px;color:#748096;margin-bottom:5px}.delivery-details dd{margin:0;font-size:13px;font-weight:700;overflow-wrap:anywhere}.delivery-status{display:inline-block;border-radius:20px;padding:5px 9px;font-size:11px;font-weight:800;background:#f1f5f9;color:#475569}.status-delivered{background:#dcfce7;color:#15803d}.status-shipped{background:#dbeafe;color:#1d4ed8}.status-preparing{background:#fef3c7;color:#92400e}.status-failed,.status-cancelled{background:#fee2e2;color:#b91c1c}.delivery-field{display:flex;flex-direction:column;gap:7px}.delivery-field label{font-size:12px;font-weight:800;color:#4a566a}.delivery-field select,.delivery-field textarea{width:100%;box-sizing:border-box;border:1px solid #dce3ee;border-radius:8px;padding:11px 12px;font:inherit;font-size:13px}.delivery-field textarea{resize:vertical}.delivery-btn-danger{display:inline-flex}@media(max-width:850px){.delivery-detail-grid{grid-template-columns:1fr}}@media(max-width:600px){.delivery-heading{align-items:flex-start;flex-direction:column}.delivery-card{padding:15px}.delivery-details{grid-template-columns:1fr}.delivery-details .full{grid-column:auto}}
</style>
@endsection
