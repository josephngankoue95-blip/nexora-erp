<script>
document.addEventListener('DOMContentLoaded', function () {
    const search = document.getElementById('productSearch');
    const items = Array.from(document.querySelectorAll('.ecol-product-item'));
    const count = document.getElementById('selectedCount');
    function refreshCount() {
        const selected = items.filter(item => item.querySelector('input[type="checkbox"]').checked).length;
        if (count) count.textContent = selected + ' sélectionné(s)';
    }
    if (search) search.addEventListener('input', function () {
        const term = this.value.toLocaleLowerCase('fr').trim();
        items.forEach(item => {
            item.hidden = !item.dataset.search.toLocaleLowerCase('fr').includes(term);
        });
    });
    items.forEach(item => item.querySelector('input[type="checkbox"]').addEventListener('change', refreshCount));
    document.getElementById('selectVisible')?.addEventListener('click', () => {
        items.filter(item => !item.hidden).forEach(item => item.querySelector('input[type="checkbox"]').checked = true);
        refreshCount();
    });
    document.getElementById('clearProducts')?.addEventListener('click', () => {
        items.forEach(item => item.querySelector('input[type="checkbox"]').checked = false);
        refreshCount();
    });
    refreshCount();
});
</script>
