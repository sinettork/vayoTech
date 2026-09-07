import './bootstrap';
import * as bootstrap from 'bootstrap';
import '@fortawesome/fontawesome-free/css/all.min.css';
import '../css/device-cards.css';
import '../css/compare.css';

window.bootstrap = bootstrap;

document.querySelectorAll('[data-share-device]').forEach((button) => {
    button.addEventListener('click', async () => {
        const title = button.dataset.shareTitle;
        const url = button.dataset.shareUrl;
        const status = button.parentElement.querySelector('[data-share-status]');

        try {
            if (navigator.share) {
                await navigator.share({ title, url });
            } else {
                await navigator.clipboard.writeText(url);
                status.textContent = 'Link copied';
                window.setTimeout(() => {
                    status.textContent = '';
                }, 2000);
            }
        } catch (error) {
            if (error.name !== 'AbortError') {
                status.textContent = 'Unable to share this link';
            }
        }
    });
});
