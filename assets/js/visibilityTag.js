window.onload = () => {
    let switchs = document.querySelectorAll('[data-switch-active-tag]');

    if (switchs) {
        switchs.forEach((element) => {
            element.addEventListener('change', () => {
                let tagId = element.value;
                sendRequest(tagId);
            })
        })
    }
}

async function sendRequest(id) {
    await fetch(`/admin/categorie/switch/${id}`);
}