@extends('layouts.school')
@section('title', $student->exists ? 'Modifier un élève' : 'Nouvel élève')
@section('page-title', $student->exists ? 'Modifier un élève' : 'Nouvel élève')
@section('page-description', 'Informations personnelles, photo et coordonnées du responsable')
@section('content')
@php
    $photoUrl = $student->photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($student->photo)
        ? \Illuminate\Support\Facades\Storage::disk('public')->url($student->photo) : null;
@endphp
<div class="student-form-page">
    <div class="student-form-heading">
        <div><span class="student-form-eyebrow">ADMINISTRATION SCOLAIRE</span><h2>{{ $student->exists ? 'Modifier la fiche élève' : 'Enregistrer un nouvel élève' }}</h2><p>Renseignez l'identité, la photo et les coordonnées du responsable.</p></div>
        <a class="student-form-btn student-form-outline" href="{{ route('company.school.students.index') }}">← Retour à la liste</a>
    </div>
    @if($errors->any())
        <div class="student-form-errors" role="alert"><strong>Veuillez corriger les champs suivants :</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    <form method="POST" enctype="multipart/form-data" action="{{ $student->exists ? route('company.school.students.update', $student) : route('company.school.students.store') }}">
        @csrf
        @if($student->exists) @method('PUT') @endif
        <div class="student-form-card">
            <div class="student-form-card-header"><span class="student-form-step">01</span><div><h3>Photo d'identité</h3><p>Cette photo apparaîtra automatiquement sur la carte scolaire recto.</p></div></div>
            <div class="student-photo-row">
                <div class="student-photo-preview" id="photoPreview" aria-label="Aperçu de la photo">
                    <img id="photoImage" src="{{ $photoUrl ?? '' }}" alt="Photo de l'élève" @if(!$photoUrl) hidden @endif>
                    <span id="photoPlaceholder" @if($photoUrl) hidden @endif>PHOTO</span>
                </div>
                <div class="student-photo-fields">
                    <label for="photo">Choisir une photo (JPG, PNG, WebP)</label>
                    <input id="photo" name="photo" type="file" accept="image/jpeg,image/png,image/webp">
                    <small>2 Mo maximum. Photo portrait, visage visible, fond clair recommandé.</small>
                    @if($student->exists && $photoUrl)
                        <label class="student-remove-photo"><input type="checkbox" id="remove_photo" name="remove_photo" value="1" @checked(old('remove_photo'))> Supprimer la photo actuelle</label>
                    @endif
                    @error('photo')<small class="student-field-error">{{ $message }}</small>@enderror
                </div>
            </div>
        </div>
        <div class="student-form-card">
            <div class="student-form-card-header"><span class="student-form-step">02</span><div><h3>Informations de l'élève</h3><p>Identité et renseignements personnels</p></div></div>
            <div class="student-form-grid">
                <div class="student-form-field student-form-wide"><label>Matricule</label><div class="student-form-matricule">{{ $student->exists ? $student->student_number : 'Généré automatiquement après enregistrement' }}</div></div>
                <div class="student-form-field"><label for="last_name">Nom <span>*</span></label><input id="last_name" name="last_name" required maxlength="255" value="{{ old('last_name', $student->last_name) }}" placeholder="Nom de famille"></div>
                <div class="student-form-field"><label for="first_name">Prénom</label><input id="first_name" name="first_name" maxlength="255" value="{{ old('first_name', $student->first_name) }}" placeholder="Prénom(s)"></div>
                <div class="student-form-field"><label for="date_of_birth">Date de naissance</label><input id="date_of_birth" type="date" name="date_of_birth" value="{{ old('date_of_birth', $student->date_of_birth?->format('Y-m-d')) }}"></div>
                <div class="student-form-field"><label for="place_of_birth">Lieu de naissance</label><input id="place_of_birth" name="place_of_birth" maxlength="255" value="{{ old('place_of_birth', $student->place_of_birth) }}" placeholder="Ville / Localité"></div>
                <div class="student-form-field"><label for="gender">Sexe</label><select id="gender" name="gender"><option value="">Sélectionner</option><option value="M" @selected(old('gender', $student->gender) === 'M')>Masculin</option><option value="F" @selected(old('gender', $student->gender) === 'F')>Féminin</option></select></div>
            </div>
        </div>
        <div class="student-form-card">
            <div class="student-form-card-header"><span class="student-form-step">03</span><div><h3>Parent ou tuteur</h3><p>Coordonnées de la personne responsable</p></div></div>
            <div class="student-form-grid">
                <div class="student-form-field"><label for="guardian_name">Nom du parent / tuteur</label><input id="guardian_name" name="guardian_name" maxlength="255" value="{{ old('guardian_name', $student->guardian_name) }}" placeholder="Nom complet"></div>
                <div class="student-form-field"><label for="guardian_phone">Téléphone</label><input id="guardian_phone" type="tel" name="guardian_phone" maxlength="40" value="{{ old('guardian_phone', $student->guardian_phone) }}" placeholder="Ex. 6XX XXX XXX"></div>
                <div class="student-form-field"><label for="guardian_email">Adresse e-mail</label><input id="guardian_email" type="email" name="guardian_email" maxlength="255" value="{{ old('guardian_email', $student->guardian_email) }}" placeholder="parent@exemple.com"></div>
                <div class="student-form-field"><label for="address">Adresse de résidence</label><input id="address" name="address" maxlength="255" value="{{ old('address', $student->address) }}" placeholder="Quartier, ville"></div>
            </div>
        </div>
        <div class="student-form-footer"><a class="student-form-btn student-form-outline" href="{{ route('company.school.students.index') }}">Annuler</a><button class="student-form-btn student-form-primary" type="submit">{{ $student->exists ? 'Enregistrer les modifications' : 'Créer la fiche élève' }}</button></div>
    </form>
</div>
<style>
.student-form-page{max-width:1080px;margin:auto;color:#17223b}.student-form-heading{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:23px}.student-form-eyebrow{font-size:11px;letter-spacing:1.1px;color:#4f46e5;font-weight:800}.student-form-heading h2{font-size:26px;margin:6px 0}.student-form-heading p,.student-form-card-header p{font-size:13px;color:#66738b;margin:5px 0 0}.student-form-card{background:#fff;border:1px solid #e5eaf3;border-radius:15px;padding:24px;margin-bottom:19px;box-shadow:0 3px 16px #17223b07}.student-form-card-header{display:flex;align-items:center;gap:12px;padding-bottom:17px;border-bottom:1px solid #edf0f5;margin-bottom:22px}.student-form-step{display:grid;place-items:center;width:40px;height:40px;border-radius:11px;background:#eef2ff;color:#4338ca;font-weight:800}.student-form-card-header h3{margin:0;font-size:16px}.student-form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:19px}.student-form-field{display:flex;flex-direction:column;gap:8px;min-width:0}.student-form-field label,.student-photo-fields>label:first-child{font-size:12px;font-weight:700;color:#42516c}.student-form-field label span{color:#dc2626}.student-form-field input,.student-form-field select,.student-form-matricule{width:100%;min-height:44px;padding:11px 13px;border:1px solid #dbe2ee;border-radius:9px;background:#fff;color:#17223b;font-size:13px;outline:none}.student-form-field input:focus,.student-form-field select:focus{border-color:#6366f1;box-shadow:0 0 0 3px #6366f119}.student-form-wide{grid-column:1/-1}.student-form-matricule{background:#f5f6ff;color:#4338ca;font-weight:700;border-style:dashed}.student-form-footer{display:flex;justify-content:flex-end;gap:11px;flex-wrap:wrap;margin:24px 0}.student-form-btn{display:inline-flex;justify-content:center;align-items:center;border-radius:9px;padding:12px 18px;border:1px solid transparent;font-size:13px;font-weight:700;cursor:pointer;text-decoration:none}.student-form-primary{background:#4f46e5;color:white}.student-form-primary:hover{background:#3730a3}.student-form-outline{background:white;border-color:#d9def0;color:#3730a3}.student-form-outline:hover{background:#f5f4ff}.student-photo-row{display:flex;gap:24px;align-items:center;flex-wrap:wrap}.student-photo-preview{width:112px;height:144px;flex:none;border:2px dashed #c4d0e2;border-radius:12px;display:grid;place-items:center;overflow:hidden;background:#f5f8fd;color:#8492a9;font-size:12px;font-weight:800}.student-photo-preview img{width:100%;height:100%;object-fit:cover}.student-photo-fields{display:flex;flex-direction:column;gap:12px;flex:1;min-width:230px}.student-photo-fields input[type=file]{font-size:13px;max-width:100%}.student-photo-fields small{font-size:12px;color:#66738b}.student-remove-photo{display:flex;align-items:center;gap:8px;font-size:12px;color:#a21d34;cursor:pointer}.student-field-error{color:#b91c1c!important}.student-form-errors{padding:14px 18px;border:1px solid #fecaca;background:#fff1f2;border-radius:12px;color:#991b1b;margin-bottom:20px}.student-form-errors ul{margin:8px 0 0;padding-left:20px}@media(max-width:640px){.student-form-grid{grid-template-columns:1fr}.student-form-card{padding:16px}.student-form-heading h2{font-size:22px}.student-form-footer .student-form-btn{flex:1}}
</style>
<script>
(() => {
    const input = document.getElementById('photo');
    const img = document.getElementById('photoImage');
    const placeholder = document.getElementById('photoPlaceholder');
    const remove = document.getElementById('remove_photo');
    const original = img.getAttribute('src');
    let previewUrl = null;
    function refresh() {
        if (previewUrl) { URL.revokeObjectURL(previewUrl); previewUrl = null; }
        const file = input.files[0];
        if (file && file.type.startsWith('image/')) {
            previewUrl = URL.createObjectURL(file);
            img.src = previewUrl; img.hidden = false; placeholder.hidden = true;
            if (remove) remove.checked = false;
        } else if (remove?.checked || !original) {
            img.removeAttribute('src'); img.hidden = true; placeholder.hidden = false;
        } else {
            img.src = original; img.hidden = false; placeholder.hidden = true;
        }
    }
    input.addEventListener('change', refresh);
    if (remove) remove.addEventListener('change', () => { if (remove.checked) input.value = ''; refresh(); });
    window.addEventListener('pagehide', () => { if (previewUrl) URL.revokeObjectURL(previewUrl); });
})();
</script>
@endsection
