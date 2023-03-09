import Swiper, { Pagination, Autoplay, EffectCreative } from 'swiper';
import 'swiper/css';
import 'swiper/css/pagination';

const swiper = new Swiper('.swiper-article', {
    modules: [Pagination, Autoplay, EffectCreative],
    direction: 'horizontal',
    loop: true,
    autoplay: {
        delay: 3000,
        disableOnInteraction: false,
    },
    grabCursor: true,
    pagination: {
        el: '.swiper-pagination',
    },
    effect: 'creative',
    creativeEffect: {
        prev: {
            shadow: true,
            translate: [0, 0, -400]
        },
        next: {
            shadow: true,
            translate: ["100%", 0, 0]
        }
    }
});
