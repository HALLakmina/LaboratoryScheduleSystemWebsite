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

export { escapeHtml, truncateText, buildAppUrl };
