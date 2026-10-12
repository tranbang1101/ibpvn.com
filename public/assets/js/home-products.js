(function () {
    const sections = document.querySelectorAll('[data-product-section]');
    if (!sections.length) return;

    function renderSection(section, showAll) {
        const selectedCategory = section.dataset.activeCategory || '';
        const slides = section.querySelectorAll('.product-swiper .swiper-slide');
        let visibleCount = 0;

        slides.forEach(function (slide) {
            const visible = showAll || !selectedCategory || slide.dataset.category === selectedCategory;
            slide.classList.toggle('is-category-hidden', !visible);
            if (visible) visibleCount += 1;
        });

        const swiperElement = section.querySelector('.product-swiper');
        const emptyState = section.querySelector('.product-empty-state');
        const isEmpty = visibleCount === 0;
        if (swiperElement) swiperElement.hidden = isEmpty;
        if (emptyState) emptyState.hidden = !isEmpty;
        section.classList.toggle('has-no-products', isEmpty);

        const swiper = swiperElement && (swiperElement.swiperInstance || swiperElement.swiper);
        if (swiper && !swiper.destroyed) {
            swiper.update();
            swiper.slideTo(0, 0);
        }
    }

    function collapseSection(section) {
        section.classList.remove('is-expanded');
        const toggle = section.querySelector('[data-products-toggle]');
        if (toggle) toggle.innerHTML = 'XEM T\u1ea4T C\u1ea2 <i class="bi bi-chevron-right"></i>';
    }

    sections.forEach(function (section) {
        section.dataset.activeCategory = section.dataset.defaultCategory || '';
        section.querySelectorAll('.promo-tab[data-category]').forEach(function (tab) {
            const active = tab.dataset.category === section.dataset.activeCategory;
            tab.classList.toggle('is-active', active);
            tab.setAttribute('aria-pressed', active ? 'true' : 'false');
        });
        renderSection(section, false);
    });

    document.addEventListener('click', function (event) {
        const tab = event.target.closest('.promo-tab[data-category]');
        if (tab) {
            const section = tab.closest('[data-product-section]');
            if (!section) return;

            section.dataset.activeCategory = tab.dataset.category;
            collapseSection(section);
            section.querySelectorAll('.promo-tab[data-category]').forEach(function (item) {
                const active = item === tab;
                item.classList.toggle('is-active', active);
                item.setAttribute('aria-pressed', active ? 'true' : 'false');
            });
            renderSection(section, false);
            return;
        }

        const toggle = event.target.closest('[data-products-toggle]');
        if (!toggle) return;
        const section = toggle.closest('[data-product-section]');
        if (!section) return;

        const expanded = section.classList.toggle('is-expanded');
        toggle.innerHTML = expanded
            ? '\u1ea8N B\u1edaT <i class="bi bi-chevron-up"></i>'
            : 'XEM T\u1ea4T C\u1ea2 <i class="bi bi-chevron-right"></i>';
        renderSection(section, expanded);
    });
})();
