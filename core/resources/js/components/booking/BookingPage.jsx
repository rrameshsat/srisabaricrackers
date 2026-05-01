import React, { useState, useEffect, useCallback } from 'react';
import axios from 'axios';

const API_BASE = '/api/booking';

function SummaryBox({ type, value, label }) {
    const icons = {
        products: '📦',
        items: '🛒',
        total: '💰',
        savings: '💚'
    };

    return (
        <div className={`summary-box ${type}`}>
            <div className="value">{value}</div>
            <div className="label">{label}</div>
        </div>
    );
}

function QuantityStepper({ value, onChange, min = 0, max = 999 }) {
    const handleDecrement = () => {
        if (value > min) {
            onChange(value - 1);
        }
    };

    const handleIncrement = () => {
        if (value < max) {
            onChange(value + 1);
        }
    };

    const handleInputChange = (e) => {
        const newValue = parseInt(e.target.value) || 0;
        if (newValue >= min && newValue <= max) {
            onChange(newValue);
        }
    };

    return (
        <div className="quantity-stepper">
            <button 
                className="stepper-btn minus" 
                onClick={handleDecrement}
                disabled={value <= min}
            >
                −
            </button>
            <input 
                type="number" 
                className="quantity-input" 
                value={value}
                onChange={handleInputChange}
                min={min}
                max={max}
            />
            <button 
                className="stepper-btn plus" 
                onClick={handleIncrement}
                disabled={value >= max}
            >
                +
            </button>
        </div>
    );
}

function ProductRow({ product, cartQuantity, onQuantityChange, onAddToCart, onRemoveFromCart }) {
    const subtotal = cartQuantity > 0 ? (product.offer_price * cartQuantity).toFixed(2) : '0.00';
    const stockStatus = product.stock === 0 ? 'out-of-stock' : product.stock <= 5 ? 'low-stock' : 'in-stock';
    const stockText = product.stock === 0 ? 'Out of Stock' : product.stock <= 5 ? `Only ${product.stock} left` : 'In Stock';
    const isInCart = cartQuantity > 0;

    return (
        <tr>
            <td data-label="Product">
                <div className="product-info">
                    <img 
                        src={product.image || '/assets/front/images/placeholder.png'} 
                        alt={product.name}
                        className="product-image"
                        onError={(e) => { e.target.src = '/assets/front/images/placeholder.png'; }}
                    />
                    <div>
                        <div className="product-name">{product.name}</div>
                        {product.sku && <div className="product-sku">SKU: {product.sku}</div>}
                    </div>
                </div>
            </td>
            <td data-label="Price" className="price-cell">
                <span className="offer-price">₹{product.offer_price.toFixed(2)}</span>
                {product.previous_price > product.offer_price && (
                    <>
                        <span className="previous-price">₹{product.previous_price.toFixed(2)}</span>
                        <span className="savings-badge">{product.savings}% OFF</span>
                    </>
                )}
            </td>
            <td data-label="Stock">
                <span className={`stock-status ${stockStatus}`}>{stockText}</span>
            </td>
            <td data-label="Quantity">
                <QuantityStepper
                    value={cartQuantity}
                    onChange={onQuantityChange}
                    min={0}
                    max={product.stock}
                />
            </td>
            <td data-label="Subtotal" className="subtotal-cell">
                ₹{subtotal}
            </td>
            <td data-label="Actions">
                <div className="cart-actions">
                    <button 
                        className={`add-cart-btn ${isInCart ? 'added' : ''}`}
                        onClick={() => onAddToCart(product)}
                        disabled={product.stock === 0}
                    >
                        {isInCart ? '✓ Added' : 'Add'}
                    </button>
                    {isInCart && (
                        <button 
                            className="remove-btn"
                            onClick={() => onRemoveFromCart(product.id)}
                        >
                            Remove
                        </button>
                    )}
                </div>
            </td>
        </tr>
    );
}

function CategorySection({ category, products, cart, onQuantityChange, onAddToCart, onRemoveFromCart, isExpanded, onToggle }) {
    return (
        <div className="category-card">
            <div className="category-header" onClick={onToggle}>
                <h3>{category.name}</h3>
                <span className="product-count">{category.product_count} products</span>
                <span className={`toggle-icon ${isExpanded ? '' : 'collapsed'}`}>▼</span>
            </div>
            {isExpanded && (
                <div className="category-content">
                    <table className="product-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {products.length === 0 ? (
                                <tr>
                                    <td colSpan="6" style={{ textAlign: 'center', padding: '30px' }}>
                                        No products available in this category
                                    </td>
                                </tr>
                            ) : (
                                products.map(product => (
                                    <ProductRow
                                        key={product.id}
                                        product={product}
                                        cartQuantity={cart[product.id]?.quantity || 0}
                                        onQuantityChange={(qty) => onQuantityChange(product.id, qty)}
                                        onAddToCart={onAddToCart}
                                        onRemoveFromCart={onRemoveFromCart}
                                    />
                                ))
                            )}
                        </tbody>
                    </table>
                </div>
            )}
        </div>
    );
}

function CheckoutButton({ cartSummary, onClearCart, onCheckout }) {
    return (
        <div className="checkout-section">
            <div className="checkout-info">
                <div className="checkout-info-item">
                    <div className="value">{cartSummary.product_count}</div>
                    <div className="label">Products</div>
                </div>
                <div className="checkout-info-item">
                    <div className="value">{cartSummary.item_count}</div>
                    <div className="label">Items</div>
                </div>
                <div className="checkout-info-item">
                    <div className="value">₹{cartSummary.total_amount.toFixed(2)}</div>
                    <div className="label">Total</div>
                </div>
                {cartSummary.savings > 0 && (
                    <div className="checkout-info-item">
                        <div className="value" style={{ color: '#27ae60' }}>₹{cartSummary.savings.toFixed(2)}</div>
                        <div className="label">Savings</div>
                    </div>
                )}
            </div>
            <div className="checkout-actions">
                <button className="clear-cart-btn" onClick={onClearCart} disabled={cartSummary.product_count === 0}>
                    Clear Cart
                </button>
                <button 
                    className="checkout-btn" 
                    onClick={onCheckout}
                    disabled={cartSummary.product_count === 0}
                >
                    Proceed to Checkout →
                </button>
            </div>
        </div>
    );
}

export default function BookingPage() {
    const [categories, setCategories] = useState([]);
    const [products, setProducts] = useState({});
    const [cart, setCart] = useState({});
    const [cartSummary, setCartSummary] = useState({
        items: {},
        product_count: 0,
        item_count: 0,
        total_amount: 0,
        previous_total: 0,
        savings: 0
    });
    const [expandedCategories, setExpandedCategories] = useState({});
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [message, setMessage] = useState(null);

    useEffect(() => {
        loadCart();
        loadCategories();
    }, []);

    const loadCart = async () => {
        try {
            const response = await axios.get(`${API_BASE}/cart`);
            if (response.data.success) {
                const cartData = response.data.data;
                setCart(cartData.items || {});
                setCartSummary({
                    items: cartData.items || {},
                    product_count: cartData.product_count,
                    item_count: cartData.item_count,
                    total_amount: cartData.total_amount,
                    previous_total: cartData.previous_total,
                    savings: cartData.savings
                });
            }
        } catch (err) {
            console.error('Error loading cart:', err);
        }
    };

    const loadCategories = async () => {
        try {
            const response = await axios.get(`${API_BASE}/categories`);
            if (response.data.success) {
                setCategories(response.data.data);
                const expanded = {};
                response.data.data.forEach((cat, index) => {
                    expanded[cat.id] = index === 0;
                });
                setExpandedCategories(expanded);
            }
            setLoading(false);
        } catch (err) {
            setError('Failed to load categories');
            setLoading(false);
        }
    };

    const loadProducts = async (categoryId) => {
        if (products[categoryId]) return;
        
        try {
            const response = await axios.get(`${API_BASE}/products/${categoryId}`);
            if (response.data.success) {
                setProducts(prev => ({
                    ...prev,
                    [categoryId]: response.data.data
                }));
            }
        } catch (err) {
            console.error('Error loading products:', err);
        }
    };

    const toggleCategory = (categoryId) => {
        setExpandedCategories(prev => ({
            ...prev,
            [categoryId]: !prev[categoryId]
        }));
        if (!products[categoryId]) {
            loadProducts(categoryId);
        }
    };

    const handleQuantityChange = async (itemId, quantity) => {
        try {
            const response = await axios.post(`${API_BASE}/cart/update`, {
                item_id: itemId,
                quantity: quantity
            });
            if (response.data.success) {
                const cartData = response.data.data;
                setCart(cartData.items || {});
                setCartSummary({
                    items: cartData.items || {},
                    product_count: cartData.product_count,
                    item_count: cartData.item_count,
                    total_amount: cartData.total_amount,
                    previous_total: cartData.previous_total,
                    savings: cartData.savings
                });
            }
        } catch (err) {
            setError(err.response?.data?.message || 'Failed to update cart');
            setTimeout(() => setError(null), 3000);
        }
    };

    const handleAddToCart = async (product) => {
        try {
            const currentQty = cart[product.id]?.quantity || 0;
            if (currentQty === 0) {
                const response = await axios.post(`${API_BASE}/cart/add`, {
                    item_id: product.id,
                    quantity: 1
                });
                if (response.data.success) {
                    const cartData = response.data.data;
                    setCart(cartData.items || {});
                    setCartSummary({
                        items: cartData.items || {},
                        product_count: cartData.product_count,
                        item_count: cartData.item_count,
                        total_amount: cartData.total_amount,
                        previous_total: cartData.previous_total,
                        savings: cartData.savings
                    });
                    showMessage('Added to cart!');
                }
            } else {
                const response = await axios.post(`${API_BASE}/cart/update`, {
                    item_id: product.id,
                    quantity: currentQty + 1
                });
                if (response.data.success) {
                    const cartData = response.data.data;
                    setCart(cartData.items || {});
                    setCartSummary({
                        items: cartData.items || {},
                        product_count: cartData.product_count,
                        item_count: cartData.item_count,
                        total_amount: cartData.total_amount,
                        previous_total: cartData.previous_total,
                        savings: cartData.savings
                    });
                }
            }
        } catch (err) {
            setError(err.response?.data?.message || 'Failed to add to cart');
            setTimeout(() => setError(null), 3000);
        }
    };

    const handleRemoveFromCart = async (itemId) => {
        try {
            const response = await axios.post(`${API_BASE}/cart/remove`, {
                item_id: itemId
            });
            if (response.data.success) {
                const cartData = response.data.data;
                setCart(cartData.items || {});
                setCartSummary({
                    items: cartData.items || {},
                    product_count: cartData.product_count,
                    item_count: cartData.item_count,
                    total_amount: cartData.total_amount,
                    previous_total: cartData.previous_total,
                    savings: cartData.savings
                });
                showMessage('Removed from cart');
            }
        } catch (err) {
            setError('Failed to remove from cart');
            setTimeout(() => setError(null), 3000);
        }
    };

    const handleClearCart = async () => {
        if (cartSummary.product_count === 0) return;
        
        if (confirm('Are you sure you want to clear the cart?')) {
            try {
                const response = await axios.post(`${API_BASE}/cart/clear`);
                if (response.data.success) {
                    setCart({});
                    setCartSummary({
                        items: {},
                        product_count: 0,
                        item_count: 0,
                        total_amount: 0,
                        previous_total: 0,
                        savings: 0
                    });
                    showMessage('Cart cleared');
                }
            } catch (err) {
                setError('Failed to clear cart');
                setTimeout(() => setError(null), 3000);
            }
        }
    };

    const handleCheckout = () => {
        window.location.href = '/checkout';
    };

    const showMessage = (msg) => {
        setMessage(msg);
        setTimeout(() => setMessage(null), 2000);
    };

    if (loading) {
        return (
            <div className="booking-container">
                <div className="loading">
                    <div className="loading-spinner"></div>
                    <p>Loading booking options...</p>
                </div>
            </div>
        );
    }

    if (error) {
        return (
            <div className="booking-container">
                <div className="error-message">{error}</div>
            </div>
        );
    }

    return (
        <div className="booking-container">
            <div className="booking-header">
                <h1>🧨 Quick Booking Mode</h1>
                <p>Select products and quantities to add to your cart</p>
            </div>

            {message && (
                <div className="success-message">{message}</div>
            )}

            <div className="summary-section">
                <SummaryBox 
                    type="products" 
                    value={cartSummary.product_count} 
                    label="Products Selected"
                />
                <SummaryBox 
                    type="items" 
                    value={cartSummary.item_count} 
                    label="Total Items"
                />
                <SummaryBox 
                    type="total" 
                    value={`₹${cartSummary.total_amount.toFixed(2)}`} 
                    label="Total Amount"
                />
                <SummaryBox 
                    type="savings" 
                    value={`₹${cartSummary.savings.toFixed(2)}`} 
                    label="Your Savings"
                />
            </div>

            <div className="categories-section">
                {categories.map(category => (
                    <CategorySection
                        key={category.id}
                        category={category}
                        products={products[category.id] || []}
                        cart={cart}
                        onQuantityChange={handleQuantityChange}
                        onAddToCart={handleAddToCart}
                        onRemoveFromCart={handleRemoveFromCart}
                        isExpanded={expandedCategories[category.id] || false}
                        onToggle={() => toggleCategory(category.id)}
                    />
                ))}
            </div>

            <CheckoutButton 
                cartSummary={cartSummary}
                onClearCart={handleClearCart}
                onCheckout={handleCheckout}
            />
        </div>
    );
}
