/* SLIDE BANNER */
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.side-banner-swiper').forEach(function (el) {
        const delay = parseInt(el.getAttribute('data-delay')) || 4000;
        new Swiper(el, {
            loop: true,
            effect: 'fade',
            fadeEffect: {
                crossFade: true
            },
            autoplay: {
                delay: delay,
                disableOnInteraction: false,
            },
            speed: 500,
            allowTouchMove: true,
        });
    });
});
/* SLIDE BANNER LỚN */
document.addEventListener('DOMContentLoaded', function () {
    const mainBanner = document.querySelector('.main-banner-swiper');
    if (mainBanner) {
        const bannerSwiper = new Swiper(mainBanner, {
            loop: true,
            autoplay: {
                delay: 4000,
                disableOnInteraction: false,
            },
            speed: 600,
            pagination: {
                el: '.main-banner-pagination',
                clickable: true,
            },
        });

        const bannerTabs = document.querySelectorAll('.main-banner-tabs [data-banner-slide]');
        function syncBannerTabs() {
            bannerTabs.forEach(function (tab) {
                const active = Number(tab.dataset.bannerSlide) === bannerSwiper.realIndex;
                tab.classList.toggle('is-active', active);
                tab.setAttribute('aria-pressed', active ? 'true' : 'false');
            });
        }
        bannerTabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                bannerSwiper.slideToLoop(Number(this.dataset.bannerSlide));
            });
        });
        bannerSwiper.on('slideChange', syncBannerTabs);
        syncBannerTabs();
    }
});
/* NEXT/PREV SANPHAM */
document.querySelectorAll('.product-swiper').forEach((el) => {
    el.swiperInstance = new Swiper(el, {
        slidesPerView: 5,
        slidesPerGroup: 1,
        spaceBetween: 20,
        grid: {
            rows: 1,
            fill: 'row',
        },
        navigation: {
            nextEl: el.closest('.product-slider-wrap').querySelector('.product-next'),
            prevEl: el.closest('.product-slider-wrap').querySelector('.product-prev'),
        },
        on: {
            init: function () {
                //
            }
        },
        breakpoints: {
            0: {
                slidesPerView: 2,
                slidesPerGroup: 4,
                spaceBetween: 12,
                grid: { rows: 2, fill: 'row' },
            },
            576: {
                slidesPerView: 2,
                slidesPerGroup: 2,
                spaceBetween: 16,
                grid: { rows: 1, fill: 'row' },
            },
            768: { slidesPerView: 3, slidesPerGroup: 1, spaceBetween: 18},
            1100: { slidesPerView: 4, slidesPerGroup: 1, spaceBetween: 20},
            1200: { slidesPerView: 5, slidesPerGroup: 1, spaceBetween: 20},
        }
    });
});
/* VIEW ALL NEWS */
const newsGrid = document.getElementById('newsGrid');
const btnToggleNews = document.getElementById('btnToggleNews');
if (newsGrid && btnToggleNews) {
    btnToggleNews.addEventListener('click', function (event) {
        event.preventDefault();
        const isExpanded = newsGrid.classList.toggle('is-expanded');
        if (isExpanded) {
            // Đang hiện tất cả → đổi thành ẨN BỚT
            this.innerHTML = 'ẨN BỚT <i class="bi bi-chevron-up"></i>';
            this.classList.add('is-expanded');
        } else {
            // Đang ẩn → đổi thành XEM TẤT CẢ
            this.innerHTML = 'XEM TẤT CẢ <i class="bi bi-chevron-right"></i>';
            this.classList.remove('is-expanded');
        }
    });
}
/* FAQ COLLAPSE */
document.querySelectorAll('.faq-list').forEach(function (list) {
    list.addEventListener('click', function (event) {
        const button = event.target.closest('.faq-question');
        if (!button || !list.contains(button)) return;

        const answerId = button.getAttribute('aria-controls');
        const answer = answerId ? document.getElementById(answerId) : null;
        if (!answer) return;

        const isOpen = button.getAttribute('aria-expanded') === 'true';
        button.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
        answer.hidden = isOpen;
        button.closest('.faq-item')?.classList.toggle('is-open', !isOpen);

        const icon = button.querySelector('i');
        if (icon) {
            icon.classList.toggle('bi-plus-circle', isOpen);
            icon.classList.toggle('bi-dash-circle', !isOpen);
        }
    });
});

/* RESPONSIVE HEADER: category drawer and compact search */
(function () {
    const header = document.querySelector('.site-header');
    const menuButton = document.querySelector('.mobile-menu-toggle');
    const categoryPanel = document.getElementById('mobile-category-panel');
    const searchButton = document.querySelector('.mobile-search-toggle');
    if (!header) return;

    function closeCategoryMenu() {
        if (!menuButton || !categoryPanel) return;
        menuButton.setAttribute('aria-expanded', 'false');
        menuButton.setAttribute('aria-label', 'Mở danh mục sản phẩm');
        menuButton.querySelector('i')?.classList.replace('bi-x-lg', 'bi-list');
        categoryPanel.hidden = true;
    }

    menuButton?.addEventListener('click', function () {
        const opening = this.getAttribute('aria-expanded') !== 'true';
        this.setAttribute('aria-expanded', String(opening));
        this.setAttribute('aria-label', opening ? 'Đóng danh mục sản phẩm' : 'Mở danh mục sản phẩm');
        this.querySelector('i')?.classList.toggle('bi-list', !opening);
        this.querySelector('i')?.classList.toggle('bi-x-lg', opening);
        if (categoryPanel) categoryPanel.hidden = !opening;
        header.classList.remove('mobile-search-open');
        searchButton?.setAttribute('aria-expanded', 'false');
    });

    searchButton?.addEventListener('click', function () {
        const opening = !header.classList.contains('mobile-search-open');
        header.classList.toggle('mobile-search-open', opening);
        this.setAttribute('aria-expanded', String(opening));
        closeCategoryMenu();
        if (opening) header.querySelector('.header-search-input')?.focus();
    });

    document.addEventListener('click', function (event) {
        if (categoryPanel && !header.contains(event.target)) closeCategoryMenu();
    });
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeCategoryMenu();
            header.classList.remove('mobile-search-open');
            searchButton?.setAttribute('aria-expanded', 'false');
        }
    });
})();

/* Featured categories: visible draggable horizontal scroll indicator */
(function () {
    const list = document.querySelector('.featured-category-list');
    const track = document.querySelector('.featured-category-scrollbar');
    const thumb = track?.querySelector('.featured-category-scrollbar-thumb');

    if (!list || !track || !thumb) return;

    let dragging = false;
    let activePointerId = null;
    let dragOffset = 0;

    function getMaxScroll() {
        // Use the actual last item position so all 7 items are included,
        // even on browsers that calculate flex overflow differently.
        const items = list.querySelectorAll('.featured-category-item');
        const lastItem = items[items.length - 1];
        if (!lastItem) return 0;

        const contentRight = lastItem.offsetLeft + lastItem.offsetWidth;
        return Math.max(0, contentRight - list.clientWidth);
    }

    function isScrollable() {
        return getMaxScroll() > 1;
    }

    function updateScrollThumb() {
        const maxScroll = getMaxScroll();

        if (maxScroll <= 1) {
            track.style.display = 'none';
            return;
        }

        track.style.display = 'block';

        const trackWidth = track.clientWidth;
        if (trackWidth <= 0) return;

        // Calculate the thumb from the visible area vs. the complete 7-item row.
        const contentWidth = list.scrollLeft + maxScroll;
        const visibleRatio = list.clientWidth / Math.max(list.clientWidth, contentWidth);
        const thumbWidth = Math.min(
            trackWidth,
            Math.max(40, Math.round(trackWidth * visibleRatio))
        );

        const maxThumbLeft = Math.max(0, trackWidth - thumbWidth);
        const scrollRatio = Math.min(1, Math.max(0, list.scrollLeft / maxScroll));
        const thumbLeft = Math.round(scrollRatio * maxThumbLeft);

        thumb.style.width = thumbWidth + 'px';
        thumb.style.left = thumbLeft + 'px';
    }

    function setScrollFromPointer(clientX) {
        const rect = track.getBoundingClientRect();
        const trackWidth = track.clientWidth;
        const thumbWidth = thumb.offsetWidth;
        const maxThumbLeft = Math.max(0, trackWidth - thumbWidth);
        const maxScroll = getMaxScroll();

        if (maxThumbLeft <= 0 || maxScroll <= 0) {
            list.scrollLeft = 0;
            return;
        }

        let thumbLeft = clientX - rect.left - dragOffset;
        thumbLeft = Math.max(0, Math.min(maxThumbLeft, thumbLeft));

        // Explicitly clamp to the exact beginning/end of all 7 items.
        if (thumbLeft <= 0) {
            list.scrollLeft = 0;
        } else if (thumbLeft >= maxThumbLeft) {
            list.scrollLeft = maxScroll;
        } else {
            list.scrollLeft = (thumbLeft / maxThumbLeft) * maxScroll;
        }
    }

    function onPointerDown(event) {
        if (!isScrollable()) return;
        if (event.button !== undefined && event.button !== 0) return;

        const thumbRect = thumb.getBoundingClientRect();
        const clickedThumb = event.target === thumb || thumb.contains(event.target);

        dragging = true;
        activePointerId = event.pointerId;
        dragOffset = clickedThumb
            ? event.clientX - thumbRect.left
            : thumb.offsetWidth / 2;

        try {
            track.setPointerCapture(event.pointerId);
        } catch (e) {}

        setScrollFromPointer(event.clientX);
        event.preventDefault();
    }

    function onPointerMove(event) {
        if (!dragging) return;
        if (activePointerId !== null && event.pointerId !== activePointerId) return;

        setScrollFromPointer(event.clientX);
        event.preventDefault();
    }

    function onPointerUp(event) {
        if (activePointerId !== null && event.pointerId !== activePointerId) return;

        dragging = false;
        activePointerId = null;

        try {
            track.releasePointerCapture(event.pointerId);
        } catch (e) {}

        updateScrollThumb();
    }

    track.addEventListener('pointerdown', onPointerDown);
    track.addEventListener('pointermove', onPointerMove);
    track.addEventListener('pointerup', onPointerUp);
    track.addEventListener('pointercancel', onPointerUp);

    // Direct finger/mouse scrolling stays synchronized with the blue thumb.
    list.addEventListener('scroll', updateScrollThumb, { passive: true });
    window.addEventListener('resize', updateScrollThumb);

    if ('ResizeObserver' in window) {
        const observer = new ResizeObserver(updateScrollThumb);
        observer.observe(list);
        observer.observe(track);
    }

    requestAnimationFrame(updateScrollThumb);
    window.setTimeout(updateScrollThumb, 100);
    window.setTimeout(updateScrollThumb, 400);

    list.querySelectorAll('img').forEach(function (img) {
        if (!img.complete) {
            img.addEventListener('load', updateScrollThumb, { once: true });
        }
    });
})();
