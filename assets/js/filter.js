import { debounce } from "lodash";
import { Flipper, spring } from 'flip-toolkit';

/**
 * Class Filter for search article in AJAX
 * 
 * @property {HTMLElement} pagination - The pagination element
 * @property {HTMLElement} content - The content element
 * @property {HTMLElement} sorting - The sorting element
 * @property {HTMLFormElement} form - the form HTMLFormElement
 * @property {HTMLElement} count - the count element
 */
export default class Filter {
    /* On attends l'élément HTML parent à l'instanciation */
    constructor(element) {
        /* On vérifie que l'élément envoyé n'est pas null, sinon on arrête l'exécution */
        if (element === null) {
            return;
        }

        /* On vient stocker dans différentes propriétés nos éléments HTML pour les manipuler plus facilement */
        this.pagination = element.querySelector('.js-filter-pagination');
        this.content = element.querySelector('.js-filter-content');
        this.sorting = element.querySelector('.js-filter-sorting');
        this.form = element.querySelector('.js-filter-form');
        this.count = element.querySelector('.js-filter-count');
        this.page = parseInt(new URLSearchParams(window.location.search).get('page') || 1);
        this.moreNav = this.page == 1;

        this.bindEvents();
    }

    /**
     * Ajout des comportements et actions
     */
    bindEvents() {
        const linkClickListener = (event) => {
            if (event.target.tagName === 'A' || event.target.tagName === 'I') {
                event.preventDefault();

                let url;

                if (event.target.tagName === 'I') {
                    url = event.target.parentNode.parentNode.href;
                } else {
                    url = event.target.href;
                }

                this.loadUrl(url);
            }
        }

        /* On ajoute le comportement sur le click sur bouton de sortable */
        this.sorting.addEventListener('click', (e) => {
            linkClickListener(e);
        });

        /* On ajoute le comportement sur les inputs de type text dans le formulaire */
        this.form.querySelectorAll('input[type="text"]').forEach((input) => {
            input.addEventListener('keyup', debounce(this.loadForm.bind(this), 500));
        });

        this.form.querySelectorAll('input[type="checkbox"]').forEach((input) => {
            input.addEventListener('change', this.loadForm.bind(this));
        });

        this.form.querySelector('#btn-reset-filter').addEventListener('click', this.resetForm.bind(this));

        /* Si moreNav à true, on remplace la pagination par un bouton voir plus */
        if (this.moreNav) {
            this.pagination.innerHTML = '<button class="btn btn-primary btn-show-more text-light">Voir Plus</button>';
            this.pagination.querySelector('button').addEventListener('click', this.loadMore.bind(this));
        } else {
            this.pagination.addEventListener('click', linkClickListener);
        }
    }

    async resetForm() {
        /* On récupère l'url de destination pour construire l'url en ajoutant les paramètres par la suite */
        const url = new URL(this.form.getAttribute('action') || window.location.href);

        this.form.querySelectorAll('input').forEach(input => {
            if (input.type == "checkbox") {
                input.checked = false;
            } else {
                input.value = "";
            }
        });

        return this.loadUrl(url.pathname.split('?')[0]);
    }

    /**
     * Ajoute la page suivante au contenu
     */
    async loadMore() {
        /* On récupère l'élement html du bouton */
        const button = this.pagination.querySelector('button');

        /* On disable le button le temps de la requête */
        button.setAttribute('disabled', 'disabled');

        /* On incrémente le numéro de la page */
        this.page++;

        /* On ouvre un objet URL avec l'url actuelle */
        const url = new URL(window.location.href);

        /* On ouvre un objet paramètre URL en lui passant les paramètre existant */
        const params = new URLSearchParams(url.search);

        /* On définit le nouveau numéro de page dans les paramètres */
        params.set('page', this.page);

        /* On envoie la requête et on attend le résultat pour réactiver le bouton voir plus */
        await this.loadUrl(url.pathname + '?' + params.toString(), true);

        /* Une fois que le contenu est modifié on réactive le bouton */
        button.removeAttribute('disabled');
    }

    /**
     * Récupérer les informations du formulaire et envoyer la requête
     */
    async loadForm() {
        /* On redéfinit la page 1 */
        this.page = 1;

        /* On stock dans un tableau clé/valeur les informations du form */
        const formData = new FormData(this.form);

        /* On récupère l'url de destination pour construire l'url en ajoutant les paramètres par la suite */
        const url = new URL(this.form.getAttribute('action') || window.location.href);

        const params = new URLSearchParams();

        /* On ajoute les paramètre Get  avec les données de laa constante formData */
        formData.forEach((value, key) => {
            params.append(key, value);
        });

        return this.loadUrl(url.pathname + '?' + params.toString());
    }

    /**
     * Send the ajax request and modify the content of the page
     * 
     * @param {URL} url - Url for the ajax request
     * @param {boolean} append - Verify if append the content
     */
    async loadUrl(url, append = false) {
        /* On affiche le loader */
        this.showLoader();

        /* On stock seulement les paramètres d'url */
        const params = new URLSearchParams(url.split('?')[1] || '');
        params.set('ajax', 1);

        // const response = await fetch(`${url.split('?')[0]}?${params.toString()}`);
        const response = await fetch(url.split('?')[0] + '?' + params.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            }
        });

        if (response.status >= 200 && response.status < 300) {
            const data = await response.json();

            /* On ajoute le contenu aux articles si append === true */
            this.flipContent(data.content, append);

            this.sorting.innerHTML = data.sortable;
            this.count.innerHTML = data.count;

            if (!this.moreNav) {
                this.pagination.innerHTML = data.pagination;
            } else if (this.page === data.pages || this.content.children.item(0) === this.content.children.namedItem('article-no-content')) {
                this.pagination.style.display = "none";
            } else {
                this.pagination.style.display = "block";
            }

            params.delete('ajax');

            history.replaceState({}, '', url.split('?')[0] + '?' + params.toString());

            this.hideLoader();
        } else {
            console.error(response);
        }
    }

    /**
     * Replace element on content with animation
     * 
     * @param {string} content 
     * @param {boolean} append 
     */
    flipContent(content, append) {
        /* On définit le nom de la configuration d'animation */
        const springName = 'veryGentle';

        /* On configure l'animation de sortie des éléments */
        const exitSpring = (element, index, onComplete) => {
            spring({
                config: 'stiff',
                values: {
                    translateY: [0, 20],
                    opacity: [1, 0],
                },
                onUpdate: ({ translateY, opacity }) => {
                    element.style.opacity = opacity;
                    element.style.transform = `translateY(${translateY}px)`
                },
                delay: index * 10,
                onComplete,
            });
        }

        /* On configure l'animation d'entrée des éléments */
        const appearSpring = (element, index) => {
            spring({
                config: 'stiff',
                values: {
                    translateY: [20, 0],
                    opacity: [0, 1],
                },
                onUpdate: ({ translateY, opacity }) => {
                    element.style.opacity = opacity;
                    element.style.transform = `translateY(${translateY}px)`;
                },
                delay: index * 10,
            });
        }

        /* On instancie la classe Flipper en lui passant le content */
        const flipper = new Flipper({
            element: this.content,
        });

        /* On enregistre la position de chaque card actuelle (sans modification) */
        this.content.querySelectorAll('.blog-card').forEach((card) => {
            flipper.addFlipped({
                element: card,
                flipId: card.id,
                spring: springName,
                onExit: exitSpring,
            });
        });

        flipper.recordBeforeUpdate();

        /* On met à jour le content en JS seulment */
        if (append) {
            this.content.innerHTML += content;
        } else {
            this.content.innerHTML = content;
        }

        /* On réindex les card du contenu modifié */
        this.content.querySelectorAll('.blog-card').forEach((card) => {
            flipper.addFlipped({
                element: card,
                flipId: card.id,
                spring: springName,
                onAppear: appearSpring,
            });
        });

        /* On met à jour le contenu sur la page */
        flipper.update();
    }

    /**
     * Show the loader animation on the form during the ajax request
     */
    showLoader() {
        this.form.classList.add('is-loading');

        const loader = this.form.querySelector('.js-loading');

        if (!loader) {
            return;
        }

        loader.setAttribute('aria-hidden', false);
        loader.style.display = null;
    }

    /**
     * Hidde the loader animation after the ajax request
     */
    hideLoader() {
        this.form.classList.remove('is-loading');

        const loader = this.form.querySelector('.js-loading');

        if (!loader) {
            return;
        }

        loader.setAttribute('aria-hidden', true);
        loader.style.display = 'none';
    }
}