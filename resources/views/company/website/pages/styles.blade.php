<style>
    /* WebStudio Pages : styles isolés pour éviter les conflits avec le layout. */
    .wp-wrap {
        --wp-blue: #4f46e5;
        --wp-border: #e2e8f0;
        --wp-muted: #64748b;
        --wp-ink: #172033;
        width: 100%;
        max-width: 1450px;
        margin: 0 auto;
        color: var(--wp-ink);
    }
    .wp-wrap *, .wp-wrap *::before, .wp-wrap *::after { box-sizing: border-box; }
    .wp-toolbar { display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap; margin-bottom: 22px; }
    .wp-search { display: flex; gap: 8px; flex-wrap: wrap; }
    .wp-wrap .wp-search input,
    .wp-wrap .wp-form input:not([type="checkbox"]),
    .wp-wrap .wp-form textarea,
    .wp-wrap .wp-form select {
        display: block; width: 100%; min-width: 0; border: 1px solid var(--wp-border);
        border-radius: 10px; padding: 12px 13px; font: inherit;
        background: #fff; color: var(--wp-ink);
    }
    .wp-wrap .wp-form input:focus,
    .wp-wrap .wp-form textarea:focus,
    .wp-wrap .wp-form select:focus {
        outline: 2px solid #c7d2fe; outline-offset: 1px; border-color: var(--wp-blue);
    }
    .wp-search input { width: min(320px, 75vw); }
    .wp-wrap .wp-search button, .wp-wrap .wp-btn {
        display: inline-flex; align-items: center; justify-content: center;
        border: 0; border-radius: 10px; padding: 12px 18px;
        background: var(--wp-blue); color: #fff !important;
        font: inherit; font-weight: 700; cursor: pointer; text-decoration: none;
    }
    .wp-wrap .wp-btn:hover { background: #4338ca; }
    .wp-table-wrap { background: #fff; border: 1px solid var(--wp-border); border-radius: 16px; overflow-x: auto; }
    .wp-table { width: 100%; border-collapse: collapse; min-width: 720px; }
    .wp-table th, .wp-table td { text-align: left; padding: 17px; border-bottom: 1px solid #f1f5f9; font-size: 13px; }
    .wp-table th { color: var(--wp-muted); font-size: 12px; background: #f8fafc; }
    .wp-table td small { display: block; color: var(--wp-muted); margin-top: 5px; }
    .wp-pill { display: inline-block; padding: 6px 10px; border-radius: 30px; background: #f1f5f9; color: #64748b; font-size: 11px; font-weight: 700; }
    .wp-pill.is-live { background: #dcfce7; color: #166534; }
    .wp-actions { display: flex; align-items: center; gap: 12px; }
    .wp-actions a { color: var(--wp-blue); font-weight: 700; }
    .wp-actions button { background: none; border: 0; color: #dc2626; cursor: pointer; font: inherit; }
    .wp-empty { text-align: center !important; color: var(--wp-muted); padding: 45px !important; }
    .wp-pagination { margin-top: 20px; }
    .wp-form-grid { display: grid; grid-template-columns: minmax(0, 2fr) minmax(280px, 1fr); gap: 22px; align-items: start; }
    .wp-panel { min-width: 0; background: #fff; border: 1px solid var(--wp-border); border-radius: 16px; padding: 26px; box-shadow: 0 4px 18px rgba(15, 23, 42, .035); }
    .wp-panel-heading { padding-bottom: 17px; margin-bottom: 18px; border-bottom: 1px solid #eef2f7; }
    .wp-panel-heading h2 { margin: 0 0 6px; font-size: 18px; font-weight: 750; }
    .wp-panel-heading p { margin: 0; color: var(--wp-muted); font-size: 13px; line-height: 1.6; }
    .wp-panel label:not(.wp-check) { display: block; margin: 18px 0 8px; font-size: 13px; font-weight: 700; }
    .wp-panel .wp-panel-heading + label { margin-top: 0; }
    .wp-panel textarea { resize: vertical; }
    .wp-hint { font-size: 12px; color: var(--wp-muted); line-height: 1.5; margin: 7px 0; }
    .wp-required { color: #dc2626; }
    .wp-field-error, .wp-wrap .field-error { display: block; margin-top: 6px; color: #b91c1c; font-size: 12px; }
    .wp-check { display: flex; align-items: center; gap: 10px; margin-top: 23px; font-size: 13px; cursor: pointer; }
    .wp-wrap .wp-form .wp-check input[type="checkbox"] { width: 17px; height: 17px; flex: 0 0 17px; accent-color: var(--wp-blue); }
    .wp-form-actions { display: flex; flex-direction: column; gap: 12px; margin-top: 28px; }
    .wp-form-actions .wp-btn { width: 100%; }
    .wp-form-actions a { text-align: center; text-decoration: none; }
    .wp-form-actions .wp-cancel { padding: 9px; color: var(--wp-muted); font-weight: 600; }
    .wp-form-actions .website-preview-button {
        display: inline-flex; align-items: center; justify-content: center;
        padding: 12px 16px; background: #eff6ff; color: #1d4ed8;
        border: 1px solid #bfdbfe; border-radius: 10px;
        font-size: 13px; font-weight: 700; transition: background .2s;
    }
    .wp-form-actions .website-preview-button:hover { background: #dbeafe; }
    .wp-alert { padding: 14px 18px; margin-bottom: 18px; border-radius: 12px; font-size: 13px; }
    .wp-alert-error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
    .wp-alert ul { margin: 8px 0 0; padding-left: 22px; }
    @media (max-width: 850px) { .wp-form-grid { grid-template-columns: 1fr; } }
    @media (max-width: 600px) {
        .wp-panel { padding: 18px; }
        .wp-toolbar { align-items: stretch; }
        .wp-search { width: 100%; }
        .wp-search input { flex: 1; width: 0; }
        .wp-toolbar > .wp-btn { width: 100%; }
    }
</style>
