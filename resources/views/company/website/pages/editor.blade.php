<label for="ws-editor">Contenu de la page</label>

<div class="ws-editor-tools" role="toolbar" aria-label="Mise en forme">
    <button type="button" data-ws-command="formatBlock" data-ws-value="p">Paragraphe</button>
    <button type="button" data-ws-command="formatBlock" data-ws-value="h2">Titre H2</button>
    <button type="button" data-ws-command="formatBlock" data-ws-value="h3">Titre H3</button>
    <button type="button" data-ws-command="bold">Gras</button>
    <button type="button" data-ws-command="italic">Italique</button>
    <button type="button" data-ws-command="insertUnorderedList">Liste</button>
    <button type="button" data-ws-command="insertOrderedList">Liste numérotée</button>
    <button type="button" data-ws-command="createLink">Lien</button>
    <button type="button" data-ws-command="removeFormat">Effacer style</button>
    <button type="button" data-ws-open-media>🖼 Insérer une image</button>
    <button type="button" data-ws-open-button>🔗 Insérer un bouton</button>
</div>

<div
    id="ws-editor"
    class="ws-editor-area"
    data-ws-editor
    contenteditable="true"
    role="textbox"
    aria-multiline="true"
    aria-label="Contenu de la page"
></div>

<textarea id="content" name="content" hidden>{{ old('content', $page->content) }}</textarea>
@error('content')
    <small class="field-error">{{ $message }}</small>
@enderror

<p class="wp-hint">Enregistrez la page avant de lancer son aperçu.</p>

{{-- Fenêtre médiathèque : pas de formulaire imbriqué --}}
<div
    class="ws-media-modal"
    data-ws-media-modal
    data-url="{{ route('company.website.media.picker') }}"
    hidden
>
    <div class="ws-media-dialog" role="dialog" aria-modal="true" aria-labelledby="ws-media-title">
        <div class="ws-media-heading">
            <h2 id="ws-media-title">Choisir une image</h2>
            <button type="button" data-ws-media-close aria-label="Fermer">✕</button>
        </div>

        <p class="ws-media-status" data-ws-media-status role="status"></p>
        <div class="ws-media-grid" data-ws-media-grid></div>

        <p class="ws-media-footer">
            Pour ajouter des images, ouvrez
            <a href="{{ route('company.website.media.index') }}" target="_blank" rel="noopener noreferrer">la médiathèque</a>,
            puis rouvrez cette fenêtre.
        </p>
    </div>
</div>

{{-- Fenêtre bouton : div au lieu d'un form, pour ne pas imbriquer de formulaires --}}
<div class="ws-button-modal" data-ws-button-modal hidden>
    <div class="ws-button-dialog" role="dialog" aria-modal="true" aria-labelledby="ws-button-title">
        <div class="ws-button-heading">
            <h2 id="ws-button-title">Insérer un bouton personnalisé</h2>
            <button type="button" data-ws-button-close aria-label="Fermer">✕</button>
        </div>

        <div data-ws-button-form>
            <label>
                Texte du bouton
                <input type="text" name="button_label" maxlength="100" placeholder="Demander un devis">
            </label>

            <label>
                Type de destination
                <select name="button_type">
                    <option value="page">Page interne (chemin)</option>
                    <option value="external">Site externe (https://)</option>
                    <option value="phone">Appeler</option>
                    <option value="email">Envoyer un e-mail</option>
                    <option value="whatsapp">WhatsApp</option>
                </select>
            </label>

            <label>
                Destination
                <input type="text" name="button_target" placeholder="/site/mon-entreprise/contact">
            </label>

            <p class="ws-button-hint">
                Page interne : utilisez le chemin public complet de la page,
                par exemple /site/mon-entreprise/contact.
            </p>

            <label>
                Apparence
                <select name="button_style">
                    <option value="primary">Bouton principal</option>
                    <option value="outline">Bouton secondaire</option>
                </select>
            </label>

            <p class="ws-button-error" data-ws-button-error role="alert"></p>

            <button type="button" class="ws-button-submit" data-ws-button-insert>
                Insérer dans la page
            </button>
        </div>
    </div>
</div>
