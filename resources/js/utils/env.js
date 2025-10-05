const normalizeUrl = (url) => {
    if (!url) {
        return '';
    }

    return url.endsWith('/') ? url.slice(0, -1) : url;
};

const getEnv = (key) => (typeof import.meta !== 'undefined' ? import.meta.env[key] : undefined);

const fallbackAppUrl = () => {
    if (typeof window !== 'undefined' && window.location) {
        return window.location.origin;
    }

    return '';
};

export const APP_URL = normalizeUrl(getEnv('VITE_APP_URL')) || fallbackAppUrl();
export const DRUPAL_URL = normalizeUrl(getEnv('VITE_DRUPAL_URL')) || APP_URL;
