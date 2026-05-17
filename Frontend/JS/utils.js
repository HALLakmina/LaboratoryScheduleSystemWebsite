const escapeHtml = (value) => String(value ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');

const truncateText = (text, maxLength = 180) => {
    const safeText = String(text || '').trim();
    if (safeText.length <= maxLength) {
        return safeText;
    }

    return `${safeText.slice(0, maxLength).trim()}...`;
};

const buildAppUrl = (path = '') => {
    if (!path) return '';
    const normalizedPath = String(path).replace(/^\/+/, '');
    return `http://localhost/LaboratoryScheduleSystemWebsite/${normalizedPath}`;
};

let activeRequestCount = 0;
let isFetchInterceptorInstalled = false;

const ensureLoadingStyles = () => {
    if (document.getElementById('global-loading-style')) return;

    const style = document.createElement('style');
    style.id = 'global-loading-style';
    style.textContent = `
        @keyframes lss-spin {
            to { transform: rotate(360deg); }
        }

        .lss-loading-hidden {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }

        .lss-loading-visible {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }

        .lss-loading-spinner {
            width: 1.25rem;
            height: 1.25rem;
            border-radius: 9999px;
            border: 3px solid rgba(255,255,255,0.22);
            border-top-color: #38bdf8;
            animation: lss-spin 0.8s linear infinite;
        }
    `;

    document.head.appendChild(style);
};

const ensureGlobalLoadingElements = () => {
    ensureLoadingStyles();

    if (!document.body) return;

    if (!document.getElementById('page-loading-overlay')) {
        const pageOverlay = document.createElement('div');
        pageOverlay.id = 'page-loading-overlay';
        pageOverlay.className = 'lss-loading-visible fixed inset-0 z-[9999] flex items-center justify-center bg-slate-950/88 px-4 transition-opacity duration-300';
        pageOverlay.innerHTML = `
            <div class="flex w-full max-w-xs flex-col items-center gap-4 rounded-3xl border border-white/10 bg-slate-900/90 px-6 py-8 text-center text-white shadow-2xl backdrop-blur">
                <div class="lss-loading-spinner h-8 w-8"></div>
                <div class="space-y-1">
                    <p class="text-sm font-black uppercase tracking-[0.24em] text-sky-300">Loading</p>
                    <p class="text-sm text-slate-200">Preparing the page...</p>
                </div>
            </div>
        `;
        document.body.appendChild(pageOverlay);
    }

    if (!document.getElementById('request-loading-overlay')) {
        const requestOverlay = document.createElement('div');
        requestOverlay.id = 'request-loading-overlay';
        requestOverlay.className = 'lss-loading-hidden fixed inset-0 z-[9998] flex items-end justify-center bg-slate-950/45 px-3 pb-6 pt-20 transition-opacity duration-200 sm:items-center sm:px-4 sm:pb-4';
        requestOverlay.innerHTML = `
            <div class="flex w-full max-w-sm items-center gap-3 rounded-2xl border border-white/10 bg-slate-900/95 px-4 py-4 text-white shadow-2xl backdrop-blur">
                <div class="lss-loading-spinner shrink-0"></div>
                <div class="min-w-0">
                    <p class="text-xs font-black uppercase tracking-[0.22em] text-sky-300">Please Wait</p>
                    <p id="request-loading-message" class="truncate text-sm text-slate-100">Processing request...</p>
                </div>
            </div>
        `;
        document.body.appendChild(requestOverlay);
    }
};

const hidePageLoader = () => {
    const pageOverlay = document.getElementById('page-loading-overlay');
    if (!pageOverlay) return;

    pageOverlay.classList.remove('lss-loading-visible');
    pageOverlay.classList.add('lss-loading-hidden');
};

const showPageLoader = (message = 'Loading page...') => {
    ensureGlobalLoadingElements();

    const pageOverlay = document.getElementById('page-loading-overlay');
    const messageEl = pageOverlay?.querySelector('p.text-sm.text-slate-200');
    if (messageEl) {
        messageEl.textContent = message;
    }

    if (pageOverlay) {
        pageOverlay.classList.remove('lss-loading-hidden');
        pageOverlay.classList.add('lss-loading-visible');
    }
};

const renderRequestLoaderState = (message = 'Processing request...') => {
    ensureGlobalLoadingElements();

    const requestOverlay = document.getElementById('request-loading-overlay');
    const messageEl = document.getElementById('request-loading-message');
    if (messageEl) {
        messageEl.textContent = message;
    }

    if (requestOverlay) {
        if (activeRequestCount > 0) {
            requestOverlay.classList.remove('lss-loading-hidden');
            requestOverlay.classList.add('lss-loading-visible');
        } else {
            requestOverlay.classList.remove('lss-loading-visible');
            requestOverlay.classList.add('lss-loading-hidden');
        }
    }
};

const beginRequestLoading = (message = 'Processing request...') => {
    activeRequestCount += 1;
    renderRequestLoaderState(message);

    let released = false;
    return () => {
        if (released) return;
        released = true;
        activeRequestCount = Math.max(0, activeRequestCount - 1);
        renderRequestLoaderState(message);
    };
};

const installFetchLoadingInterceptor = () => {
    if (isFetchInterceptorInstalled || typeof window.fetch !== 'function') return;

    const nativeFetch = window.fetch.bind(window);
    window.fetch = (...args) => {
        const release = beginRequestLoading();
        return nativeFetch(...args).finally(() => {
            release();
        });
    };

    isFetchInterceptorInstalled = true;
};

const initLoadingSystem = () => {
    ensureGlobalLoadingElements();
    installFetchLoadingInterceptor();

    const hideLoader = () => {
        window.setTimeout(hidePageLoader, 120);
    };

    if (document.readyState === 'complete') {
        hideLoader();
    } else {
        window.addEventListener('load', hideLoader, { once: true });
    }

    document.addEventListener('click', (event) => {
        const link = event.target.closest('a[href]');
        if (!link) return;

        if (event.defaultPrevented || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
        if (link.target && link.target !== '_self') return;

        const href = link.getAttribute('href') || '';
        if (!href || href.startsWith('#') || href.startsWith('javascript:') || href.startsWith('mailto:') || href.startsWith('tel:')) return;

        try {
            const url = new URL(link.href, window.location.href);
            if (url.origin !== window.location.origin) return;
        } catch (error) {
            return;
        }

        showPageLoader('Opening page...');
    });
};

const setElementBusyState = (element, isBusy, busyLabel = 'Please wait...') => {
    if (!element) return;

    if (isBusy) {
        if (!element.dataset.originalLabel) {
            element.dataset.originalLabel = element.tagName === 'INPUT'
                ? (element.value || '')
                : (element.innerHTML || '');
        }

        element.disabled = true;
        if (element.tagName === 'INPUT') {
            element.value = busyLabel;
        } else {
            element.innerHTML = busyLabel;
        }
        element.classList.add('opacity-70', 'cursor-not-allowed');
        return;
    }

    element.disabled = false;
    if (element.dataset.originalLabel) {
        if (element.tagName === 'INPUT') {
            element.value = element.dataset.originalLabel;
        } else {
            element.innerHTML = element.dataset.originalLabel;
        }
    }
    delete element.dataset.originalLabel;
    element.classList.remove('opacity-70', 'cursor-not-allowed');
};

const bindAsyncFormSubmit = (form, handler, options = {}) => {
    if (!form) return;

    const busyLabel = options.busyLabel || 'Processing...';

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        if (form.dataset.submitting === 'true') {
            return;
        }

        form.dataset.submitting = 'true';
        const submitElements = Array.from(form.querySelectorAll('button[type="submit"], input[type="submit"]'));
        submitElements.forEach((element) => setElementBusyState(element, true, busyLabel));

        try {
            await handler(event);
        } finally {
            form.dataset.submitting = 'false';
            submitElements.forEach((element) => setElementBusyState(element, false, busyLabel));
        }
    });
};

export {
    escapeHtml,
    truncateText,
    buildAppUrl,
    initLoadingSystem,
    bindAsyncFormSubmit,
    showPageLoader,
};
