<section class="product-form-card ecommerce-images-section" aria-labelledby="product-images-title">
    <div class="product-form-card-header">
        <div>
            <h2 id="product-images-title">Photos du produit</h2>
            <p>Ajoutez jusqu'à 5 photos JPG, PNG ou WebP (4 Mo maximum par photo).</p>
        </div>
        <span class="product-form-card-number">04</span>
    </div>

    @php
        $existingImages = isset($ecommerceProduct) ? $ecommerceProduct->images : collect();
        $remainingSlots = max(0, 5 - $existingImages->count());
    @endphp

    <div class="product-images-upload">
        <div class="product-images-upload-icon" aria-hidden="true">📷</div>
        <label for="product_images" class="product-images-upload-title">Ajouter des photos</label>
        <p class="product-images-upload-description">Sélectionnez une ou plusieurs images sur votre ordinateur.</p>
        <input id="product_images" type="file" name="images[]"
               accept="image/jpeg,image/png,image/webp" multiple
               data-max-images="5" data-existing-images="{{ $existingImages->count() }}"
               aria-describedby="product-images-help">
        <p id="product-images-help" class="product-images-help">
            @if($remainingSlots > 0)
                {{ $remainingSlots }} emplacement(s) disponible(s). Pour remplacer une photo, cochez « Supprimer » puis enregistrez.
            @else
                La galerie contient déjà 5 photos. Supprimez-en une pour en ajouter une nouvelle.
            @endif
        </p>
        <p id="product-images-client-error" class="product-images-error" role="alert" hidden></p>
    </div>

    @error('images') <p class="product-images-error">{{ $message }}</p> @enderror
    @error('images.*') <p class="product-images-error">{{ $message }}</p> @enderror

    <div id="product-images-preview" class="product-images-grid" aria-live="polite" hidden></div>

    @if($existingImages->isNotEmpty())
        <div class="product-images-existing-heading">
            <h3>Photos enregistrées</h3>
            <span>{{ $existingImages->count() }} / 5</span>
        </div>
        <div class="product-images-grid">
            @foreach($existingImages as $photo)
                <div class="product-image-item">
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($photo->image_path) }}"
                         alt="{{ $photo->alt_text ?: $ecommerceProduct->product?->name }}" loading="lazy">
                    @if($photo->is_primary)
                        <span class="product-image-primary">★ Image principale</span>
                    @endif
                    <label class="product-image-option">
                        <input type="radio" name="primary_image_id" value="{{ $photo->id }}"
                               @checked(old('primary_image_id', $existingImages->firstWhere('is_primary', true)?->id) == $photo->id)>
                        <span>Définir comme principale</span>
                    </label>
                    <label class="product-image-option product-image-delete">
                        <input type="checkbox" name="remove_image_ids[]" value="{{ $photo->id }}"
                               @checked(in_array($photo->id, old('remove_image_ids', [])))>
                        <span>Supprimer cette photo</span>
                    </label>
                </div>
            @endforeach
        </div>
    @endif
</section>

@once
<style>
.ecommerce-images-section { min-width:0; }
.ecommerce-images-section .product-form-card-header { display:flex;justify-content:space-between;gap:12px;margin-bottom:20px; }
.ecommerce-images-section .product-form-card-header h2 { margin:0;color:#172033;font-size:16px;font-weight:750; }
.ecommerce-images-section .product-form-card-header p { margin:6px 0 0;color:#64748b;font-size:12px;line-height:1.6; }
.ecommerce-images-section .product-form-card-number {color:#315bea;font-size:12px;font-weight:800;}
.product-images-upload {padding:22px 16px;text-align:center;background:#f8fbff;border:2px dashed #b9c9f8;border-radius:12px;transition:background .2s,border-color .2s;}
.product-images-upload:hover {background:#eff6ff;border-color:#315bea;}
.product-images-upload-icon {font-size:30px;margin-bottom:9px;}
.product-images-upload-title {display:block;font-size:14px;font-weight:750;color:#172033;margin-bottom:6px;}
.product-images-upload-description {font-size:12px;color:#64748b;line-height:1.5;margin:0 0 15px;}
.product-images-upload input[type=file] {display:block;width:100%;box-sizing:border-box;padding:10px;border:1px solid #dbe3f0;border-radius:9px;background:#fff;color:#334155;font:inherit;font-size:12px;}
.product-images-upload input[type=file]::file-selector-button {border:0;border-radius:7px;background:#315bea;color:#fff;padding:10px 12px;margin-right:10px;font-weight:700;cursor:pointer;}
.product-images-upload input[type=file]::file-selector-button:hover {background:#2448c9;}
.product-images-help {font-size:11px;color:#64748b;margin:12px 0 0;line-height:1.5;}
.product-images-error {color:#b91c1c;font-size:12px;margin:10px 0 0;}
.product-images-existing-heading {display:flex;align-items:center;justify-content:space-between;gap:12px;margin:22px 0 12px;}
.product-images-existing-heading h3 {font-size:13px;font-weight:750;color:#172033;margin:0;}
.product-images-existing-heading span {font-size:11px;font-weight:700;color:#64748b;}
.product-images-grid {display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;margin-top:16px;}
.product-image-item {min-width:0;padding:9px;border:1px solid #e2e8f0;border-radius:11px;background:#fff;overflow:hidden;}
.product-image-item img {display:block;width:100%;height:126px;object-fit:cover;border-radius:8px;background:#f1f5f9;}
.product-image-primary {display:inline-block;margin-top:8px;padding:5px 8px;border-radius:6px;background:#eff6ff;color:#2448c9;font-size:10px;font-weight:800;}
.product-image-option {display:flex;align-items:flex-start;gap:7px;margin-top:10px;color:#475569;font-size:11px;line-height:1.45;cursor:pointer;}
.product-image-option input {width:15px;height:15px;flex:0 0 15px;margin:0;accent-color:#315bea;}
.product-image-delete {color:#b91c1c;}
.product-image-delete input {accent-color:#dc2626;}
.product-image-preview-caption {font-size:11px;color:#64748b;overflow-wrap:anywhere;margin:8px 0 0;}
@media(max-width:650px){.product-images-upload{padding:18px 12px;}.product-images-grid{grid-template-columns:1fr;}.product-image-item img{height:175px;}}
</style>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('product_images');
    const preview = document.getElementById('product-images-preview');
    const error = document.getElementById('product-images-client-error');
    if (!input || !preview || !error) return;
    const maxImages = Number(input.dataset.maxImages || 5);
    const existingImages = Number(input.dataset.existingImages || 0);
    const deleteInputs = [...document.querySelectorAll('input[name="remove_image_ids[]"]')];
    const primaryInputs = [...document.querySelectorAll('input[name="primary_image_id"]')];
    function update() {
        const removed = deleteInputs.filter(el => el.checked).length;
        const available = maxImages - existingImages + removed;
        const files = [...input.files];
        error.hidden = true;
        error.textContent = '';
        preview.replaceChildren();
        preview.hidden = true;
        if (files.length > available || files.some(file => file.size > 4 * 1024 * 1024 || !['image/jpeg','image/png','image/webp'].includes(file.type))) {
            error.textContent = 'Maximum ' + Math.max(0,available) + ' nouvelle(s) photo(s), au format JPG, PNG ou WebP et de 4 Mo maximum chacune.';
            error.hidden = false;
            input.value = '';
            return;
        }
        for (const file of files) {
            const card = document.createElement('div');
            card.className = 'product-image-item';
            const img = document.createElement('img');
            img.alt = 'Aperçu : ' + file.name;
            const url = URL.createObjectURL(file);
            img.src = url;
            img.addEventListener('load', () => URL.revokeObjectURL(url), {once:true});
            const caption = document.createElement('p');
            caption.className = 'product-image-preview-caption';
            caption.textContent = file.name;
            card.append(img, caption);
            preview.append(card);
        }
        preview.hidden = files.length === 0;
    }
    input.addEventListener('change', update);
    deleteInputs.forEach(el => el.addEventListener('change', () => {
        const card = el.closest('.product-image-item');
        const radio = card?.querySelector('input[name="primary_image_id"]');
        if (el.checked && radio?.checked) radio.checked = false;
        if (radio) radio.disabled = el.checked;
        update();
    }));
    primaryInputs.forEach(el => el.addEventListener('change', () => {
        if (!el.checked) return;
        const card = el.closest('.product-image-item');
        const remove = card?.querySelector('input[name="remove_image_ids[]"]');
        if (remove?.checked) {remove.checked = false;el.disabled = false;update();}
    }));
    deleteInputs.forEach(el => {if (el.checked){const radio=el.closest('.product-image-item')?.querySelector('input[name="primary_image_id"]');if(radio)radio.disabled=true;}});
});
</script>
@endonce
