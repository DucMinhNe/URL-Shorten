/* LinkPay Pro — shared front-end micro-interactions (vanilla, no build) */
(function () {
    'use strict';
    const LP = window.LP = window.LP || {};

    /* ---------- Toasts ---------------------------------------------------- */
    const ICONS = {
        ok:     '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>',
        err:    '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>',
        info:   '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/><circle cx="12" cy="12" r="10"/></svg>',
        reward: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15 9 22 9 17 14 19 21 12 17 5 21 7 14 2 9 9 9"/></svg>',
    };
    function wrap() {
        let w = document.querySelector('.lp-toasts');
        if (!w) { w = document.createElement('div'); w.className = 'lp-toasts'; document.body.appendChild(w); }
        return w;
    }
    LP.toast = function (type, title, msg, ttl) {
        type = ICONS[type] ? type : 'info';
        const el = document.createElement('div');
        el.className = 'lp-toast ' + type;
        el.innerHTML = '<span class="ic">' + ICONS[type] + '</span>'
            + '<div class="body"><div class="ttl"></div>' + (msg ? '<div class="msg"></div>' : '') + '</div>'
            + '<button class="x" aria-label="Đóng">&times;</button>';
        el.querySelector('.ttl').textContent = title || '';
        if (msg) el.querySelector('.msg').textContent = msg;
        wrap().appendChild(el);
        requestAnimationFrame(() => el.classList.add('show'));
        const close = () => { el.classList.remove('show'); setTimeout(() => el.remove(), 450); };
        el.querySelector('.x').addEventListener('click', close);
        setTimeout(close, ttl || 4200);
        return el;
    };

    /* ---------- Animated counters ---------------------------------------- */
    function animateCount(el) {
        const target = parseFloat(el.getAttribute('data-count')) || 0;
        const dur = parseInt(el.getAttribute('data-dur') || '1100', 10);
        const suffix = el.getAttribute('data-suffix') || '';
        const prefix = el.getAttribute('data-prefix') || '';
        const dec = parseInt(el.getAttribute('data-dec') || '0', 10);
        const start = performance.now();
        function fmt(n) {
            return prefix + n.toLocaleString('vi-VN', { minimumFractionDigits: dec, maximumFractionDigits: dec }) + suffix;
        }
        function tick(now) {
            const p = Math.min(1, (now - start) / dur);
            const eased = 1 - Math.pow(1 - p, 3);
            el.textContent = fmt(target * eased);
            if (p < 1) requestAnimationFrame(tick); else el.textContent = fmt(target);
        }
        requestAnimationFrame(tick);
    }
    LP.initCounters = function (root) {
        (root || document).querySelectorAll('[data-count]:not([data-counted])').forEach(el => {
            el.setAttribute('data-counted', '1');
            const io = new IntersectionObserver((entries) => {
                entries.forEach(e => { if (e.isIntersecting) { animateCount(el); io.disconnect(); } });
            }, { threshold: 0.3 });
            io.observe(el);
        });
    };

    /* ---------- Copy to clipboard ---------------------------------------- */
    LP.copy = function (text, label) {
        const done = () => LP.toast('ok', label || 'Đã sao chép', text.length > 42 ? text.slice(0, 42) + '…' : text);
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(done).catch(() => fallback(text, done));
        } else { fallback(text, done); }
    };
    function fallback(text, done) {
        const t = document.createElement('textarea'); t.value = text; t.style.position = 'fixed'; t.style.opacity = '0';
        document.body.appendChild(t); t.select();
        try { document.execCommand('copy'); done(); } catch (e) {}
        t.remove();
    }
    document.addEventListener('click', (e) => {
        const c = e.target.closest('[data-copy]');
        if (c) { e.preventDefault(); LP.copy(c.getAttribute('data-copy'), c.getAttribute('data-copy-label')); }
    });

    /* ---------- Reward popup (interstitial) ------------------------------- */
    LP.reward = function (text) {
        const el = document.createElement('div');
        el.className = 'lp-reward-pop'; el.textContent = text;
        document.body.appendChild(el);
        requestAnimationFrame(() => el.classList.add('go'));
        setTimeout(() => el.remove(), 2000);
    };

    /* ---------- Auto-init + flash toasts ---------------------------------- */
    document.addEventListener('DOMContentLoaded', () => {
        LP.initCounters();
        const f = window.__LP_FLASH;
        if (f && f.title) LP.toast(f.type || 'info', f.title, f.msg || '');
    });
})();
