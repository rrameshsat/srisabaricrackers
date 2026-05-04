@extends('master.front')

@section('title')
    {{ __('Quick Shopping') }}
@endsection

@section('meta')
<meta name="description" content="Quick bulk order booking for crackers and celebration products">
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('styleplugins')
    <link rel="stylesheet" href="{{ asset('assets/front/css/quick-shopping.css') }}">
@endsection

@section('content')
<div class="page-title qs-hidden">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <ul class="breadcrumbs">
                    <li><a href="{{ route('front.index') }}">{{ __('Home') }}</a></li>
                    <li class="separator"></li>
                    <li>Quick Shopping</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="container padding-bottom-3x mb-1">
    <div id="booking-app"></div>
</div>

<div id="checkout-bar" class="qs-checkout-bar">
    <div class="qs-checkout-bar__stats">
        <span id="checkout-products" class="qs-checkout-bar__stat">Products: 0</span>
        <span id="checkout-items" class="qs-checkout-bar__stat">Items: 0</span>
        <span id="checkout-total" class="qs-checkout-bar__stat">Total: ₹0.00</span>
        <span id="checkout-savings" class="qs-checkout-bar__stat qs-checkout-bar__stat--savings">Savings: ₹0.00</span>
    </div>
    <div class="qs-checkout-bar__actions">
        <button onclick="clearBookingCart()" class="qs-checkout-bar__btn qs-checkout-bar__btn--clear">Clear Cart</button>
        <button onclick="proceedToCheckout()" id="checkout-btn" class="qs-checkout-bar__btn qs-checkout-bar__btn--proceed">Proceed to Checkout</button>
    </div>
</div>

<script>
var state = { categories: [], products: {}, cart: {} };
var BASE_PATH = '{{ rtrim(url('/'), '/') }}';
var API_BASE = BASE_PATH + '/quickshopping';
var CHECKOUT_URL = '{{ route("front.checkout") }}';

function formatPrice(price) {
    return '\u20B9' + Number(price || 0).toFixed(2);
}

function parseJsonAttr(value) {
    if (!value) return {};
    try {
        return JSON.parse(decodeURIComponent(value));
    } catch (e) {
        return {};
    }
}

function getDefaultAttributes(productAttributes) {
    var attrs = {};
    if (!Array.isArray(productAttributes)) return attrs;

    productAttributes.forEach(function(attr) {
        if (attr && Array.isArray(attr.options) && attr.options.length > 0) {
            attrs[attr.id] = attr.options[0].id;
        }
    });

    return attrs;
}

function getAttributePrice(productAttributes, selectedAttrs) {
    var total = 0;
    if (!Array.isArray(productAttributes)) return total;

    productAttributes.forEach(function(attr) {
        if (!attr || !Array.isArray(attr.options)) return;
        var selectedId = selectedAttrs ? selectedAttrs[attr.id] : null;
        var option = attr.options.find(function(opt) {
            return String(opt.id) === String(selectedId);
        }) || attr.options[0];

        if (option) {
            total += parseFloat(option.price || 0) || 0;
        }
    });

    return total;
}

function getProductAttributes(row) {
    if (!row) return [];
    return parseJsonAttr(row.getAttribute('data-product-attrs')) || [];
}

function getRowAttrs(row) {
    if (!row) return {};
    var attrs = parseJsonAttr(row.getAttribute('data-attrs'));
    if (Object.keys(attrs).length > 0) {
        return attrs;
    }

    return getDefaultAttributes(getProductAttributes(row));
}

function getCartItemByProductId(productId) {
    for (var key in state.cart) {
        if (!Object.prototype.hasOwnProperty.call(state.cart, key)) continue;
        var item = state.cart[key];
        if (parseInt(item.item_id || item.id || 0, 10) === parseInt(productId, 10)) {
            return { key: key, item: item };
        }
    }
    return null;
}

function buildAttributeDisplay(productAttributes, selectedAttrs) {
    if (!Array.isArray(productAttributes) || productAttributes.length === 0) {
        return '<span class="qs-empty-inline">-</span>';
    }

    var html = '<div class="attr-display-container">';
    productAttributes.forEach(function(attr) {
        var option = null;
        if (Array.isArray(attr.options)) {
            option = attr.options.find(function(opt) {
                return String(opt.id) === String(selectedAttrs ? selectedAttrs[attr.id] : '');
            }) || attr.options[0];
        }

        html += '<div class="attr-display-row">';
        html += '<span class="attr-display-name">' + attr.name + ':</span> ';
        html += '<span class="attr-display-value">' + (option ? option.name : '-') + '</span>';
        html += '</div>';
    });
    html += '</div>';

    return html;
}

function getSummary() {
    var itemCount = 0;
    var totalAmount = 0;
    var previousTotal = 0;

    for (var key in state.cart) {
        if (!Object.prototype.hasOwnProperty.call(state.cart, key)) continue;
        var item = state.cart[key];
        var qty = parseInt(item.quantity || 0, 10);
        var price = parseFloat(item.price || 0) || 0;
        var previousPrice = parseFloat(item.previous_price || 0) || 0;
        itemCount += qty;
        totalAmount += price * qty;
        previousTotal += previousPrice * qty;
    }

    return {
        productCount: Object.keys(state.cart).length,
        itemCount: itemCount,
        totalAmount: totalAmount,
        savings: previousTotal - totalAmount
    };
}

function updateCheckoutBar() {
    var summary = getSummary();
    document.getElementById('checkout-products').textContent = 'Products: ' + summary.productCount;
    document.getElementById('checkout-items').textContent = 'Items: ' + summary.itemCount;
    document.getElementById('checkout-total').textContent = 'Total: ' + formatPrice(summary.totalAmount);
    document.getElementById('checkout-savings').textContent = 'Savings: ' + formatPrice(summary.savings);
    var checkoutBtn = document.getElementById('checkout-btn');
    checkoutBtn.classList.toggle('qs-checkout-bar__btn--inactive', summary.productCount === 0);
    checkoutBtn.disabled = summary.productCount === 0;
}

function fetchJSON(url, options) {
    var opts = options || {};
    if (!opts.headers) opts.headers = {};
    var csrfMeta = document.querySelector('meta[name="csrf-token"]');
    if (csrfMeta) opts.headers['X-CSRF-TOKEN'] = csrfMeta.content;
    opts.headers['Content-Type'] = 'application/json';
    return fetch(url, opts).then(function(r) { return r.json(); });
}

function loadData() {
    Promise.all([fetchJSON(API_BASE + '-cart'), fetchJSON(API_BASE + '-categories')]).then(function(results) {
        if (results[0].success && results[0].data && results[0].data.items) {
            state.cart = results[0].data.items;
        }

        if (results[1].success) {
            state.categories = results[1].data;
            render();
        }

        updateSummaryGrid();
        updateCheckoutBar();
    });
}

function render() {
    var container = document.getElementById('booking-app');
    if (!container) return;

    var summary = getSummary();
    var html = '<div class="qs-hero"><h2 class="qs-hero__title">Quick Shopping</h2><p class="qs-hero__subtitle">Select products and quantities to add to cart</p></div>';
    html += '<div class="qs-summary-grid">';
    html += '<div class="qs-summary-card qs-summary-card--products"><div class="qs-summary-card__value">' + summary.productCount + '</div><div class="qs-summary-card__label">Products</div></div>';
    html += '<div class="qs-summary-card qs-summary-card--items"><div class="qs-summary-card__value">' + summary.itemCount + '</div><div class="qs-summary-card__label">Total Items</div></div>';
    html += '<div class="qs-summary-card qs-summary-card--total"><div class="qs-summary-card__value">' + formatPrice(summary.totalAmount) + '</div><div class="qs-summary-card__label">Total</div></div>';
    html += '<div class="qs-summary-card qs-summary-card--savings"><div class="qs-summary-card__value">' + formatPrice(summary.savings) + '</div><div class="qs-summary-card__label">Savings</div></div>';
    html += '</div>';

    state.categories.forEach(function(cat) {
        html += '<div class="qs-category">';
        html += '<div class="qs-category__header" onclick="toggleCat(' + cat.id + ')">';
        html += '<h3 class="qs-category__title">' + cat.name + '</h3>';
        html += '<span class="qs-category__count">' + cat.product_count + ' products</span>';
        html += '</div>';
        html += '<div id="cat-' + cat.id + '" class="qs-category__body"><p class="qs-empty">Click category to load products...</p></div>';
        html += '</div>';
    });

    container.innerHTML = html;
}

function toggleCat(catId) {
    var div = document.getElementById('cat-' + catId);
    if (div.style.display === 'none' || div.style.display === '') {
        div.style.display = 'block';
        if (div.getAttribute('data-loaded') !== 'true') {
            loadProducts(catId);
        }
    } else {
        div.style.display = 'none';
    }
}

function loadProducts(catId) {
    var div = document.getElementById('cat-' + catId);
    div.innerHTML = '<p class="qs-loading">Loading products...</p>';

    fetchJSON(API_BASE + '-products/' + catId).then(function(data) {
        if (data.success && data.data.length > 0) {
            var html = '<div class="qs-table-wrap"><table class="qs-table"><thead><tr><th>Image</th><th>Product</th><th>Price</th><th>Stock</th><th>Options</th><th>Qty</th><th>Subtotal</th><th>Action</th></tr></thead><tbody>';

            data.data.forEach(function(p) {
                var cartRecord = getCartItemByProductId(p.id);
                var cartItem = cartRecord ? cartRecord.item : null;
                var qty = cartItem ? parseInt(cartItem.quantity || 0, 10) : 0;
                var selectedAttrs = cartItem ? (cartItem.attribute_ids || cartItem.attributes || {}) : getDefaultAttributes(p.attributes);
                var attributePrice = 0;
                var itemPrice = parseFloat(p.offer_price || 0);
                var previousPrice = parseFloat(p.previous_price || 0);
                var subtotal = qty * itemPrice;
                var inStock = p.stock > 0;
				var imgSrc = (p.thumbnail || p.image) ? '{{ url('/') }}/core/public/storage/images/' + (p.thumbnail || p.image) : '{{ url('/') }}/core/public/storage/images/placeholder.png';
                var attrHtml = buildAttributeDisplay(p.attributes, selectedAttrs);

                html += '<tr class="product-row qs-row-card" data-name="' + p.name + '" data-price="' + formatPrice(itemPrice) + '" data-stock="' + (inStock ? 'In Stock' : 'Out of Stock') + '" data-qty="' + qty + '" data-subtotal="' + formatPrice(subtotal) + '" data-added="' + (qty > 0 ? 'true' : 'false') + '" data-instock="' + (inStock ? 'true' : 'false') + '" data-id="' + p.id + '" data-product-attrs="' + encodeURIComponent(JSON.stringify(p.attributes || [])) + '" data-attrs="' + encodeURIComponent(JSON.stringify(selectedAttrs)) + '">';
                html += '<td class="cell-image" data-label=""><img src="' + imgSrc + '" alt="' + p.name + '"></td>';
                html += '<td class="cell-name" data-label="Product">' + p.name + '</td>';
                html += '<td class="cell-price" data-label="Price"><div class="qs-price-wrap"><span class="qs-price-main">' + formatPrice(itemPrice) + '</span>' + (previousPrice > itemPrice ? '<span class="qs-price-prev">' + formatPrice(previousPrice) + '</span>' : '') + '</div></td>';
                html += '<td class="cell-stock" data-label="Stock"><span class="qs-stock-badge ' + (inStock ? 'qs-stock-badge--in' : 'qs-stock-badge--out') + '">' + (inStock ? 'In Stock' : 'Out of Stock') + '</span></td>';
                html += '<td class="cell-attrs" data-label="Options">' + attrHtml + '</td>';
                html += '<td class="cell-qty" data-label="Qty"><div class="qs-qty"><button onclick="updateQty(' + p.id + ',-1,event)">-</button><span>' + qty + '</span><button onclick="updateQty(' + p.id + ',1,event)">+</button></div></td>';
                html += '<td class="cell-subtotal" data-label="Subtotal">' + formatPrice(subtotal) + '</td>';
                html += '<td class="cell-action" data-label="Action">' + (qty > 0 ? '<span class="qs-added">Added</span>' : '<button class="qs-add-btn" onclick="addItem(' + p.id + ',event)"' + (inStock ? '' : ' disabled') + '>Add</button>') + '</td>';
                html += '</tr>';
            });

            html += '</tbody></table></div>';
            div.innerHTML = html;
            div.setAttribute('data-loaded', 'true');
        } else {
            div.innerHTML = '<p class="qs-empty">No products found</p>';
            div.setAttribute('data-loaded', 'true');
        }
    }).catch(function() {
        div.innerHTML = '<p class="qs-error">Error loading products</p>';
    });
}

var pendingRequests = {};

function getPriceFromRow(row) {
    if (!row) return 0;
    var priceAttr = row.getAttribute('data-price') || '₹0';
    return parseFloat(priceAttr.replace(/[^0-9.]/g, '')) || 0;
}

function updateQty(itemId, change, event) {
    if (event) event.stopPropagation();
    if (pendingRequests[itemId]) return;

    var row = document.querySelector('tr[data-id="' + itemId + '"]');
    var currentQtySpan = row ? row.querySelector('.cell-qty span') : null;
    var currentQty = currentQtySpan ? parseInt(currentQtySpan.textContent, 10) || 0 : 0;
    var newQty = currentQty + change;

    if (newQty < 0) newQty = 0;
    pendingRequests[itemId] = true;

    var selectedAttrs = getRowAttrs(row);
    var url = newQty > 0 ? API_BASE + '-cart-update' : API_BASE + '-cart-remove';

    fetchJSON(url, {
        method: 'POST',
        body: JSON.stringify({ item_id: itemId, quantity: newQty, attributes: selectedAttrs })
    }).then(function(data) {
        pendingRequests[itemId] = false;
        if (data.success && data.data) {
            state.cart = data.data.items || {};
            updateCheckoutBar();
            updateSummaryGrid();
            renderProductRows();
        }
    });
}

function updateSummaryGrid() {
    var summary = getSummary();
    var container = document.getElementById('booking-app');
    if (!container) return;

    var summaryHtml = '<div class="qs-summary-grid">';
    summaryHtml += '<div class="qs-summary-card qs-summary-card--products"><div class="qs-summary-card__value">' + summary.productCount + '</div><div class="qs-summary-card__label">Products</div></div>';
    summaryHtml += '<div class="qs-summary-card qs-summary-card--items"><div class="qs-summary-card__value">' + summary.itemCount + '</div><div class="qs-summary-card__label">Total Items</div></div>';
    summaryHtml += '<div class="qs-summary-card qs-summary-card--total"><div class="qs-summary-card__value">' + formatPrice(summary.totalAmount) + '</div><div class="qs-summary-card__label">Total</div></div>';
    summaryHtml += '<div class="qs-summary-card qs-summary-card--savings"><div class="qs-summary-card__value">' + formatPrice(summary.savings) + '</div><div class="qs-summary-card__label">Savings</div></div>';
    summaryHtml += '</div>';

    var oldGrid = container.querySelector('.summary-grid');
    if (oldGrid) {
        oldGrid.outerHTML = summaryHtml;
    }
}

function addItem(productId, event) {
    if (event) event.stopPropagation();
    if (pendingRequests[productId]) return;
    pendingRequests[productId] = true;

    var row = document.querySelector('tr[data-id="' + productId + '"]');
    var selectedAttrs = getRowAttrs(row);

    fetchJSON(API_BASE + '-cart-add', {
        method: 'POST',
        body: JSON.stringify({ item_id: productId, quantity: 1, attributes: selectedAttrs })
    }).then(function(data) {
        pendingRequests[productId] = false;
        if (data.success && data.data) {
            state.cart = data.data.items || {};
            updateCheckoutBar();
            updateSummaryGrid();
            renderProductRows();
        }
    });
}

function renderProductRows() {
    document.querySelectorAll('tr[data-id]').forEach(function(row) {
        var itemId = row.getAttribute('data-id');
        var cartRecord = getCartItemByProductId(itemId);
        var cartItem = cartRecord ? cartRecord.item : null;
        var qty = cartItem ? parseInt(cartItem.quantity || 0, 10) : 0;
        var productAttrs = getProductAttributes(row);
        var selectedAttrs = cartItem ? (cartItem.attribute_ids || cartItem.attributes || getRowAttrs(row)) : getRowAttrs(row);
        if (!selectedAttrs || Object.keys(selectedAttrs).length === 0) {
            selectedAttrs = getDefaultAttributes(productAttrs);
        }
        var price = cartItem ? (parseFloat(cartItem.price || 0) || getPriceFromRow(row)) : getPriceFromRow(row);
        var subtotal = qty * price;

        row.setAttribute('data-attrs', encodeURIComponent(JSON.stringify(selectedAttrs)));
        row.querySelector('.cell-attrs').innerHTML = buildAttributeDisplay(productAttrs, selectedAttrs);
        row.querySelector('.cell-qty span').textContent = qty;
        row.querySelector('.cell-subtotal').textContent = formatPrice(subtotal);
        row.querySelector('.cell-action').innerHTML = qty > 0 ? '<span class="qs-added">Added</span>' : '<button class="qs-add-btn" onclick="addItem(' + itemId + ',event)">Add</button>';
    });
}

function clearBookingCart() {
    fetchJSON(API_BASE + '-cart-clear', { method: 'POST' }).then(function() {
        state.cart = {};
        updateCheckoutBar();
        updateSummaryGrid();
        renderProductRows();
    });
}

function proceedToCheckout() {
    var summary = getSummary();
    if (summary.productCount === 0) {
        alert('Please add products to cart first');
        return;
    }

    fetchJSON(API_BASE + '-cart-transfer', { method: 'POST' }).then(function(data) {
        if (data.success) {
            window.location.href = CHECKOUT_URL;
        } else {
            alert(data.message || 'Failed to transfer cart');
        }
    });
}

loadData();
updateCheckoutBar();
</script>
@endsection
