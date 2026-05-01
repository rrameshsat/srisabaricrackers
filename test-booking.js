var BASE_PATH = '/sscrackers';
var API_BASE = BASE_PATH + '/quickshopping';
var CURRENCY = 'Rs';

var state = {
    categories: [],
    products: {},
    cart: {},
    expandedCats: {},
    csrfToken: document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : ''
};

function formatPrice(price) {
    return CURRENCY + ' ' + price.toFixed(2);
}

function createEl(tag, attrs, children) {
    var el = document.createElement(tag);
    for (var key in attrs) {
        if (key === 'style') {
            el.style.cssText = attrs.style;
        } else if (key === 'class') {
            el.className = attrs.class;
        } else if (key === 'onclick') {
            el.onclick = attrs.onclick;
        } else if (key === 'onchange') {
            el.onchange = attrs.onchange;
        } else if (key === 'disabled') {
            el.disabled = attrs.disabled;
        } else if (key === 'type') {
            el.type = attrs.type;
        } else if (key === 'value') {
            el.value = attrs.value;
        } else if (key === 'min') {
            el.min = attrs.min;
        } else if (key === 'max') {
            el.max = attrs.max;
        } else if (key === 'src') {
            el.src = attrs.src;
        } else if (key === 'alt') {
            el.alt = attrs.alt;
        } else if (key === 'onerror') {
            el.onerror = attrs.onerror;
        } else {
            el.setAttribute(key, attrs[key]);
        }
    }
    if (typeof children === 'string') {
        el.textContent = children;
    } else if (Array.isArray(children)) {
        children.forEach(function(child) {
            if (child === null || child === undefined) return;
            if (typeof child === 'string') {
                el.appendChild(document.createTextNode(child));
            } else if (child.nodeType === 1 || child.nodeType === 3) {
                el.appendChild(child);
            }
        });
    } else if (children && (children.nodeType === 1 || children.nodeType === 3)) {
        el.appendChild(children);
    }
    return el;
}

function renderProductImage(product) {
    if (!product.image) {
        return createEl('div', {style: 'width:60px;height:60px;display:flex;align-items:center;justify-content:center;border-radius:8px;background:#f0f0f0;font-size:24px'}, '📦');
    }
    var img = createEl('img', {
        src: BASE_PATH + '/core/public/storage/images/' + product.image,
        alt: product.name,
        style: 'width:60px;height:60px;object-fit:cover;border-radius:8px;background:#f0f0f0',
        onerror: "this.style.display='none';this.outerHTML='<div style=width:60px;height:60px;display:flex;align-items:center;justify-content:center;border-radius:8px;background:#f0f0f0;font-size:24px>📦</div>'"
    });
    return img;
}

function fetchJSON(url, options) {
    var opts = options || {};
    if (!opts.headers) opts.headers = {};
    if (state.csrfToken) {
        opts.headers['X-CSRF-TOKEN'] = state.csrfToken;
    }
    opts.headers['Content-Type'] = 'application/json';
    return fetch(url, opts).then(function(r) { return r.json(); });
}

function loadCategories() {
    Promise.all([
        fetchJSON(API_BASE + '-cart'),
        fetchJSON(API_BASE + '-categories')
    ]).then(function(results) {
        var cartData = results[0];
        var catData = results[1];
        
        if (cartData.success && cartData.data && cartData.data.items) {
            state.cart = cartData.data.items;
        }
        
        if (catData.success) {
            state.categories = catData.data;
            catData.data.forEach(function(cat, index) {
                state.expandedCats[cat.id] = index === 0;
            });
            if (catData.data.length > 0 && !state.products[catData.data[0].id]) {
                loadProducts(catData.data[0].id);
            }
        }
        render();
    }).catch(function(err) {
        console.error('Error loading data:', err);
    });
}

function loadProducts(catId) {
    if (state.products[catId]) return;
    
    fetchJSON(API_BASE + '-products/' + catId).then(function(data) {
        if (data.success) {
            state.products[catId] = data.data;
            render();
        }
    }).catch(function(err) {
        console.error('Error loading products:', err);
    });
}

function toggleCategory(catId) {
    state.expandedCats[catId] = !state.expandedCats[catId];
    if (!state.products[catId]) {
        loadProducts(catId);
    }
    render();
}

function updateQuantity(itemId, qty) {
    if (qty < 0) qty = 0;
    var url = qty > 0 ? API_BASE + '-cart-update' : API_BASE + '-cart-remove';
    fetchJSON(url, {
        method: 'POST',
        body: JSON.stringify({ item_id: itemId, quantity: qty })
    }).then(function(data) {
        if (data.success) {
            state.cart = data.data.items || {};
            render();
        }
    });
}

function addToCart(product) {
    fetchJSON(API_BASE + '-cart-add', {
        method: 'POST',
        body: JSON.stringify({ item_id: product.id, quantity: 1 })
    }).then(function(data) {
        if (data.success) {
            state.cart = data.data.items || {};
            render();
        }
    });
}

function getCartSummary() {
    var itemCount = 0;
    var totalAmount = 0;
    var previousTotal = 0;
    for (var key in state.cart) {
        var item = state.cart[key];
        itemCount += item.quantity;
        totalAmount += item.price * item.quantity;
        previousTotal += item.previous_price * item.quantity;
    }
    return {
        productCount: Object.keys(state.cart).length,
        itemCount: itemCount,
        totalAmount: totalAmount,
        savings: previousTotal - totalAmount
    };
}

function render() {
    var container = document.getElementById('booking-app');
    if (!container) return;
    
    try {
        var summary = getCartSummary();
        
        var productCount = Number(summary.productCount) || 0;
        var itemCount = Number(summary.itemCount) || 0;
        var totalAmount = Number(summary.totalAmount) || 0;
        var savings = Number(summary.savings) || 0;
        
        var header = createEl('div', {style: 'text-align:center;margin-bottom:30px'},
            createEl('h2', {style: 'margin-bottom:10px'}, 'Quick Booking Mode'),
            createEl('p', {style: 'color:#666'}, 'Select products and quantities to add to cart')
        );
        
        var summaryCards = createEl('div', {style: 'display:grid;grid-template-columns:repeat(4,1fr);gap:15px;margin-bottom:30px'},
            createEl('div', {style: 'background:linear-gradient(135deg,#667eea,#764ba2);color:white;padding:20px;border-radius:10px;text-align:center'},
                createEl('div', {style: 'font-size:28px;font-weight:bold'}, String(productCount)),
                createEl('div', {style: 'font-size:14px'}, 'Products')
            ),
            createEl('div', {style: 'background:linear-gradient(135deg,#f093fb,#f5576c);color:white;padding:20px;border-radius:10px;text-align:center'},
                createEl('div', {style: 'font-size:28px;font-weight:bold'}, String(itemCount)),
                createEl('div', {style: 'font-size:14px'}, 'Total Items')
            ),
            createEl('div', {style: 'background:linear-gradient(135deg,#43e97b,#38f9d7);color:#333;padding:20px;border-radius:10px;text-align:center'},
                createEl('div', {style: 'font-size:28px;font-weight:bold'}, formatPrice(totalAmount)),
                createEl('div', {style: 'font-size:14px'}, 'Total')
            ),
            createEl('div', {style: 'background:linear-gradient(135deg,#fa709a,#fee140);color:#333;padding:20px;border-radius:10px;text-align:center'},
                createEl('div', {style: 'font-size:28px;font-weight:bold'}, formatPrice(savings)),
                createEl('div', {style: 'font-size:14px'}, 'Savings')
            )
        );
        
        var categoriesContainer = createEl('div', null);
        
        state.categories.forEach(function(cat) {
            var catProducts = state.products[cat.id] || [];
            var isExpanded = state.expandedCats[cat.id];
            
            var catCard = createEl('div', {style: 'margin-bottom:25px;border-radius:12px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,0.08)'},
                createEl('div', {
                    style: 'background:linear-gradient(135deg,#FF6A00,#fccfaf);padding:15px;display:flex;align-items:center;cursor:pointer',
                    onclick: function() { toggleCategory(cat.id); }
                }, [
                    createEl('h3', {style: 'margin:0;flex:1;color:#333'}, cat.name),
                    createEl('span', {style: 'background:rgba(255,255,255,0.8);padding:5px 12px;border-radius:20px;font-size:13px;color:#666'}, cat.product_count + ' products'),
                    createEl('span', {style: 'margin-left:15px;color:#666'}, isExpanded ? '▼' : '▶')
                ])
            );
            
            if (isExpanded) {
                if (catProducts.length === 0) {
                    var loadingDiv = createEl('div', {style: 'padding:20px;text-align:center;color:#666'}, 'Loading products...');
                    catCard.appendChild(loadingDiv);
                } else {
                    var thead = document.createElement('thead');
                    var theadRow = document.createElement('tr');
                    theadRow.innerHTML = '<th style="text-align:left;padding:12px;background:#f8f9fa;font-weight:600;color:#555;font-size:13px">Product</th><th style="text-align:left;padding:12px;background:#f8f9fa;font-weight:600;color:#555;font-size:13px">Price</th><th style="text-align:center;padding:12px;background:#f8f9fa;font-weight:600;color:#555;font-size:13px">Stock</th><th style="text-align:center;padding:12px;background:#f8f9fa;font-weight:600;color:#555;font-size:13px">Quantity</th><th style="text-align:right;padding:12px;background:#f8f9fa;font-weight:600;color:#555;font-size:13px">Subtotal</th><th style="text-align:center;padding:12px;background:#f8f9fa;font-weight:600;color:#555;font-size:13px">Actions</th>';
                    thead.appendChild(theadRow);
                    
                    var tbody = document.createElement('tbody');
                    catProducts.forEach(function(product) {
                        var cartItem = state.cart[String(product.id)];
                        var qty = cartItem ? cartItem.quantity : 0;
                        var subtotal = qty * product.offer_price;
                        var stockText = product.stock === 0 ? 'Out of Stock' : product.stock <= 5 ? 'Only ' + product.stock + ' left' : 'In Stock';
                        var stockStyle = product.stock === 0 ? 'background:#f8d7da;color:#721c24' : product.stock <= 5 ? 'background:#fff3cd;color:#856404' : 'background:#d4edda;color:#155724';
                        
                        var tr = createEl('tr', {style: 'border-bottom:1px solid #eee'}, [
                            createEl('td', {style: 'padding:15px'},
                                createEl('div', {style: 'display:flex;align-items:center;gap:15px'}, [
                                    renderProductImage(product),
                                    createEl('div', null, [
                                        createEl('div', {style: 'font-weight:500;color:#333'}, product.name),
                                        product.sku ? createEl('div', {style: 'font-size:12px;color:#999;margin-top:3px'}, 'SKU: ' + product.sku) : ''
                                    ])
                                ])
                            ),
                            createEl('td', {style: 'padding:15px;white-space:nowrap'},
                                createEl('span', {style: 'font-size:18px;font-weight:bold;color:#e74c3c'}, formatPrice(product.offer_price))
                            ),
                            createEl('td', {style: 'padding:15px;text-align:center'},
                                createEl('span', {style: 'font-size:12px;padding:3px 8px;border-radius:4px;' + stockStyle}, stockText)
                            ),
                            createEl('td', {style: 'padding:15px;text-align:center'},
                                createEl('div', {style: 'display:flex;align-items:center;justify-content:center;gap:0'}, [
                                    createEl('button', {style: 'width:36px;height:36px;border:1px solid #ddd;background:#f8f9fa;font-size:18px;cursor:pointer', onclick: function(e) { e.stopPropagation(); updateQuantity(product.id, qty - 1); }}, '-'),
                                    createEl('input', {type: 'number', value: qty, min: 0, max: product.stock, style: 'width:50px;height:36px;border:1px solid #ddd;border-left:none;border-right:none;text-align:center;font-size:14px'}),
                                    createEl('button', {style: 'width:36px;height:36px;border:1px solid #ddd;background:#f8f9fa;font-size:18px;cursor:pointer', onclick: function(e) { e.stopPropagation(); updateQuantity(product.id, qty + 1); }}, '+')
                                ])
                            ),
                            createEl('td', {style: 'padding:15px;text-align:right;font-weight:bold;color:#333;font-size:16px'}, formatPrice(subtotal)),
                            createEl('td', {style: 'padding:15px;text-align:center'},
                                qty > 0 
                                    ? createEl('span', {style: 'background:#27ae60;color:white;border:none;padding:8px 16px;border-radius:6px;font-size:13px'}, 'Added')
                                    : createEl('button', {style: 'background:linear-gradient(135deg,#667eea,#764ba2);color:white;border:none;padding:8px 16px;border-radius:6px;cursor:pointer;font-size:13px', onclick: function(e) { e.stopPropagation(); addToCart(product); }, disabled: product.stock === 0}, 'Add')
                            )
                        ]);
                        if (tr && tr.nodeType === 1) {
                            tbody.appendChild(tr);
                        }
                    });
                    
                    var table = document.createElement('table');
                    table.style.width = '100%';
                    table.style.borderCollapse = 'collapse';
                    table.appendChild(thead);
                    table.appendChild(tbody);
                    
                    var contentDiv = createEl('div', {style: 'padding:20px'}, table);
                    catCard.appendChild(contentDiv);
                }
            }
            
            categoriesContainer.appendChild(catCard);
        });
        
        var checkoutButtons = document.createElement('div');
        checkoutButtons.style.cssText = 'display:flex;gap:15px;flex-wrap:wrap';
        
        var clearCartBtn = document.createElement('button');
        clearCartBtn.style.cssText = 'background:#f8f9fa;color:#666;border:1px solid #ddd;padding:15px 25px;border-radius:8px;font-size:14px;cursor:pointer';
        clearCartBtn.textContent = 'Clear Cart';
        clearCartBtn.onclick = function() { 
            fetchJSON(API_BASE + '-cart-clear', {method: 'POST'}).then(function() {
                state.cart = {};
                render();
            }); 
        };
        
        var checkoutBtn = document.createElement('button');
        checkoutBtn.style.cssText = productCount === 0 
            ? 'background:#ccc;color:#666;border:none;padding:15px 40px;border-radius:8px;font-size:16px;font-weight:bold;cursor:not-allowed;min-width:180px'
            : 'background:linear-gradient(135deg,#43e97b,#38f9d7);color:#333;border:none;padding:15px 40px;border-radius:8px;font-size:16px;font-weight:bold;cursor:pointer;min-width:180px';
        checkoutBtn.textContent = 'Proceed to Checkout';
        checkoutBtn.onclick = function() { 
            if (productCount === 0) {
                return;
            }
            fetchJSON(API_BASE + '-cart-transfer', {method: 'POST'}).then(function(data) {
                if (data.success) {
                    window.location.href = BASE_PATH + '/checkout';
                } else {
                    alert(data.message || 'Failed to transfer cart');
                }
            }); 
        };
        
        checkoutButtons.appendChild(clearCartBtn);
        checkoutButtons.appendChild(checkoutBtn);
        
        var checkoutSection = createEl('div', {style: 'position:sticky;bottom:0;left:0;right:0;background:white;padding:20px;border-radius:12px 12px 0 0;box-shadow:0 -4px 20px rgba(0,0,0,0.15);margin-top:30px;display:flex !important;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:15px;z-index:9999'},
            createEl('div', {style: 'display:flex;gap:30px;flex-wrap:wrap'}, [
                createEl('div', {style: 'text-align:center'}, [
                    createEl('div', {style: 'font-size:24px;font-weight:bold;color:#333'}, String(productCount)),
                    createEl('div', {style: 'font-size:12px;color:#666;text-transform:uppercase'}, 'Products')
                ]),
                createEl('div', {style: 'text-align:center'}, [
                    createEl('div', {style: 'font-size:24px;font-weight:bold;color:#333'}, String(itemCount)),
                    createEl('div', {style: 'font-size:12px;color:#666;text-transform:uppercase'}, 'Items')
                ]),
                createEl('div', {style: 'text-align:center'}, [
                    createEl('div', {style: 'font-size:24px;font-weight:bold;color:#333'}, formatPrice(totalAmount)),
                    createEl('div', {style: 'font-size:12px;color:#666;text-transform:uppercase'}, 'Total')
                ]),
                savings > 0 ? createEl('div', {style: 'text-align:center'}, [
                    createEl('div', {style: 'font-size:24px;font-weight:bold;color:#27ae60'}, formatPrice(savings)),
                    createEl('div', {style: 'font-size:12px;color:#666;text-transform:uppercase'}, 'Savings')
                ]) : ''
            ]),
            checkoutButtons
        );
        
        container.innerHTML = '';
        container.appendChild(header);
        container.appendChild(summaryCards);
        container.appendChild(categoriesContainer);
        container.appendChild(checkoutSection);
    } catch (err) {
        console.error('Render error:', err);
        container.innerHTML = '<div style="padding:20px;color:red">Error rendering: ' + err.message + '</div>';
    }
}

loadCategories();