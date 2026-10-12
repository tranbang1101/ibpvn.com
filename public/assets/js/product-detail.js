(function () {
    const page = document.querySelector('.product-detail-redesign');
    if (!page) return;

    const image = page.querySelector('[data-main-image]');
    const thumbs = Array.from(page.querySelectorAll('[data-gallery-image]'));
    const variants = Array.from(page.querySelectorAll('[data-variant-id]'));
    const forms = Array.from(page.querySelectorAll('[data-product-detail-form]'));
    const quantityInput = page.querySelector('[data-product-quantity]');
    const unitPriceNode = page.querySelector('[data-unit-price]');
    const basePrice = Number(unitPriceNode?.dataset.unitPrice || 0);
    const delEl = page.querySelector('.pd-price-line del');
    const originalPrice = delEl ? Number(delEl.textContent.replace(/[^0-9]/g, '')) : basePrice;
    let activeImageIndex = 0;
    let selectedVariant = variants[0]?.dataset.variantId || '';
    const selectedAddons = new Set();
    const money = new Intl.NumberFormat('vi-VN', { maximumFractionDigits: 0 });

    function displayImage(src, index) {
        if (!src || !image) return;
        image.src = src;
        if (Number.isInteger(index) && index >= 0) activeImageIndex = index;
        thumbs.forEach(function (thumb, thumbIndex) {
            const active = thumbIndex === activeImageIndex && thumb.dataset.galleryImage === src;
            thumb.classList.toggle('is-active', active);
        });
    }

    thumbs.forEach(function (thumb, index) {
        thumb.addEventListener('click', function () {
            displayImage(thumb.dataset.galleryImage, index);
        });
    });

    function moveGallery(delta) {
        if (!thumbs.length) return;
        const nextIndex = (activeImageIndex + delta + thumbs.length) % thumbs.length;
        displayImage(thumbs[nextIndex].dataset.galleryImage, nextIndex);
    }
    page.querySelector('[data-gallery-prev]')?.addEventListener('click', function () {
        moveGallery(-1);
    });
    page.querySelector('[data-gallery-next]')?.addEventListener('click', function () {
        moveGallery(1);
    });

    function currentUnitPrice() {
        const selected = variants.find(function (button) { return button.dataset.variantId === selectedVariant; });
        return Number(selected?.dataset.variantPrice || basePrice);
    }

    function updateFormOptions() {
        forms.forEach(function (form) {
            const holder = form.querySelector('[data-selected-addons]');
            if (holder) {
                holder.replaceChildren();
                selectedAddons.forEach(function (id) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'addon_ids[]';
                    input.value = id;
                    holder.appendChild(input);
                });
            }
            const variantInput = form.querySelector('[data-selected-variant]');
            if (variantInput) variantInput.value = selectedVariant;
            const bundleQuantity = form.querySelector('[data-bundle-quantity]');
            if (bundleQuantity) bundleQuantity.value = quantityInput?.value || '1';
        });
    }

    function updateTotals() {
        const quantity = Math.max(1, Math.min(99, Number(quantityInput?.value || 1)));
        const productPrice = currentUnitPrice();
        if (unitPriceNode) unitPriceNode.textContent = money.format(productPrice) + '₫';
        const visibleDiscount = Math.max(0, originalPrice - productPrice);
        const discountTag = page.querySelector('.pd-price-line em');
        const savingsTag = page.querySelector('[data-price-savings]');
        if (discountTag) discountTag.textContent = '-' + (originalPrice ? Math.round(visibleDiscount / originalPrice * 100) : 0) + '%';
        if (savingsTag) savingsTag.textContent = 'Tiết kiệm: ' + money.format(visibleDiscount) + '₫';
        let addonsPrice = 0;
        let addonsOriginal = 0;
        page.querySelectorAll('[data-addon-card]').forEach(function (card) {
            if (!selectedAddons.has(card.dataset.addonId)) return;
            addonsPrice += Number(card.dataset.addonPrice || 0);
            addonsOriginal += Number(card.dataset.addonOldPrice || card.dataset.addonPrice || 0);
        });
        const grandTotal = (productPrice + addonsPrice) * quantity;
        const oldTotal = (originalPrice + addonsOriginal) * quantity;
        const saved = Math.max(0, oldTotal - grandTotal);
        const percent = oldTotal ? Math.round(saved / oldTotal * 100) : 0;
        const bundleTotal = page.querySelector('[data-bundle-total]');
        const addonTotal = page.querySelector('[data-addon-total]');
        const savings = page.querySelector('[data-savings]');
        if (bundleTotal) bundleTotal.textContent = money.format(grandTotal) + '₫';
        if (addonTotal) addonTotal.textContent = money.format(addonsPrice * quantity) + '₫';
        if (savings) savings.textContent = 'Tiết kiệm ' + money.format(saved) + '₫ · -' + percent + '%';
        updateFormOptions();
    }

    variants.forEach(function (button) {
        button.addEventListener('click', function () {
            variants.forEach(function (variant) { variant.classList.remove('is-selected'); });
            button.classList.add('is-selected');
            selectedVariant = button.dataset.variantId;
            const galleryMatch = thumbs.findIndex(function (thumb) { return thumb.dataset.galleryImage === button.dataset.variantImage; });
            displayImage(button.dataset.variantImage, galleryMatch);
            updateTotals();
        });
    });

    page.querySelectorAll('[data-addon-card]').forEach(function (card) {
        const toggle = card.querySelector('.pd-addon-toggle');
        toggle?.addEventListener('click', function () {
            const id = card.dataset.addonId;
            const isSelected = selectedAddons.has(id);
            if (isSelected) {
                selectedAddons.delete(id);
            } else {
                selectedAddons.add(id);
            }
            card.classList.toggle('is-selected', !isSelected);
            toggle.setAttribute('aria-pressed', String(!isSelected));
            toggle.textContent = isSelected ? 'CHỌN' : 'BỎ CHỌN';
            updateTotals();
        });
    });

    page.querySelectorAll('[data-quantity]').forEach(function (button) {
        button.addEventListener('click', function () {
            if (!quantityInput) return;
            const delta = button.dataset.quantity === 'plus' ? 1 : -1;
            quantityInput.value = Math.max(1, Math.min(99, Number(quantityInput.value || 1) + delta));
            updateTotals();
        });
    });
    quantityInput?.addEventListener('input', function () {
        quantityInput.value = Math.max(1, Math.min(99, Number(quantityInput.value || 1)));
        updateTotals();
    });

    const addonTrack = page.querySelector('[data-addon-track]');
    function scrollAddons(direction) {
        if (!addonTrack) return;
        const card = addonTrack.querySelector('[data-addon-card]');
        addonTrack.scrollBy({
            left: direction * ((card?.getBoundingClientRect().width || 150) + 12),
            behavior: 'smooth',
        });
    }
    page.querySelector('[data-addon-prev]')?.addEventListener('click', function () {
        scrollAddons(-1);
    });
    page.querySelector('[data-addon-next]')?.addEventListener('click', function () {
        scrollAddons(1);
    });

    const tabButtons = Array.from(page.querySelectorAll('[data-info-tab]'));
    const infoPanels = Array.from(page.querySelectorAll('[data-info-panel]'));
    function showInfoTab(name) {
        tabButtons.forEach(function (button) {
            const active = button.dataset.infoTab === name;
            button.classList.toggle('is-active', active);
            button.setAttribute('aria-selected', String(active));
        });
        infoPanels.forEach(function (panel) {
            const active = panel.dataset.infoPanel === name;
            panel.hidden = !active;
            panel.classList.toggle('is-visible', active);
        });
    }
    tabButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            showInfoTab(button.dataset.infoTab);
        });
    });
    page.querySelector('[data-specs-shortcut]')?.addEventListener('click', function (event) {
        event.preventDefault();
        showInfoTab('specs');
        document.querySelector('#product-information')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    const specsDialog = page.querySelector('[data-spec-dialog]');
    page.querySelector('[data-open-specs]')?.addEventListener('click', function () {
        if (specsDialog && typeof specsDialog.showModal === 'function' && !specsDialog.open) {
            specsDialog.showModal();
        }
    });
    page.querySelector('[data-close-specs]')?.addEventListener('click', function () {
        if (specsDialog && typeof specsDialog.close === 'function' && specsDialog.open) {
            specsDialog.close();
        }
    });
    specsDialog?.addEventListener('click', function (event) {
        if (event.target === specsDialog && typeof specsDialog.close === 'function') {
            specsDialog.close();
        }
    });

    const reviewItems = Array.from(page.querySelectorAll('[data-review-list] .pd-review-item'));
    const reviewPageSize = 3;
    const reviewPages = Math.max(1, Math.ceil(reviewItems.length / reviewPageSize));
    let reviewPage = 0;
    const indicator = page.querySelector('[data-review-page-indicator]');
    function renderReviewPage() {
        reviewItems.forEach(function (item, index) {
            item.hidden = index < reviewPage * reviewPageSize || index >= (reviewPage + 1) * reviewPageSize;
        });
        if (indicator) indicator.textContent = (reviewPage + 1) + ' / ' + reviewPages;
        page.querySelectorAll('[data-review-page]').forEach(function (button) {
            const isFirstButton = button.dataset.reviewPage === 'first'
                || button.dataset.reviewPage === 'prev';
            const isLastButton = button.dataset.reviewPage === 'last'
                || button.dataset.reviewPage === 'next';

            button.disabled = reviewPages === 1
                || (isFirstButton && reviewPage === 0)
                || (isLastButton && reviewPage === reviewPages - 1);
        });
    }
    page.querySelectorAll('[data-review-page]').forEach(function (button) {
        button.addEventListener('click', function () {
            if (button.dataset.reviewPage === 'first') reviewPage = 0;
            if (button.dataset.reviewPage === 'prev') reviewPage = Math.max(0, reviewPage - 1);
            if (button.dataset.reviewPage === 'next') reviewPage = Math.min(reviewPages - 1, reviewPage + 1);
            if (button.dataset.reviewPage === 'last') reviewPage = reviewPages - 1;
            renderReviewPage();
        });
    });
    renderReviewPage();
    updateTotals();

    const similarTrack = page.querySelector('[data-similar-track]');
    const similarPrev = page.querySelector('[data-similar-prev]');
    const similarNext = page.querySelector('[data-similar-next]');
    function updateSimilarControls() {
        if (!similarTrack || !similarPrev || !similarNext) return;
        const canScroll = similarTrack.scrollWidth > similarTrack.clientWidth + 2;
        similarPrev.hidden = !canScroll;
        similarNext.hidden = !canScroll;
        similarPrev.disabled = !canScroll || similarTrack.scrollLeft <= 2;
        similarNext.disabled = !canScroll || similarTrack.scrollLeft + similarTrack.clientWidth >= similarTrack.scrollWidth - 2;
    }
    function scrollSimilar(direction) {
        if (!similarTrack) return;
        const card = similarTrack.querySelector('.pd-similar-card');
        const styles = window.getComputedStyle(similarTrack);
        const gap = parseFloat(styles.columnGap || styles.gap) || 12;
        similarTrack.scrollBy({
            left: direction * ((card?.getBoundingClientRect().width || 220) + gap),
            behavior: 'smooth',
        });
    }
    similarPrev?.addEventListener('click', function () {
        scrollSimilar(-1);
    });
    similarNext?.addEventListener('click', function () {
        scrollSimilar(1);
    });
    similarTrack?.addEventListener('scroll', updateSimilarControls, { passive: true });
    similarTrack?.addEventListener('scrollend', updateSimilarControls, { passive: true });
    window.addEventListener('resize', updateSimilarControls);
    updateSimilarControls();

    page.querySelectorAll('[data-similar-buy]').forEach(function (button) {
        button.addEventListener('click', function () {
            const form = document.createElement('form');
            form.method = 'post';
            form.action = page.querySelector('#productDetailForm').action;
            const csrfToken = page.querySelector('#productDetailForm input[name="_csrf"]')?.value || '';
            [['_csrf', csrfToken], ['product_id', button.dataset.similarBuy], ['quantity', '1'], ['buy_now', '1']].forEach(function (pair) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = pair[0];
                input.value = pair[1];
                form.appendChild(input);
            });
            document.body.appendChild(form);
            form.submit();
        });
    });
}());
