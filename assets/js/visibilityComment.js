window.onload = () => {
    let swtichs = document.querySelectorAll('[data-switch-enabled]');
    if (swtichs) {
        swtichs.forEach((element) => {
            element.addEventListener('change', () => {
                let tagId = element.value;
                sendRequest(tagId);
            })
        })
    }
}

async function sendRequest(id) {
    await fetch(`/admin/article/comments/${id}/switch`)
}