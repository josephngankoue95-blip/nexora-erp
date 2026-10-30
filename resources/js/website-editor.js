document.addEventListener('DOMContentLoaded', () => {
    const editor = document.querySelector('[data-ws-editor]');
    const hidden = document.querySelector('#content');
    if (!editor || !hidden) return;

    const oldValue = hidden.value || '';
    if (oldValue && !/<\/?(?:p|h[1-6]|div|ul|ol|li|blockquote|strong|em|br|a|img)\b/i.test(oldValue)) {
        editor.textContent = oldValue;
    } else {
        editor.innerHTML = oldValue;
    }

    const sync = () => { hidden.value = editor.innerHTML; };
    editor.addEventListener('input', sync);
    editor.addEventListener('blur', sync);
    editor.closest('form')?.addEventListener('submit', sync);

    document.querySelectorAll('[data-ws-command]').forEach(button => {
        button.addEventListener('click', () => {
            editor.focus();
            const cmd = button.dataset.wsCommand;
            if (cmd === 'createLink') {
                const url = window.prompt('URL du lien (https://...)');
                if (!url) return;
                try {
                    const parsed = new URL(url);
                    if (!['https:', 'http:'].includes(parsed.protocol)) return alert('Adresse HTTP(S) uniquement.');
                    document.execCommand('createLink', false, parsed.href);
                } catch { alert('URL invalide.'); }
            } else {
                document.execCommand(cmd, false, button.dataset.wsValue || null);
            }
            sync();
        });
    });

    // Boutons universels : aucune dépendance au module E-commerce.
    const buttonModal = document.querySelector('[data-ws-button-modal]');
    const buttonOpen = document.querySelector('[data-ws-open-button]');
    if (buttonModal && buttonOpen) {
        const buttonClose = buttonModal.querySelector('[data-ws-button-close]');
        const buttonForm = buttonModal.querySelector('[data-ws-button-form]');
        const labelInput = buttonForm.querySelector('[name="button_label"]');
        const typeInput = buttonForm.querySelector('[name="button_type"]');
        const targetInput = buttonForm.querySelector('[name="button_target"]');
        const styleInput = buttonForm.querySelector('[name="button_style"]');
        const error = buttonForm.querySelector('[data-ws-button-error]');
        let buttonRange = null;
        const saveButtonSelection = () => {
            const sel = window.getSelection();
            if (sel && sel.rangeCount && editor.contains(sel.anchorNode)) {
                buttonRange = sel.getRangeAt(0).cloneRange();
            }
        };
        editor.addEventListener('mouseup', saveButtonSelection);
        editor.addEventListener('keyup', saveButtonSelection);
        editor.addEventListener('input', saveButtonSelection);
        const closeButtonModal = () => { buttonModal.hidden = true; buttonOpen.focus(); };
        buttonOpen.addEventListener('click', () => {
            saveButtonSelection();
            error.textContent = '';
            buttonModal.hidden = false;
            labelInput.focus();
        });
        buttonClose.addEventListener('click', closeButtonModal);
        buttonModal.addEventListener('click', e => { if (e.target === buttonModal) closeButtonModal(); });
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape' && !buttonModal.hidden) closeButtonModal();
        });
        const normalizeDestination = (type, value) => {
            const raw = value.trim();
            if (!raw) throw new Error('Veuillez saisir une destination.');
            if (type === 'page') {
                if (!raw.startsWith('/') || raw.startsWith('//') || /[\\\s<>"']/.test(raw)) {
                    throw new Error('Indiquez un chemin interne commençant par / (ex. /site/entreprise/contact).');
                }
                return raw;
            }
            if (type === 'external') {
                const u = new URL(raw);
                if (!['http:', 'https:'].includes(u.protocol)) throw new Error('Lien HTTP ou HTTPS uniquement.');
                return u.href;
            }
            if (type === 'phone' || type === 'whatsapp') {
                const digits = raw.replace(/[\s().-]/g, '');
                if (!/^\+?[0-9]{7,15}$/.test(digits)) throw new Error('Numéro invalide (7 à 15 chiffres).');
                return type === 'phone' ? 'tel:' + digits : 'https://wa.me/' + digits.replace(/^\+/, '');
            }
            if (type === 'email') {
                if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(raw)) throw new Error('Adresse e-mail invalide.');
                return 'mailto:' + raw;
            }
            throw new Error('Type de lien non autorisé.');
        };
        const insertButton = buttonForm.querySelector(
    '[data-ws-button-insert]'
);

insertButton.addEventListener('click', event => {
    event.preventDefault();

    error.textContent = '';

    try {
                const label = labelInput.value.trim();
                if (!label || label.length > 100) throw new Error('Texte requis (100 caractères maximum).');
                const href = normalizeDestination(typeInput.value, targetInput.value);
                const anchor = document.createElement('a');
                anchor.href = href;
                anchor.textContent = label;
                anchor.className = styleInput.value === 'outline' ? 'ws-site-button ws-site-button-outline' : 'ws-site-button ws-site-button-primary';
                if (typeInput.value === 'external' || typeInput.value === 'whatsapp') {
                    anchor.target = '_blank';
                    anchor.rel = 'noopener noreferrer';
                }
                editor.focus();
                const sel = window.getSelection();
                let range;
                if (buttonRange && editor.contains(buttonRange.commonAncestorContainer)) {
                    range = buttonRange.cloneRange();
                } else {
                    range = document.createRange();
                    range.selectNodeContents(editor);
                    range.collapse(false);
                }
                range.deleteContents();
                range.insertNode(anchor);
                range.setStartAfter(anchor);
                range.collapse(true);
                sel.removeAllRanges();
                sel.addRange(range);
                buttonRange = range.cloneRange();
                sync();
                closeButtonModal();
            } catch (err) {
                error.textContent = err.message || 'Destination invalide.';
            }
        });
    }

    const modal = document.querySelector('[data-ws-media-modal]');
    const open = document.querySelector('[data-ws-open-media]');
    if (!modal || !open) return;
    const grid = modal.querySelector('[data-ws-media-grid]');
    const status = modal.querySelector('[data-ws-media-status]');
    const close = modal.querySelector('[data-ws-media-close]');
    let savedRange = null;

    const rememberSelection = () => {
        const selection = window.getSelection();
        if (selection && selection.rangeCount && editor.contains(selection.anchorNode)) {
            savedRange = selection.getRangeAt(0).cloneRange();
        }
    };
    editor.addEventListener('keyup', rememberSelection);
    editor.addEventListener('mouseup', rememberSelection);
    editor.addEventListener('input', rememberSelection);

    const hideModal = () => {
        modal.hidden = true;
        open.focus();
    };
    close.addEventListener('click', hideModal);
    modal.addEventListener('click', event => { if (event.target === modal) hideModal(); });
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && !modal.hidden) hideModal();
    });

    const insertImage = (url, alt) => {
        const image = document.createElement('img');
        image.src = url;
        image.alt = alt || 'Image du site';
        const selection = window.getSelection();
        editor.focus();
        if (savedRange && editor.contains(savedRange.commonAncestorContainer)) {
            selection.removeAllRanges();
            selection.addRange(savedRange);
        } else {
            const range = document.createRange();
            range.selectNodeContents(editor);
            range.collapse(false);
            selection.removeAllRanges();
            selection.addRange(range);
        }
        const range = selection.getRangeAt(0);
        range.deleteContents();
        range.insertNode(image);
        range.setStartAfter(image);
        range.collapse(true);
        selection.removeAllRanges();
        selection.addRange(range);
        savedRange = range.cloneRange();
        sync();
        hideModal();
    };

    open.addEventListener('click', async () => {
        rememberSelection();
        modal.hidden = false;
        close.focus();
        grid.replaceChildren();
        status.textContent = 'Chargement des images…';
        try {
            const response = await fetch(modal.dataset.url, {
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin',
            });
            if (!response.ok) throw new Error('Impossible de charger la médiathèque (' + response.status + ').');
            const data = await response.json();
            const images = Array.isArray(data.images) ? data.images : [];
            status.textContent = images.length ? 'Sélectionnez une image.' : 'Aucune image disponible.';
            for (const item of images) {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'ws-media-choice';
                const image = document.createElement('img');
                image.src = item.url;
                image.alt = item.name || 'Image';
                image.loading = 'lazy';
                const name = document.createElement('span');
                name.textContent = item.name || 'Image';
                button.append(image, name);
                button.addEventListener('click', () => insertImage(item.url, item.name));
                grid.append(button);
            }
        } catch (error) {
            status.textContent = error.message || 'Erreur de chargement.';
        }
    });
});
