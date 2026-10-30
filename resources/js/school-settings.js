
document.addEventListener('DOMContentLoaded', () => {
    const inputs = document.querySelectorAll(
        '.school-settings-page input[type="file"][data-preview]'
    );

    inputs.forEach((input) => {
        let previousUrl = null;

        input.addEventListener('change', () => {
            const file = input.files?.[0];
            const previewId = input.dataset.preview;
            const preview = document.getElementById(previewId);

            if (!preview || !file) return;

            if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
                alert('Veuillez sélectionner une image JPG, PNG ou WEBP.');
                input.value = '';
                return;
            }

            if (file.size > 2 * 1024 * 1024) {
                alert('La taille maximale autorisée est de 2 Mo.');
                input.value = '';
                return;
            }

            if (previousUrl) {
                URL.revokeObjectURL(previousUrl);
            }

            previousUrl = URL.createObjectURL(file);

            preview.src = previousUrl;
            preview.hidden = false;
        });
    });
});
