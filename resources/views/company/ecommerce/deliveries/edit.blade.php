@extends('layouts.ecommerce')
@section('title', 'Modifier la livraison')
@section('content')
<div class="delivery-page">
    <header class="delivery-heading"><div><span class="delivery-eyebrow">LOGISTIQUE</span><h1>Modifier la livraison</h1><p>{{ $delivery->reference }}</p></div><a class="delivery-btn delivery-btn-light" href="{{ route('company.ecommerce.deliveries.show', $delivery) }}">← Retour</a></header>
    @if($errors->any())<div class="delivery-alert error">@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>@endif
    <form class="delivery-card delivery-form" method="POST" action="{{ route('company.ecommerce.deliveries.update', $delivery) }}">
        @csrf @method('PUT') @include('company.ecommerce.deliveries._form')
        <div class="delivery-actions"><a class="delivery-btn delivery-btn-light" href="{{ route('company.ecommerce.deliveries.show', $delivery) }}">Annuler</a><button class="delivery-btn delivery-btn-primary" type="submit">Enregistrer</button></div>
    </form>
</div>
<style>
.delivery-page{color:#182235}.delivery-heading{display:flex;justify-content:space-between;align-items:center;gap:18px;margin-bottom:22px}.delivery-eyebrow{font-size:11px;letter-spacing:1.3px;font-weight:800;color:#315bea}.delivery-heading h1{margin:5px 0;font-size:27px}.delivery-heading p{margin:0;color:#748096;font-size:13px}.delivery-btn{display:inline-flex;justify-content:center;align-items:center;padding:10px 14px;border:1px solid #dce3ee;border-radius:9px;background:#fff;color:#263449;text-decoration:none;font-size:13px;font-weight:700;cursor:pointer}.delivery-btn-primary{background:#315bea;color:white;border-color:#315bea}.delivery-btn-light{background:#f7f9fc}.delivery-card{background:white;border:1px solid #e5eaf2;border-radius:13px;padding:22px}.delivery-form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:17px}.delivery-field{display:flex;flex-direction:column;gap:7px}.delivery-field.full{grid-column:1/-1}.delivery-field label{font-size:12px;font-weight:800;color:#4a566a}.delivery-field input,.delivery-field select,.delivery-field textarea{width:100%;box-sizing:border-box;border:1px solid #dce3ee;border-radius:8px;padding:11px 12px;background:#fff;font:inherit;font-size:13px}.delivery-field textarea{min-height:95px;resize:vertical}.delivery-actions{display:flex;justify-content:flex-end;gap:10px;border-top:1px solid #edf0f5;padding-top:18px;margin-top:22px}.delivery-alert{padding:12px;border-radius:9px;margin-bottom:14px;background:#fee2e2;color:#991b1b}@media(max-width:650px){.delivery-heading{align-items:flex-start;flex-direction:column}.delivery-card{padding:15px}.delivery-form-grid{grid-template-columns:1fr}.delivery-field.full{grid-column:auto}.delivery-actions>*{flex:1}}
</style>
@endsection
