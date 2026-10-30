
document.addEventListener('DOMContentLoaded', () => {
    'use strict';

    const root = document.getElementById('wb-builder');
    if (!root) return;

    const $ = (selector) => document.querySelector(selector);

    const canvas = $('#wb-canvas');
    const library = $('#wb-categories');
    const inspector = $('#wb-properties');
    const hidden = $('#wb-json');
    const form = $('#wb-form');
    const search = $('#wb-search');

    if (!canvas || !library || !inspector || !hidden || !form) return;

    const definitions = [
        ['STRUCTURE', [
            ['hero', 'Bannière', '▣'],
            ['columns', 'Deux colonnes', '▥'],
            ['divider', 'Séparateur', '━'],
            ['spacer', 'Espacement', '↕']
        ]],
        ['CONTENU', [
            ['heading', 'Titre', 'H₂'],
            ['text', 'Paragraphe', '☰'],
            ['button', 'Bouton', '↗'],
            ['label', 'Étiquette', '◆'],
            ['quote', 'Citation', '❞'],
            ['faq', 'Questions fréquentes', '?']
        ]],
        ['MÉDIAS', [
            ['image', 'Image', '▧'],
            ['gallery', 'Galerie', '▦'],
            ['video', 'Vidéo', '▶']
        ]],
        ['PRÉSENTATIONS', [
            ['cards', 'Cartes', '▤'],
            ['stats', 'Statistiques', '◫'],
            ['cta', 'Appel à action', '✦']
        ]],
        ['CARROUSELS', [
            ['carousel', 'Carrousel', '◧'],
            ['banner_slider', 'Bannières animées', '▣'],
            ['testimonials', 'Témoignages', '❝']
        ]]
    ];

    const types = new Map(
        definitions.flatMap(([group, items]) =>
            items.map(([id, label, icon]) => [
                id, { group, label, icon }
            ])
        )
    );

    const defaults = {
        hero: {
            title: 'Bienvenue chez nous',
            subtitle: 'Découvrez nos services',
            background: '#142d50',
            color: '#ffffff'
        },
        heading: { title: 'Un titre inspirant' },
        text: { text: 'Présentez votre activité ici.' },
        image: { alt: 'Notre image' },
        gallery: { alt: 'Galerie photos' },
        button: { label: 'En savoir plus', url: '/' },
        label: { label: 'Nouveauté' },
        columns: {
            left: 'Première colonne',
            right: 'Deuxième colonne'
        },
        cards: {
            title: 'Nos services',
            left: 'Service 1',
            right: 'Service 2',
            third: 'Service 3'
        },
        stats: {
            title: 'Nos chiffres',
            left: '100+',
            right: '20+',
            third: '98%'
        },
        quote: {
            text: 'Une belle expérience',
            title: 'Notre client'
        },
        faq: {
            title: 'Questions fréquentes',
            left: 'Comment nous contacter ?',
            right: 'Contactez notre équipe.'
        },
        cta: {
            title: 'Parlons de votre projet',
            subtitle: 'Nous sommes à votre écoute',
            label: 'Contactez-nous',
            url: '/'
        },
        spacer: { height: '48' },
        video: {
            title: 'Découvrez notre vidéo',
            url: ''
        },
        carousel: {
            title: 'Nos réalisations',
            caption: 'Premier visuel',
            caption2: 'Deuxième visuel',
            caption3: 'Troisième visuel',
            autoplay: 'non',
            interval: '4500'
        },
        banner_slider: {
            title: 'Bienvenue',
            title2: 'Nos solutions',
            title3: 'Notre équipe',
            subtitle: 'Découvrez notre univers',
            autoplay: 'oui',
            interval: '4500'
        },
        testimonials: {
            title: 'Ils nous font confiance',
            left: 'Un service remarquable',
            right: 'Une expérience exceptionnelle',
            third: 'Une équipe professionnelle',
            author: 'Client satisfait',
            autoplay: 'non',
            interval: '4500'
        }
    };

    const fieldOrder = [
        'title', 'title2', 'title3', 'subtitle',
        'text', 'label', 'url',
        'image', 'image2', 'image3', 'alt',
        'caption', 'caption2', 'caption3',
        'left', 'right', 'third',
        'author', 'author2', 'author3',
        'background', 'color',
        'height', 'autoplay', 'interval',
        'animation', 'radius', 'padding'
    ];

    const labels = {
        title: 'Titre',
        title2: 'Titre 2',
        title3: 'Titre 3',
        subtitle: 'Sous-titre',
        text: 'Texte',
        label: 'Libellé',
        url: 'Lien URL',
        image: 'Image 1',
        image2: 'Image 2',
        image3: 'Image 3',
        alt: 'Description de l’image',
        caption: 'Légende 1',
        caption2: 'Légende 2',
        caption3: 'Légende 3',
        left: 'Contenu 1',
        right: 'Contenu 2',
        third: 'Contenu 3',
        author: 'Auteur 1',
        author2: 'Auteur 2',
        author3: 'Auteur 3',
        background: 'Couleur de fond',
        color: 'Couleur du texte',
        height: 'Hauteur (px)',
        autoplay: 'Défilement automatique',
        interval: 'Intervalle (ms)',
        animation: 'Animation',
        radius: 'Arrondi',
        padding: 'Espacement'
    };

    const imageFields = new Set(['image', 'image2', 'image3']);
    const textareaFields = new Set([
        'subtitle', 'text', 'left', 'right', 'third'
    ]);

    let blocks = [];
    let selected = -1;
    let history = [];
    let future = [];
    let mediaTarget = null;

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, char => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;'
        })[char]);
    }

    function safeImage(value) {
        if (typeof value !== 'string' || !value) return '';

        if (/[\s<>"'`\\]/.test(value)) return '';

        try {
            if (value.startsWith('/') && !value.startsWith('//')) {
                return value;
            }

            const url = new URL(value);

            if (
                ['http:', 'https:'].includes(url.protocol) &&
                !url.username &&
                !url.password
            ) {
                return value;
            }
        } catch (_) {}

        return '';
    }

    function readInitial() {
        try {
            let data = JSON.parse($('#wb-initial')?.textContent || '[]');

            if (typeof data === 'string') {
                data = JSON.parse(data);
            }

            if (Array.isArray(data)) {
                blocks = data
                    .filter(item =>
                        item &&
                        types.has(item.type) &&
                        item.data &&
                        typeof item.data === 'object' &&
                        !Array.isArray(item.data)
                    )
                    .map(item => ({
                        type: item.type,
                        data: { ...item.data }
                    }));
            }
        } catch (error) {
            console.error('WebStudio : contenu initial invalide', error);
        }
    }

    const snapshot = () => JSON.stringify(blocks);

    function sync() {
        hidden.value = snapshot();
    }

    function commit() {
        history.push(snapshot());
        if (history.length > 80) history.shift();
        future = [];
    }

    function renderLibrary() {
        library.innerHTML = '';

        const term = (search?.value || '').trim().toLowerCase();

        definitions.forEach(([group, items]) => {
            const visible = items.filter(([, name]) =>
                name.toLowerCase().includes(term) ||
                group.toLowerCase().includes(term)
            );

            if (!visible.length) return;

            const section = document.createElement('section');
            section.className = 'wb2-category';

            const heading = document.createElement('h3');
            heading.className = 'wb2-category-title';
            heading.textContent = group;

            const grid = document.createElement('div');
            grid.className = 'wb2-category-grid';

            visible.forEach(([type, name, icon]) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'wb2-library-item';
                button.draggable = true;

                button.innerHTML = `
                    <span class="wb2-library-icon">${escapeHtml(icon)}</span>
                    <span class="wb2-library-name">${escapeHtml(name)}</span>
                `;

                button.addEventListener('click', () => add(type));

                button.addEventListener('dragstart', event => {
                    event.dataTransfer.setData('text/plain', 'new:' + type);
                    event.dataTransfer.effectAllowed = 'copy';
                });

                grid.appendChild(button);
            });

            section.append(heading, grid);
            library.appendChild(section);
        });

        if (!library.children.length) {
            library.innerHTML = '<p class="wb2-muted">Aucun composant trouvé.</p>';
        }
    }

    function add(type, index = blocks.length) {
        if (!types.has(type)) return;

        commit();

        blocks.splice(index, 0, {
            type,
            data: { ...(defaults[type] || {}) }
        });

        selected = index;
        render();
    }

    function move(from, to) {
        if (
            from < 0 ||
            from >= blocks.length ||
            to < 0 ||
            to >= blocks.length ||
            from === to
        ) return;

        commit();

        const [item] = blocks.splice(from, 1);
        blocks.splice(to, 0, item);

        selected = to;
        render();
    }

    function preview(block) {
        const d = block.data || {};
        const type = block.type;

        const title = escapeHtml(d.title || d.caption || '');
        const text = escapeHtml(d.subtitle || d.text || '');
        const image = safeImage(d.image);

        if (['hero', 'cta', 'carousel', 'banner_slider'].includes(type)) {
            const headline = type === 'carousel'
                ? escapeHtml(d.caption || d.title || '')
                : title;

            return `
                <div class="wb2-preview-hero">
                    ${image
                        ? `<img src="${escapeHtml(image)}" alt="">`
                        : '<div class="wb2-preview-placeholder">Choisissez une image dans la médiathèque</div>'
                    }
                    <div class="wb2-preview-copy">
                        <strong>${headline || 'Votre bannière'}</strong>
                        <p>${text}</p>
                    </div>
                </div>
            `;
        }

        if (type === 'image' || type === 'gallery') {
            const images = type === 'gallery'
                ? ['image', 'image2', 'image3']
                    .map(key => safeImage(d[key]))
                    .filter(Boolean)
                : [image].filter(Boolean);

            return images.length
                ? `<div class="wb2-preview-gallery">${images.map(src =>
                    `<img src="${escapeHtml(src)}" alt="">`
                ).join('')}</div>`
                : '<div class="wb2-preview-placeholder">Ajoutez une image</div>';
        }

        if (type === 'heading') {
            return `<h2>${title || 'Votre titre'}</h2>`;
        }

        if (type === 'text') {
            return `<p class="wb2-preview-text">${text || 'Votre texte'}</p>`;
        }

        if (type === 'button') {
            return `<span class="wb2-preview-button">${escapeHtml(d.label || 'Bouton')} →</span>`;
        }

        if (type === 'label') {
            return `<span class="wb2-preview-label">${escapeHtml(d.label || 'Étiquette')}</span>`;
        }

        if (type === 'divider') return '<hr class="wb2-preview-divider">';

        if (type === 'spacer') {
            const height = Math.min(200, Math.max(8, Number(d.height) || 48));
            return `<div class="wb2-preview-spacer" style="height:${height}px">Espacement</div>`;
        }

        if (type === 'columns') {
            return `<div class="wb2-preview-grid two">
                <div>${escapeHtml(d.left)}</div>
                <div>${escapeHtml(d.right)}</div>
            </div>`;
        }

        if (type === 'cards' || type === 'stats') {
            return `<h3>${title}</h3>
                <div class="wb2-preview-grid three">
                    <div>${escapeHtml(d.left)}</div>
                    <div>${escapeHtml(d.right)}</div>
                    <div>${escapeHtml(d.third)}</div>
                </div>`;
        }

        if (type === 'quote' || type === 'testimonials') {
            return `<blockquote class="wb2-preview-quote">
                “${escapeHtml(d.text || d.left || 'Votre témoignage')}”
            </blockquote>`;
        }

        if (type === 'faq') {
            return `<div class="wb2-preview-faq">
                <strong>${title || 'Questions fréquentes'}</strong>
                <p>${escapeHtml(d.left)}</p>
                <small>${escapeHtml(d.right)}</small>
            </div>`;
        }

        if (type === 'video') {
            return `<div class="wb2-preview-video">
                <span>▶</span>
                <strong>${title || 'Votre vidéo'}</strong>
                <small>${escapeHtml(d.url || 'Ajoutez un lien YouTube ou Vimeo')}</small>
            </div>`;
        }

        return `<div class="wb2-preview-generic">${title || escapeHtml(types.get(type)?.label)}</div>`;
    }

    function renderCanvas() {
        canvas.innerHTML = '';

        if (!blocks.length) {
            canvas.innerHTML = `
                <div class="wb2-empty-state">
                    <span>✦</span>
                    <h3>Commencez votre création</h3>
                    <p>Sélectionnez un composant dans la bibliothèque.</p>
                </div>
            `;
            return;
        }

        blocks.forEach((block, index) => {
            const element = document.createElement('section');

            element.className = 'wb2-editor-block' +
                (selected === index ? ' is-selected' : '');

            element.draggable = true;
            element.dataset.index = index;

            element.innerHTML = `
                <div class="wb2-editor-block-head">
                    <span class="wb2-editor-block-title">
                        <span class="wb2-drag-handle">⠿</span>
                        ${escapeHtml(types.get(block.type)?.label || block.type)}
                    </span>
                    <div class="wb2-editor-block-actions">
                        <button type="button" data-action="up" title="Monter">↑</button>
                        <button type="button" data-action="down" title="Descendre">↓</button>
                        <button type="button" data-action="duplicate" title="Dupliquer">⧉</button>
                        <button type="button" data-action="delete" title="Supprimer">×</button>
                    </div>
                </div>
                <div class="wb2-editor-block-preview">
                    ${preview(block)}
                </div>
            `;

            element.addEventListener('click', () => {
                selected = index;
                render();
            });

            element.querySelectorAll('[data-action]').forEach(button => {
                button.addEventListener('click', event => {
                    event.stopPropagation();

                    const action = button.dataset.action;

                    if (action === 'up') move(index, index - 1);
                    if (action === 'down') move(index, index + 1);

                    if (action === 'duplicate') {
                        commit();
                        blocks.splice(index + 1, 0, structuredClone(block));
                        selected = index + 1;
                        render();
                    }

                    if (action === 'delete') {
                        commit();
                        blocks.splice(index, 1);
                        selected = Math.min(index, blocks.length - 1);
                        render();
                    }
                });
            });

            element.addEventListener('dragstart', event => {
                event.dataTransfer.setData('text/plain', 'move:' + index);
            });

            element.addEventListener('dragover', event => {
                event.preventDefault();
            });

            element.addEventListener('drop', event => {
                event.preventDefault();
                event.stopPropagation();

                const value = event.dataTransfer.getData('text/plain');

                if (value.startsWith('new:')) {
                    add(value.slice(4), index);
                } else if (value.startsWith('move:')) {
                    move(Number(value.slice(5)), index);
                }
            });

            canvas.appendChild(element);
        });
    }

    function renderInspector() {
        inspector.innerHTML = `
            <div class="wb2-panel-head">
                <span class="wb2-eyebrow">PERSONNALISATION</span>
                <h2>Propriétés</h2>
            </div>
        `;

        if (selected < 0 || !blocks[selected]) {
            inspector.innerHTML += `
                <p class="wb2-muted">
                    Sélectionnez un composant pour modifier ses propriétés.
                </p>
            `;
            return;
        }

        const block = blocks[selected];

        const componentName = document.createElement('div');
        componentName.className = 'wb2-inspector-component';
        componentName.textContent = types.get(block.type)?.label || block.type;
        inspector.appendChild(componentName);

        const relevant = new Set([
            ...Object.keys(defaults[block.type] || {}),
            ...Object.keys(block.data || {})
        ]);

        if (['hero', 'cta', 'image', 'gallery', 'carousel', 'banner_slider'].includes(block.type)) {
            relevant.add('image');
        }

        if (['gallery', 'carousel', 'banner_slider'].includes(block.type)) {
            relevant.add('image2');
            relevant.add('image3');
        }

        if (block.type === 'testimonials') {
            ['left', 'right', 'third', 'author', 'author2', 'author3']
                .forEach(field => relevant.add(field));
        }

        if (block.type === 'carousel') {
            ['caption', 'caption2', 'caption3'].forEach(field => relevant.add(field));
        }

        if (block.type === 'banner_slider') {
            ['title', 'title2', 'title3'].forEach(field => relevant.add(field));
        }

        if (['carousel', 'banner_slider', 'testimonials'].includes(block.type)) {
            relevant.add('autoplay');
            relevant.add('interval');
        }

        relevant.add('animation');

        if (['hero', 'cta', 'heading', 'text', 'button', 'label'].includes(block.type)) {
            relevant.add('color');
        }

        if (['hero', 'cta', 'button', 'label'].includes(block.type)) {
            relevant.add('background');
        }

        const orderedFields = [
            ...fieldOrder.filter(field => relevant.has(field)),
            ...[...relevant].filter(field => !fieldOrder.includes(field))
        ];

        orderedFields.forEach(field => {
            const wrap = document.createElement('label');
            wrap.className = 'wb2-field';

            const caption = document.createElement('span');
            caption.className = 'wb2-field-label';
            caption.textContent = labels[field] || field;

            wrap.appendChild(caption);

            let input;

            if (field === 'autoplay' || field === 'animation') {
                input = document.createElement('select');

                const options = field === 'autoplay'
                    ? [['non', 'Non'], ['oui', 'Oui']]
                    : [
                        ['', 'Aucune'],
                        ['fade', 'Fondu'],
                        ['fade-up', 'Fondu vers le haut'],
                        ['slide-up', 'Glissement vers le haut'],
                        ['slide-left', 'Depuis la gauche'],
                        ['slide-right', 'Depuis la droite'],
                        ['zoom', 'Zoom']
                    ];

                options.forEach(([value, text]) => {
                    const option = document.createElement('option');
                    option.value = value;
                    option.textContent = text;
                    input.appendChild(option);
                });
            } else if (textareaFields.has(field)) {
                input = document.createElement('textarea');
                input.rows = 3;
            } else {
                input = document.createElement('input');
                input.type = 'text';
            }

            input.value = String(block.data[field] ?? '');

            let tracked = false;

            input.addEventListener('focus', () => {
                if (!tracked) {
                    commit();
                    tracked = true;
                }
            });

            input.addEventListener('blur', () => {
                tracked = false;
            });

            input.addEventListener('input', () => {
                block.data[field] = input.value;
                renderCanvas();
                sync();
            });

            input.addEventListener('change', () => {
                block.data[field] = input.value;
                renderCanvas();
                sync();
            });

            if (imageFields.has(field)) {
                input.readOnly = true;

                const choose = document.createElement('button');
                choose.type = 'button';
                choose.className = 'wb2-media-pick';
                choose.textContent = '📷 Choisir / importer une image';

                choose.addEventListener('click', () => openMedia(field));

                wrap.append(input, choose);
            } else {
                wrap.appendChild(input);
            }

            inspector.appendChild(wrap);
        });
    }

    function render() {
        renderCanvas();
        renderInspector();
        sync();
    }

    function undo() {
        if (!history.length) return;

        future.push(snapshot());
        blocks = JSON.parse(history.pop());
        selected = -1;
        render();
    }

    function redo() {
        if (!future.length) return;

        history.push(snapshot());
        blocks = JSON.parse(future.pop());
        selected = -1;
        render();
    }

    $('#wb-undo')?.addEventListener('click', undo);
    $('#wb-redo')?.addEventListener('click', redo);

    search?.addEventListener('input', renderLibrary);

    canvas.addEventListener('dragover', event => event.preventDefault());

    canvas.addEventListener('drop', event => {
        if (event.target.closest('.wb2-editor-block')) return;

        event.preventDefault();

        const value = event.dataTransfer.getData('text/plain');

        if (value.startsWith('new:')) add(value.slice(4));
        else if (value.startsWith('move:')) {
            move(Number(value.slice(5)), blocks.length - 1);
        }
    });

    document.querySelectorAll('[data-device]').forEach(button => {
        button.addEventListener('click', () => {
            document.querySelectorAll('[data-device]').forEach(item => {
                item.classList.toggle('active', item === button);
            });

            canvas.dataset.device = button.dataset.device;

            const deviceLabel = $('#wb-device-label');

            if (deviceLabel) {
                deviceLabel.textContent = '100% · ' + ({
                    desktop: 'Ordinateur',
                    tablet: 'Tablette',
                    mobile: 'Mobile'
                }[button.dataset.device] || 'Ordinateur');
            }
        });
    });

    form.addEventListener('submit', sync);

    const modal = $('#wb-media-modal');
    const grid = $('#wb-media-grid');
    const message = $('#wb-media-message');

    function openMedia(field) {
        mediaTarget = field;
        if (!modal) return;
        modal.hidden = false;
        loadMedia();
    }

    function closeMedia() {
        if (modal) modal.hidden = true;
        mediaTarget = null;
    }

    $('#wb-media-close')?.addEventListener('click', closeMedia);

    modal?.addEventListener('click', event => {
        if (event.target === modal) closeMedia();
    });

    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && modal && !modal.hidden) {
            closeMedia();
        }
    });

    async function loadMedia() {
        if (!grid) return;

        grid.textContent = 'Chargement des images…';

        try {
            const response = await fetch(root.dataset.mediaList, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });

            if (!response.ok) throw new Error('Chargement impossible.');

            const payload = await response.json();

            const items = Array.isArray(payload)
                ? payload
                : (payload.data || payload.media?.data || []);

            grid.innerHTML = '';

            items.forEach(item => {
                const url = item.url || item.image_url;
                const validUrl = safeImage(url);

                if (!validUrl) return;

                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'wb2-media-item';

                const img = document.createElement('img');
                img.src = validUrl;
                img.alt = item.original_name || 'Image';
                img.loading = 'lazy';

                button.appendChild(img);

                button.addEventListener('click', () => {
                    if (selected >= 0 && blocks[selected] && mediaTarget) {
                        commit();
                        blocks[selected].data[mediaTarget] = validUrl;
                        render();
                    }
                    closeMedia();
                });

                grid.appendChild(button);
            });

            if (!grid.children.length) {
                grid.textContent = 'Aucune image disponible.';
            }
        } catch (error) {
            grid.textContent = error.message;
        }
    }

    $('#wb-media-upload')?.addEventListener('change', async event => {
        const file = event.target.files?.[0];
        if (!file) return;

        const data = new FormData();
        data.append('image', file);

        const csrf = form.querySelector('input[name="_token"]')?.value || '';

        try {
            if (message) message.textContent = 'Importation en cours…';

            const response = await fetch(root.dataset.mediaUpload, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: data,
                credentials: 'same-origin'
            });

            if (!response.ok) throw new Error('Importation impossible.');

            if (message) message.textContent = 'Image importée avec succès.';
            await loadMedia();
        } catch (error) {
            if (message) message.textContent = error.message;
        }

        event.target.value = '';
    });

    readInitial();
    renderLibrary();
    render();
});

const WB_ANIMATION_DEFAULTS = {
    animation: 'fade-up',
    duration: '700',
    delay: '0',
    parallax: 'non',
    hover: 'oui'
};

const WB_ANIMATION_OPTIONS = [
    { value: 'none', label: 'Aucune animation' },
    { value: 'fade', label: 'Apparition douce' },
    { value: 'fade-up', label: 'Apparition du bas' },
    { value: 'slide-up', label: 'Glissement vertical' },
    { value: 'slide-left', label: 'Depuis la gauche' },
    { value: 'slide-right', label: 'Depuis la droite' },
    { value: 'zoom', label: 'Zoom avant' },
    { value: 'zoom-out', label: 'Zoom arrière' },
    { value: 'rotate', label: 'Rotation légère' }
];