// NEXORA — Interactions légères de la feuille d'appel (sans dépendance)
document.addEventListener('DOMContentLoaded', () => {
  const root = document.querySelector('.sa-page');
  if (!root) return;
  const year = root.querySelector('#sa-year');
  if (year) year.addEventListener('change', () => {
    const form = year.closest('form');
    const cls = form?.querySelector('[name="class_id"]');
    if (cls) cls.value = '';
    form?.submit();
  });
  const rows = Array.from(root.querySelectorAll('.sa-student-row'));
  const search = root.querySelector('#sa-student-search');
  const empty = root.querySelector('.sa-search-empty');
  search?.addEventListener('input', () => {
    const q = search.value.trim().toLocaleLowerCase();
    let visible = 0;
    rows.forEach(row => {
      const match = (row.dataset.student || '').includes(q);
      row.hidden = !match;
      if (match) visible++;
    });
    if (empty) empty.hidden = visible > 0 || !q;
  });
  root.querySelectorAll('.sa-status').forEach(select => {
    const delay = select.closest('tr')?.querySelector('.sa-delay');
    const refresh = () => {
      select.dataset.status = select.value;
      if (delay && !delay.disabled) {
        if (select.value !== 'late') delay.value = '';
        delay.disabled = select.value !== 'late';
      }
    };
    // Initialiser le style sans effacer la valeur du serveur.
    select.dataset.status = select.value;
    if (delay && !delay.disabled) delay.disabled = select.value !== 'late';
    select.addEventListener('change', refresh);
  });
});
