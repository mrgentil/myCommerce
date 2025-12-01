@extends('vendor.layouts.master')

@section('content')

<div class="card mt-4">
    <div class="card-header card-header-bg text-white">
        <h6 class="d-flex align-items-center mb-0 dt-heading">{{ __('cms.products.title_create') }}</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('vendor.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Language Tabs --}}
            @php 
                // Use session locale or first active language
                $sessionLang = app()->getLocale();
                $firstLang = $languages->first()?->code ?? 'en';
                $activeLang = $languages->contains('code', $sessionLang) ? $sessionLang : $firstLang;
            @endphp
            <div class="alert alert-info small mb-3">
                <i class="fa fa-info-circle me-2"></i>
                <strong>Astuce :</strong> Remplissez le nom du produit dans au moins une langue. 
                Vous pouvez ajouter des traductions dans les autres langues si besoin.
            </div>
            
            <ul class="nav nav-tabs" id="languageTabs" role="tablist">
                @foreach($languages as $language)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $language->code === $activeLang ? 'active' : '' }}" 
                                id="{{ $language->code }}-tab" 
                                data-bs-toggle="tab" 
                                data-bs-target="#{{ $language->code }}" 
                                type="button" 
                                role="tab">
                            {{ ucwords($language->name) }}
                        </button>
                    </li>
                @endforeach
            </ul>

            <div class="tab-content mt-3" id="languageTabContent">
                @foreach($languages as $language)
                    <div class="tab-pane fade {{ $language->code === $activeLang ? 'show active' : '' }}" 
                         id="{{ $language->code }}" 
                         role="tabpanel">

                        <div class="mb-3">
                            <label class="form-label">
                                Nom du produit ({{ strtoupper($language->code) }})
                            </label>
                            <input type="text"
                                   name="translations[{{ $language->code }}][name]"
                                   class="form-control @error("translations.{$language->code}.name") is-invalid @enderror"
                                   value="{{ old("translations.{$language->code}.name") }}"
                                   placeholder="Ex: T-shirt en coton bio, Chaussures de sport Nike...">
                            <small class="text-muted">Nom clair et descriptif du produit (max 255 caractères)</small>
                            @error("translations.{$language->code}.name")
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Description ({{ strtoupper($language->code) }})
                            </label>
                            <textarea name="translations[{{ $language->code }}][description]"
                                      class="form-control ck-editor-multi-languages @error("translations.{$language->code}.description") is-invalid @enderror"
                                      placeholder="Décrivez votre produit en détail : matériaux, caractéristiques, avantages...">{{ old("translations.{$language->code}.description") }}</textarea>
                            <small class="text-muted">Description détaillée pour aider les clients à comprendre votre produit</small>
                            @error("translations.{$language->code}.description")
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Category & Brand --}}
            <div class="row mt-4">
                <div class="col-md-6">
                    <label class="form-label">Catégorie <span class="text-danger">*</span></label>
                    <select name="category_id" class="form-control">
                        <option value="">-- Sélectionnez une catégorie --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->translation->name ?? '—' }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Dans quelle catégorie classer ce produit ?</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Marque</label>
                    <select name="brand_id" class="form-control">
                        <option value="">-- Sans marque --</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                {{ $brand->translation->name ?? '—' }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Optionnel : marque du produit si applicable</small>
                </div>
            </div>

            {{-- Variants --}}
            <div class="mt-4">
                <h5><i class="fa fa-cubes me-2"></i>Variantes du produit</h5>
                <p class="text-muted small">
                    Ajoutez au moins une variante. Chaque variante peut avoir un prix, stock et attributs différents 
                    (ex: T-shirt Rouge Taille M, T-shirt Bleu Taille L).
                </p>
            </div>
            <div id="variants-wrapper" class="mt-3"></div>
            <div class="d-flex gap-2 mt-3 align-items-center">
                 <button type="button" id="add-variant-btn"
                    class="btn btn-outline-primary btn-sm">
                    <i class="fa-solid fa-plus me-1"></i> Ajouter une variante
                </button>
                <button type="button" id="remove-variant-btn"
                    class="btn btn-outline-danger btn-sm" disabled>
                    <i class="fa-solid fa-trash me-1"></i> Supprimer la dernière
                </button>
            </div>
            <template id="variant-template">
                <div class="card p-3 mt-3 variant-item border rounded bg-light" data-index="__INDEX__">
                    <h6 class="text-primary mb-3"><i class="fa fa-cube me-1"></i> Variante #<span class="variant-number">__INDEX__</span></h6>
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label>Nom de la variante <span class="text-danger">*</span></label>
                            <input type="text" name="variants[__INDEX__][name]" class="form-control" value="__NAME__" placeholder="Ex: Rouge - Taille M" />
                            <small class="text-muted">Identifie cette variante</small>
                            <div class="invalid-feedback d-block variant-name-error"></div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label>Prix (€) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="variants[__INDEX__][price]" class="form-control" value="__PRICE__" placeholder="29.99" />
                            <small class="text-muted">Prix de vente</small>
                            <div class="invalid-feedback d-block variant-price-error"></div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label>Prix promo</label>
                            <input type="number" step="0.01" name="variants[__INDEX__][discount_price]" class="form-control" value="__DISCOUNT__" placeholder="19.99" />
                            <small class="text-muted">Optionnel : prix soldé</small>
                        </div>

                        <div class="col-md-4 mb-2">
                            <label>Stock <span class="text-danger">*</span></label>
                            <input type="number" name="variants[__INDEX__][stock]" class="form-control" value="__STOCK__" placeholder="100" />
                            <small class="text-muted">Quantité disponible</small>
                            <div class="invalid-feedback d-block variant-stock-error"></div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label>SKU <span class="text-danger">*</span></label>
                            <input type="text" name="variants[__INDEX__][SKU]" class="form-control" value="__SKU__" placeholder="PROD-001-RED-M" />
                            <small class="text-muted">Code unique du produit</small>
                            <div class="invalid-feedback d-block variant-sku-error"></div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label>Code-barres</label>
                            <input type="text" name="variants[__INDEX__][barcode]" class="form-control" value="__BARCODE__" placeholder="1234567890123" />
                            <small class="text-muted">Optionnel : EAN/UPC</small>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label>Poids</label>
                            <input type="text" name="variants[__INDEX__][weight]" class="form-control" placeholder="0.5 kg" value="__WEIGHT__" />
                            <small class="text-muted">Pour le calcul livraison</small>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label>Dimensions</label>
                            <input type="text" name="variants[__INDEX__][dimension]" class="form-control" placeholder="30x20x5 cm" value="__DIMENSION__" />
                            <small class="text-muted">L x l x H</small>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label>Taille</label>
                            <select name="variants[__INDEX__][size_id]" class="form-control">
                                <option value="">--</option>
                                @foreach($sizes as $size)
                                    <option value="{{ $size->id }}" __SIZE_SELECTED__>{{ $size->value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label>Couleur</label>
                            <select name="variants[__INDEX__][color_id]" class="form-control">
                                <option value="">--</option>
                                @foreach($colors as $color)
                                    <option value="{{ $color->id }}" __COLOR_SELECTED__>{{ $color->value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </template>

            {{-- Images --}}
            <div class="mt-4">
                <h5><i class="fa fa-images me-2"></i>Images du produit</h5>
                <p class="text-muted small">
                    Ajoutez des photos de qualité. La première image sera l'image principale.
                    Formats acceptés : JPEG, PNG, GIF, WebP (max 2 Mo par image).
                </p>
                <div class="custom-file">
                    <label class="btn btn-outline-primary" for="productImages">
                        <i class="fa fa-upload me-1"></i> Choisir des images
                    </label>
                    <input type="file" name="images[]" class="form-control d-none" id="productImages" multiple onchange="previewMultipleImages(this)" accept="image/jpeg,image/png,image/gif,image/webp">
                </div>
                <div id="productImagesPreview" class="mt-3 d-flex flex-wrap gap-2"></div>
            </div>

            {{-- Submit --}}
            <div class="mt-4 pt-3 border-top">
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="fa fa-check me-1"></i> Enregistrer le produit
                </button>
                <a href="{{ route('vendor.products.index') }}" class="btn btn-outline-secondary btn-lg ms-2">
                    <i class="fa fa-times me-1"></i> Annuler
                </a>
            </div>
        </form>
    </div>
</div>

@endsection

@section('js')
<script>
document.addEventListener("DOMContentLoaded", function () {
    @if ($errors->any())
        var firstErrorElement = document.querySelector('.is-invalid');
        if (firstErrorElement) {
            var tabPane = firstErrorElement.closest('.tab-pane');
            if (tabPane) {
                var tabId = tabPane.getAttribute('id');
                var triggerEl = document.querySelector(`button[data-bs-target="#${tabId}"]`);
                if (triggerEl) {
                    var tab = new bootstrap.Tab(triggerEl);
                    tab.show();
                }
            }
        }
    @endif
});
</script>

<script>
let variantIndex = 0;
let validationErrors = @json($errors->getMessages());

function updateRemoveButtonState() {
    const count = $('#variants-wrapper .variant-item').length;
    $('#remove-variant-btn').prop('disabled', count === 0);
}

function addVariant(variant = {}, index = variantIndex) {
    let template = $('#variant-template').html();
    template = template
        .replace(/__INDEX__/g, index)
        .replace(/__NAME__/g, variant.name || '')
        .replace(/__PRICE__/g, variant.price || '')
        .replace(/__DISCOUNT__/g, variant.discount_price || '')
        .replace(/__STOCK__/g, variant.stock || '')
        .replace(/__SKU__/g, variant.SKU || '')   
        .replace(/__BARCODE__/g, variant.barcode || '')
        .replace(/__WEIGHT__/g, variant.weight || '')
        .replace(/__DIMENSION__/g, variant.dimension || '')
        .replace(/__SIZE_SELECTED__/g, '')
        .replace(/__COLOR_SELECTED__/g, '');

    const $variant = $(template);

    if(variant.size_id) $variant.find(`select[name="variants[${index}][size_id]"] option[value="${variant.size_id}"]`).attr('selected', true);
    if(variant.color_id) $variant.find(`select[name="variants[${index}][color_id]"]`).attr('selected', true);

    if(validationErrors[`variants.${index}.name`]) {
        $variant.find('.variant-name-error').text(validationErrors[`variants.${index}.name`][0]);
        $variant.find(`input[name="variants[${index}][name]"]`).addClass('is-invalid');
    }
    if(validationErrors[`variants.${index}.price`]) {
        $variant.find('.variant-price-error').text(validationErrors[`variants.${index}.price`][0]);
        $variant.find(`input[name="variants[${index}][price]"]`).addClass('is-invalid');
    }
    if(validationErrors[`variants.${index}.stock`]) {
        $variant.find('.variant-stock-error').text(validationErrors[`variants.${index}.stock`][0]);
        $variant.find(`input[name="variants[${index}][stock]"]`).addClass('is-invalid');
    }
    if(validationErrors[`variants.${index}.SKU`]) {
        $variant.find('.variant-sku-error').text(validationErrors[`variants.${index}.SKU`][0]);
        $variant.find(`input[name="variants[${index}][SKU]"]`).addClass('is-invalid');
    }

    $('#variants-wrapper').append($variant);
    variantIndex++;
    updateRemoveButtonState();
}

$(document).ready(function () {
    @if(old('variants'))
        let oldVariants = @json(old('variants'));
        oldVariants.forEach((v, i) => addVariant(v, i));
    @else
        addVariant();
    @endif

    $('#add-variant-btn').click(() => addVariant());
    $('#remove-variant-btn').click(() => {
        const $variants = $('#variants-wrapper .variant-item');
        if ($variants.length > 0) {
            $variants.last().remove();
            variantIndex--;
            updateRemoveButtonState();
        }
    });
});
</script>

{{-- Image Preview --}}
<script>
let selectedFiles = [];

@if (session()->has('_old_input'))
    window.addEventListener('load', () => {
        const oldFiles = sessionStorage.getItem('vendor_product_temp_images');
        if (oldFiles) {
            selectedFiles = JSON.parse(oldFiles).map(b64 => {
                const file = dataURLtoFile(b64.data, b64.name);
                file.uniqueId = b64.name + '_' + file.size; 
                return file;
            });
            refreshPreview(document.getElementById('productImages'));
        }
    });
@endif

function previewMultipleImages(input) {
    const files = Array.from(input.files);

    files.forEach(file => {
        const uniqueId = file.name + '_' + file.size;
        if (!selectedFiles.some(f => f.uniqueId === uniqueId)) {
            file.uniqueId = uniqueId;
            selectedFiles.push(file);
        }
    });

    refreshPreview(input);
}

function refreshPreview(input) {
    const previewContainer = document.getElementById('productImagesPreview');
    previewContainer.innerHTML = '';

    selectedFiles.forEach(file => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const wrapper = document.createElement('div');
            wrapper.className = 'position-relative m-1';

            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'img-thumbnail';
            img.style.maxWidth = '150px';

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.innerHTML = '&times;';
            removeBtn.className = 'btn btn-sm btn-danger position-absolute top-0 end-0';
            removeBtn.onclick = function() {
                selectedFiles = selectedFiles.filter(f => f.uniqueId !== file.uniqueId);
                updateFileInput(input);
                refreshPreview(input);
            };

            wrapper.appendChild(img);
            wrapper.appendChild(removeBtn);
            previewContainer.appendChild(wrapper);
        };
        reader.readAsDataURL(file);
    });

    updateFileInput(input);
    saveTempImages();
}

function updateFileInput(input) {
    const dataTransfer = new DataTransfer();
    selectedFiles.forEach(file => dataTransfer.items.add(file));
    input.files = dataTransfer.files;
}

function saveTempImages() {
    const readers = selectedFiles.map(file => new Promise(resolve => {
        const reader = new FileReader();
        reader.onload = e => resolve({ name: file.name, data: e.target.result });
        reader.readAsDataURL(file);
    }));

    Promise.all(readers).then(results => {
        sessionStorage.setItem('vendor_product_temp_images', JSON.stringify(results));
    });
}

function dataURLtoFile(dataurl, filename) {
    const arr = dataurl.split(','),
          mime = arr[0].match(/:(.*?);/)[1],
          bstr = atob(arr[1]),
          n = bstr.length,
          u8arr = new Uint8Array(n);
    for (let i = 0; i < n; i++) u8arr[i] = bstr.charCodeAt(i);
    return new File([u8arr], filename, {type:mime});
}
</script>

<script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>
<script>
document.querySelectorAll('.ck-editor-multi-languages').forEach((element) => {
    ClassicEditor.create(element).catch(error => console.error(error));
});
</script>
@endsection
