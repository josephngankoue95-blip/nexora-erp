@extends('layouts.school')
@section('title', 'Seuils des distinctions')
@section('page-title', 'Seuils des distinctions')
@section('content')
@vite('resources/css/school-honors.css')
<div class="honors-wrap"><div class="honors-head"><h2>Paramètres du tableau d'honneur</h2><a class="honors-link" href="{{ route('company.school.honors.index') }}">Retour</a></div>
<p>Ces seuils s'appliquent à votre établissement. Les distinctions sont calculées selon le seuil le plus élevé atteint.</p>
<form class="honors-settings" method="POST" action="{{ route('company.school.honors.settings.update') }}">@csrf @method('PUT')
@foreach(['honor_min'=>"Tableau d'honneur",'encouragement_min'=>'Encouragements','congratulation_min'=>'Félicitations','excellence_min'=>'Excellence'] as $key=>$label)
<label>{{ $label }} — moyenne minimale /20<input type="number" name="{{ $key }}" min="0" max="20" step="0.01" required value="{{ old($key, $settings->$key) }}"></label>
@endforeach
<button type="submit">Enregistrer les seuils</button></form>
</div>
@endsection
