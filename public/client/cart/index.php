<?php
require_once __DIR__ . '/../../../app/bootstrap.php';
require_once __DIR__ . '/../../../app/config/db_connect.php';
require_once __DIR__ . '/../../../app/controllers/StoreCartController.php';

$cartController = new StoreCartController($pdo);
$page_title = 'My Cart';
$page_header = 'Shopping Cart';
$page_subheader = 'Review and manage your items';

ob_start();

// Check if logged in
if (!isLoggedIn()) {
    redirect('/Ismano/public/auth/login.php?redirect=' . urlencode('/Ismano/public/client/cart/'));
}

$cart = $cartController->getCart();
$savedItems = $cartController->getSavedItems();

?>

<style>
.cart-item {
    border-bottom: 1px solid #e0e0e0;
    padding: 20px 0;
}
.cart-item:last-child {
    border-bottom: none;
}
.cart-item-image {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 8px;
}
.quantity-input {
    width: 80px;
    text-align: center;
}
.cart-summary {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 20px;
}
</style>

<div class="row">
    <div class="col-lg-8">
        <div class="card-modern">
            <div class="card-header-modern">
                <i class="fas fa-shopping-cart me-2"></i> Cart Items (<?php echo $cart['count']; ?>)
            </div>
            <div class="card-body">
                <?php if (empty($cart['items'])): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                        <p class="mb-3">Your cart is empty.</p>
                        <a href="/Ismano/public/store/" class="btn btn-primary">
                            <i class="fas fa-store me-2"></i> Continue Shopping
                        </a>
                    </div>
                <?php else: ?>
                    <?php foreach ($cart['items'] as $item): ?>
                        <div class="cart-item" data-item-id="<?php echo $item['id']; ?>">
                            <div class="row align-items-center">
                                <div class="col-md-2">
                                    <?php if ($item['featured_image']): ?>
                                        <img src="<?php echo $item['featured_image']; ?>" class="cart-item-image w-100">
                                    <?php else: ?>
                                        <div class="bg-light d-flex align-items-center justify-content-center" style="width: 100px; height: 100px; border-radius: 8px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-4">
                                    <h6 class="mb-1">
                                        <a href="/Ismano/public/store/details.php?slug=<?php echo $item['slug']; ?>" class="text-decoration-none text-dark">
                                            <?php echo htmlspecialchars($item['name']); ?>
                                        </a>
                                    </h6>
                                    <small class="text-muted">$<?php echo number_format($item['price'], 2); ?> each</small>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group quantity-control" style="width: 120px;">
                                        <button class="btn btn-outline-secondary btn-sm update-qty" data-change="-1">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="number" class="form-control form-control-sm text-center quantity-input" 
                                               value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['stock_quantity']; ?>">
                                        <button class="btn btn-outline-secondary btn-sm update-qty" data-change="1">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <strong>$<?php echo number_format($item['subtotal'], 2); ?></strong>
                                </div>
                                <div class="col-md-1">
                                    <button class="btn btn-sm btn-outline-danger remove-item" data-item-id="<?php echo $item['id']; ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-12">
                                    <button class="btn btn-sm btn-link text-muted save-for-later" data-product-id="<?php echo $item['product_id']; ?>">
                                        <i class="far fa-heart me-1"></i> Save for Later
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if (!empty($savedItems)): ?>
            <div class="card-modern mt-4">
                <div class="card-header-modern">
                    <i class="far fa-heart me-2"></i> Saved for Later (<?php echo count($savedItems); ?>)
                </div>
                <div class="card-body">
                    <?php foreach ($savedItems as $item): ?>
                        <div class="cart-item">
                            <div class="row align-items-center">
                                <div class="col-md-2">
                                    <?php if ($item['featured_image']): ?>
                                        <img src="<?php echo $item['featured_image']; ?>" class="cart-item-image w-100">
                                    <?php else: ?>
                                        <div class="bg-light d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; border-radius: 8px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-7">
                                    <h6 class="mb-1"><?php echo htmlspecialchars($item['name']); ?></h6>
                                    <small class="text-muted">$<?php echo number_format($item['price'], 2); ?></small>
                                </div>
                                <div class="col-md-3">
                                    <button class="btn btn-sm btn-primary move-to-cart" data-product-id="<?php echo $item['product_id']; ?>">
                                        <i class="fas fa-shopping-cart me-1"></i> Move to Cart
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="col-lg-4">
        <div class="cart-summary">
            <h5 class="mb-3">Order Summary</h5>
            <div class="d-flex justify-content-between mb-2">
                <span>Subtotal:</span>
                <strong>$<?php echo number_format($cart['subtotal'], 2); ?></strong>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span>Shipping:</span>
                <strong>Calculated at checkout</strong>
            </div>
            <hr>
            <div class="d-flex justify-content-between mb-3">
                <span class="fw-bold">Total:</span>
                <span class="fw-bold fs-5">$<?php echo number_format($cart['total'], 2); ?></span>
            </div>
            <button class="btn btn-primary w-100 checkout-btn" <?php echo empty($cart['items']) ? 'disabled' : ''; ?>>
                <i class="fas fa-credit-card me-2"></i> Proceed to Checkout
            </button>
            <a href="/Ismano/public/store/" class="btn btn-outline-secondary w-100 mt-2">
                <i class="fas fa-store me-2"></i> Continue Shopping
            </a>
        </div>
    </div>
</div>

<script>
// Update quantity
document.querySelectorAll('.update-qty').forEach(btn => {
    btn.addEventListener('click', async function() {
        const cartItem = this.closest('.cart-item');
        const itemId = cartItem.dataset.itemId;
        const qtyInput = cartItem.querySelector('.quantity-input');
        let newQty = parseInt(qtyInput.value);
        const change = parseInt(this.dataset.change);
        
        newQty += change;
        if (newQty < 1) newQty = 1;
        
        qtyInput.value = newQty;
        
        // Update via API
        const response = await fetch('/Ismano/public/api/store/cart/update.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ item_id: itemId, quantity: newQty })
        });
        
        if (response.ok) {
            location.reload();
        }
    });
});

// Manual quantity change
document.querySelectorAll('.quantity-input').forEach(input => {
    input.addEventListener('change', async function() {
        const cartItem = this.closest('.cart-item');
        const itemId = cartItem.dataset.itemId;
        let newQty = parseInt(this.value);
        if (isNaN(newQty) || newQty < 1) newQty = 1;
        
        const response = await fetch('/Ismano/public/api/store/cart/update.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ item_id: itemId, quantity: newQty })
        });
        
        if (response.ok) {
            location.reload();
        }
    });
});

// Remove item
document.querySelectorAll('.remove-item').forEach(btn => {
    btn.addEventListener('click', async function() {
        if (!confirm('Remove this item from cart?')) return;
        
        const itemId = this.dataset.itemId;
        const response = await fetch('/Ismano/public/api/store/cart/remove.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ item_id: itemId })
        });
        
        if (response.ok) {
            location.reload();
        }
    });
});

// Save for later
document.querySelectorAll('.save-for-later').forEach(btn => {
    btn.addEventListener('click', async function() {
        const productId = this.dataset.productId;
        const response = await fetch('/Ismano/public/api/store/cart/save_for_later.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ product_id: productId })
        });
        
        if (response.ok) {
            location.reload();
        }
    });
});

// Move to cart
document.querySelectorAll('.move-to-cart').forEach(btn => {
    btn.addEventListener('click', async function() {
        const productId = this.dataset.productId;
        const response = await fetch('/Ismano/public/api/store/cart/move_to_cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ product_id: productId })
        });
        
        if (response.ok) {
            location.reload();
        }
    });
});

// Checkout
document.querySelector('.checkout-btn')?.addEventListener('click', function() {
    alert('Checkout feature coming soon! Your cart data is saved.');
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../templates/client/layout.php';
?>