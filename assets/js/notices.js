function showNotice(notice, message, type = notice.dataset.type) {
    const content = notice.querySelector('[data-notice-message]');
    notice.dataset.type = type;
    content.setAttribute('role', type === 'error' ? 'alert' : 'status');
    notice.hidden = false;
    content.textContent = message;
}

function hideNotice(notice) {
    notice.hidden = true;
    notice.querySelector('[data-notice-message]').textContent = '';
}

document.addEventListener('click', (event) => {
    const dismiss = event.target.closest('[data-notice-dismiss]');

    if (dismiss) {
        hideNotice(dismiss.closest('[data-notice]'));
    }
});
