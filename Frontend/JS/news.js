import { getNews } from '../API/newsApi.js';
import { buildAppUrl, escapeHtml, truncateText } from './utils.js';

const initNewsPage = async () => {
    const newsList = document.getElementById('news-list');
    const newsViewer = document.getElementById('news-viewer');
    const newsViewerClose = document.getElementById('news-viewer-close');
    const newsViewerImage = document.getElementById('news-viewer-image');
    const newsViewerTitle = document.getElementById('news-viewer-title');
    const newsViewerMeta = document.getElementById('news-viewer-meta');
    const newsViewerDescription = document.getElementById('news-viewer-description');

    if (!newsList || !newsViewer || !newsViewerClose || !newsViewerImage || !newsViewerTitle || !newsViewerMeta || !newsViewerDescription) {
        return;
    }

    const getImageUrl = (filePath) => (String(filePath || '').startsWith('http') ? filePath : buildAppUrl(filePath));

    const buildMetaText = (newsItem) => {
        const parts = [];
        if (newsItem.start_date) parts.push(`Start: ${newsItem.start_date}`);
        if (newsItem.end_date) parts.push(`End: ${newsItem.end_date}`);
        if (newsItem.start_at) parts.push(`From: ${newsItem.start_at}`);
        if (newsItem.end_at) parts.push(`To: ${newsItem.end_at}`);
        return parts.join(' | ');
    };

    const openNewsViewer = (newsItem) => {
        newsViewerImage.src = getImageUrl(newsItem.file_path);
        newsViewerImage.alt = newsItem.title || 'news image';
        newsViewerTitle.textContent = newsItem.title || 'Untitled News';
        newsViewerMeta.textContent = buildMetaText(newsItem);
        newsViewerDescription.textContent = newsItem.description || 'No description available.';
        newsViewer.classList.remove('hidden');
    };

    newsViewerClose.addEventListener('click', () => {
        newsViewer.classList.add('hidden');
    });

    try {
        const response = await getNews();
        const newsItems = response.status === '200' && Array.isArray(response.data) ? response.data : [];

        if (newsItems.length === 0) {
            newsList.innerHTML = '<div class="w-full bg-white rounded-lg p-6 text-center font-bold text-gray-600">No news available.</div>';
            return;
        }

        newsList.innerHTML = newsItems.map((newsItem) => `
            <button
                type="button"
                class="image-card w-sm md:w-md rounded-lg bg-white p-2 m-2 hover:bg-gray-200 active:bg-blue-200 text-left"
                data-news-id="${escapeHtml(newsItem.id)}"
            >
                <img
                    src="${escapeHtml(getImageUrl(newsItem.file_path))}"
                    alt="${escapeHtml(newsItem.title || 'news image')}"
                    class="w-full min-h-[200px] h-[220px] object-cover rounded-sm bg-gray-200"
                />
                <p class="text-lg font-bold pt-2">${escapeHtml(newsItem.title || 'Untitled News')}</p>
                <p class="text-sm text-gray-600 pb-2">${escapeHtml(buildMetaText(newsItem))}</p>
                <p class="w-full min-h-[100px] h-full overflow-y-scroll" style="scrollbar-width: none;">
                    ${escapeHtml(truncateText(newsItem.description, 180) || 'No description available.')}
                </p>
            </button>
        `).join('');

        newsList.addEventListener('click', (event) => {
            const card = event.target.closest('.image-card');
            if (!card) return;

            const selectedNews = newsItems.find((item) => String(item.id) === String(card.getAttribute('data-news-id')));
            if (!selectedNews) return;

            openNewsViewer(selectedNews);
        });
    } catch (error) {
        console.error('Error loading news page:', error);
        newsList.innerHTML = '<div class="w-full bg-white rounded-lg p-6 text-center font-bold text-red-600">Failed to load news.</div>';
    }
};

export { initNewsPage };
