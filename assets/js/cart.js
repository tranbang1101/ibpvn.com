document.addEventListener('submit', async function (event) {
    const form = event.target.closest('.card-cart-form, .product-buy-form');
    if (!form) return;

    const submitter = event.submitter;
    if (submitter && submitter.name === 'buy_now') return;

    event.preventDefault();
    const button = submitter || form.querySelector('button[type="submit"]');
    if (button) button.disabled = true;

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        const result = await response.json();
        if (!response.ok || !result.success) throw new Error(result.message || 'Không thể thêm sản phẩm.');

        document.querySelectorAll('.cart-count').forEach(function (badge) {
            badge.textContent = result.count;
        });
        showCartToast(result.message || 'Đã thêm sản phẩm vào giỏ hàng.');
    } catch (error) {
        showCartToast(error.message || 'Không thể thêm sản phẩm. Vui lòng thử lại.');
    } finally {
        if (button) button.disabled = false;
    }
});

document.querySelectorAll('.cart-quantity-form').forEach(function (form) {
    const input = form.querySelector('input[name="quantity"]');
    if (!input) return;

    form.querySelectorAll('[data-cart-quantity]').forEach(function (button) {
        button.addEventListener('click', function () {
            const delta = button.dataset.cartQuantity === 'plus' ? 1 : -1;
            const current = Number.parseInt(input.value, 10) || 1;
            input.value = String(Math.max(1, Math.min(99, current + delta)));
            form.requestSubmit();
        });
    });
    input.addEventListener('change', function () {
        const current = Number.parseInt(input.value, 10) || 1;
        input.value = String(Math.max(1, Math.min(99, current)));
        form.requestSubmit();
    });
});

function showCartToast(message) {
    let toast = document.querySelector('.cart-toast');
    if (!toast) {
        toast = document.createElement('div');
        toast.className = 'cart-toast';
        toast.setAttribute('role', 'status');
        toast.setAttribute('aria-live', 'polite');
        toast.innerHTML = '<i class="bi bi-check-circle-fill"></i><span></span>';
        document.body.appendChild(toast);
    }
    toast.querySelector('span').textContent = message;
    toast.classList.add('is-visible');
    window.clearTimeout(showCartToast.timeout);
    showCartToast.timeout = window.setTimeout(function () {
        toast.classList.remove('is-visible');
    }, 2600);
}
