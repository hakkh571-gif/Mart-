@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Bootstrap Icons CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
    body {
        background-color: #2dceee;
        overflow-x: hidden;
    }
    .pos-container {
        padding: 15px;
    }
    .product-card {
        background: #39bdf1;
        border: 1px solid #16dff5;
        border-radius: 12px;
        padding: 12px;
        position: relative;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .product-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        border-color: #ff5722;
    }
    .product-img {
        background: #5f96ce;
        border-radius: 20px;
        height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 8px;
        overflow: hidden;
    }
    .product-img img {
        height: 100%;
        width: 100%;
        object-fit: cover;
    }
    .btn-add-orange {
        position: absolute;
        bottom: 10px;
        right: 10px;
        background: #ff5722;
        color: white;
        border: none;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .cart-box {
        background: #20d62c;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        padding: 15px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .cart-items-list {
        max-height: 280px;
        overflow-y: auto;
    }
    .btn-checkout {
        background: #ff5722;
        color: white;
        font-weight: bold;
        padding: 12px;
        border-radius: 8px;
        border: none;
        width: 100%;
        font-size: 1.1rem;
    }
    .btn-checkout:hover {
        background: #e04818;
        color: white;
    }
    .qty-btn {
        width: 26px;
        height: 26px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
    }

    /* Thermal Receipt Printing Style */
    @media print {
        body * { visibility: hidden; }
        #receiptPrintArea, #receiptPrintArea * { visibility: visible; }
        #receiptPrintArea { position: absolute; left: 0; top: 0; width: 100%; }
        .modal-footer, .btn-close { display: none !important; }
    }
</style>

<div class="container-fluid pos-container">
    <div class="row g-3">
        <!-- ផ្នែកខាងឆ្វេង៖ បញ្ជីទំនិញ (Products List) -->
        <div class="col-lg-7 col-xl-8">
            <div class="d-flex justify-content-between align-items-center mb-3 bg-white p-3 rounded-3 border">
                <div>
                    <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-shop me-2"></i>ប្រព័ន្ធលក់ POS</h5>
                    <small class="text-muted" id="posDate"></small>
                </div>
                <div style="width: 280px;">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" id="searchInput" class="form-control border-start-0 ps-0" placeholder="ស្វែងរកទំនិញ..." onkeyup="filterProducts()">
                    </div>
                </div>
            </div>

            <!-- កន្លែងបង្ហាញ Grid ទំនិញ ដោយ Render តាម JS -->
            <div class="row g-2" id="productsGrid">
                <!-- Data will be loaded via JS -->
            </div>
        </div>

        <!-- ផ្នែកខាងស្តាំ៖ កន្ត្រក និងកន្លែងគិតលុយ (Checkout / Cart Panel) -->
        <div class="col-lg-5 col-xl-4">
            <div class="cart-box sticky-top" style="top: 15px;">
                <div class="d-flex justify-content-between align-items-center pb-2 mb-2 border-bottom">
                    <h6 class="fw-bold m-0 text-dark"><i class="bi bi-cart3 me-1"></i> កន្ត្រកទំនិញ (Cart)</h6>
                    <button class="btn btn-sm btn-outline-danger" onclick="clearCart()">
                        <i class="bi bi-trash me-1"></i> សម្អាត
                    </button>
                </div>

                <div class="cart-items-list mb-3" id="cartList"></div>

                <div class="bg-light p-3 rounded-3 mb-3 border">
                    <div class="d-flex justify-content-between text-muted mb-2">
                        <span>តម្លៃសរុប (Subtotal):</span>
                        <span class="fw-bold text-dark" id="subtotal">$0.00</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">បញ្ចុះតម្លៃ (%):</span>
                        <input type="number" id="discount" class="form-control form-control-sm text-end fw-bold" style="width: 80px;" value="0" min="0" max="100" oninput="calc()">
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">វិធីទូទាត់:</span>
                        <select id="paymentMethod" class="form-select form-select-sm fw-bold" style="width: 140px;">
                            <option value="cash">💵 Cash (ប្រាក់ស្រស់)</option>
                            <option value="khqr">📱 KHQR / ABA</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-between fw-bold border-top pt-2 mt-2 fs-5">
                        <span class="text-dark">ប្រាក់ត្រូវបង់:</span>
                        <span class="text-danger" id="total">$0.00</span>
                    </div>

                    <hr class="my-2">

                    <div class="row g-2 align-items-center">
                        <div class="col-6">
                            <label class="small text-muted fw-bold">ប្រាក់ទទួល ($):</label>
                            <input type="number" step="0.01" id="cashReceived" class="form-control form-control-sm fw-bold text-success" placeholder="0.00" oninput="calcChange()">
                        </div>
                        <div class="col-6 text-end">
                            <label class="small text-muted fw-bold">ប្រាក់អាប់ ($):</label>
                            <div class="fw-bold fs-6 text-primary" id="changeAmount">$0.00</div>
                        </div>
                    </div>
                </div>

                <button class="btn-checkout" id="btnCheckout" onclick="checkout()">
                    <i class="bi bi-credit-card-2-front me-1"></i> គិតលុយ (Checkout)
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal បង្ហាញវិក្កយបត្រ (Receipt Modal) -->
<div class="modal fade" id="receiptModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold fs-6"><i class="bi bi-receipt me-1"></i> វិក្កយបត្រ / Receipt</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-dark" id="receiptPrintArea" style="font-size: 0.85rem;">
                <div class="text-center mb-2">
                    <h6 class="fw-bold m-0">POS STORE</h6>
                    <small class="text-muted d-block">វិក្កយបត្រ / INVOICE</small>
                    <small id="recInvoiceNo" class="fw-bold text-primary d-block mt-1"></small>
                </div>
                <hr class="my-2">
                <div class="d-flex justify-content-between text-muted small">
                    <span>កាលបរិច្ឆេទ:</span>
                    <span id="recDate"></span>
                </div>
                <table class="table table-borderless table-sm my-2">
                    <thead>
                        <tr class="border-bottom text-muted">
                            <th>មុខទំនិញ</th>
                            <th class="text-center">ចំនួន</th>
                            <th class="text-end">សរុប</th>
                        </tr>
                    </thead>
                    <tbody id="recItems"></tbody>
                </table>
                <hr class="my-2">
                <div class="d-flex justify-content-between small">
                    <span>តម្លៃសរុប:</span>
                    <span id="recSubtotal" class="fw-bold"></span>
                </div>
                <div class="d-flex justify-content-between small text-danger">
                    <span>បញ្ចុះតម្លៃ:</span>
                    <span id="recDiscount" class="fw-bold"></span>
                </div>
                <div class="d-flex justify-content-between fs-6 fw-bold border-top pt-2 mt-1">
                    <span>ទូទាត់សរុប:</span>
                    <span id="recTotal" class="text-primary"></span>
                </div>
                <div class="d-flex justify-content-between small mt-2">
                    <span>ប្រាក់ទទួល:</span>
                    <span id="recCashReceived" class="fw-bold"></span>
                </div>
                <div class="d-flex justify-content-between small">
                    <span>ប្រាក់អាប់:</span>
                    <span id="recChange" class="fw-bold text-success"></span>
                </div>
                <div class="text-center mt-3 text-muted">
                    <small>សូមអរគុណ! សូមអញ្ជើញមកម្ដងទៀត!</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">បិទ</button>
                <button type="button" class="btn btn-sm btn-primary" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i> បោះពុម្ព
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // បំលែងទិន្នន័យទំនិញពី Laravel ទៅជា JavaScript Array សុទ្ធ
    const rawProducts = {!! json_encode($products ?? []) !!};
    let cart = [];

    document.getElementById('posDate').innerText = new Date().toLocaleDateString('km-KH', { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' });

    // មុខងារបង្ហាញទំនិញទាំងអស់
    function renderProducts() {
        const grid = document.getElementById('productsGrid');
        if (!rawProducts || rawProducts.length === 0) {
            grid.innerHTML = `
                <div class="col-12 text-center py-5 bg-white rounded-3 border">
                    <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                    មិនទាន់មានទិន្នន័យទំនិញនៅឡើយទេ
                </div>
            `;
            return;
        }

        let html = '';
        rawProducts.forEach(product => {
            const id = product.id;
            const name = product.name || product.title || 'N/A';
            const price = parseFloat(product.price || 0);
            const image = product.image || '';
            const searchName = name.toLowerCase();

            let imgHtml = '<i class="bi bi-box-seam fs-1 text-secondary"></i>';
            if (image) {
                imgHtml = `<img src="/storage/${image}" alt="${name}" onerror="this.src='/uploads/products/${image}'; this.onerror=function(){this.parentElement.innerHTML='<i class=\\'bi bi-box-seam fs-1 text-secondary\\'></i>';}">`;
            }

            html += `
                <div class="col-6 col-md-4 col-lg-3 product-item" data-name="${searchName}">
                    <div class="product-card" onclick="addToCart(${id}, '${escapeJsString(name)}', ${price}, '${escapeJsString(image)}')">
                        <div class="product-img">
                            ${imgHtml}
                        </div>
                        <div>
                            <div class="fw-bold text-truncate text-dark" style="font-size: 0.9rem;">${name}</div>
                            <div class="fw-bold text-primary mt-1">$${price.toFixed(2)}</div>
                        </div>
                        <button class="btn-add-orange" type="button">
                            <i class="bi bi-plus fs-5"></i>
                        </button>
                    </div>
                </div>
            `;
        });
        grid.innerHTML = html;
    }

    function escapeJsString(str) {
        return str.replace(/'/g, "\\'").replace(/"/g, '\\"');
    }

    function addToCart(id, name, price, image = '') {
        let item = cart.find(i => i.id === id);
        if (item) {
            item.qty++;
        } else {
            cart.push({ id, name, price: parseFloat(price), qty: 1, image: image });
        }
        renderCart();
    }

    function updateQty(id, change) {
        let item = cart.find(i => i.id === id);
        if (item) {
            item.qty += change;
            if (item.qty <= 0) {
                cart = cart.filter(i => i.id !== id);
            }
        }
        renderCart();
    }

    function removeItem(id) {
        cart = cart.filter(i => i.id !== id);
        renderCart();
    }

    function clearCart() {
        if(cart.length === 0) return;
        if(confirm('តើអ្នកប្រាកដជាចង់សម្អាតកន្ត្រកទំនិញនេះមែនទេ?')) {
            cart = [];
            renderCart();
        }
    }

    function renderCart() {
        let list = document.getElementById('cartList');
        if (cart.length === 0) {
            list.innerHTML = `
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-cart-x fs-2 d-block mb-1"></i>
                    គ្មានទំនិញក្នុងកន្ត្រក
                </div>
            `;
            calc();
            return;
        }

        list.innerHTML = '';
        cart.forEach((i) => {
            list.innerHTML += `
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <div style="max-width: 50%;">
                        <div class="fw-bold text-truncate" style="font-size: 0.88rem;">${i.name}</div>
                        <small class="text-muted">$${i.price.toFixed(2)}</small>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <button class="btn btn-sm btn-outline-secondary qty-btn" onclick="updateQty(${i.id}, -1)">-</button>
                        <span class="fw-bold px-1" style="font-size: 0.85rem;">${i.qty}</span>
                        <button class="btn btn-sm btn-outline-secondary qty-btn" onclick="updateQty(${i.id}, 1)">+</button>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold text-dark" style="font-size: 0.88rem;">$${(i.price * i.qty).toFixed(2)}</div>
                        <a href="javascript:void(0)" class="text-danger small" onclick="removeItem(${i.id})">
                            <i class="bi bi-trash"></i>
                        </a>
                    </div>
                </div>
            `;
        });
        calc();
    }

    function calc() {
        let sub = cart.reduce((s, i) => s + (i.price * i.qty), 0);
        let disc = parseFloat(document.getElementById('discount').value) || 0;
        if(disc < 0) disc = 0;
        if(disc > 100) disc = 100;
        
        let tot = sub - (sub * disc / 100);
        document.getElementById('subtotal').innerText = '$' + sub.toFixed(2);
        document.getElementById('total').innerText = '$' + tot.toFixed(2);
        calcChange();
    }

    function calcChange() {
        let sub = cart.reduce((s, i) => s + (i.price * i.qty), 0);
        let disc = parseFloat(document.getElementById('discount').value) || 0;
        let tot = sub - (sub * disc / 100);

        let cash = parseFloat(document.getElementById('cashReceived').value) || 0;
        let change = cash - tot;

        let changeElem = document.getElementById('changeAmount');
        if (change >= 0) {
            changeElem.innerText = '$' + change.toFixed(2);
            changeElem.className = "fw-bold fs-6 text-primary";
        } else {
            changeElem.innerText = '-$' + Math.abs(change).toFixed(2);
            changeElem.className = "fw-bold fs-6 text-danger";
        }
    }

    function filterProducts() {
        let q = document.getElementById('searchInput').value.toLowerCase();
        document.querySelectorAll('.product-item').forEach(e => {
            e.style.display = e.getAttribute('data-name').includes(q) ? 'block' : 'none';
        });
    }

    async function checkout() {
        if(cart.length === 0) return alert('សូមជ្រើសរើសទំនិញចូលកន្ត្រកជាមុនសិន!');

        let btn = document.getElementById('btnCheckout');
        btn.disabled = true;
        btn.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span> កំពុងដំណើរការ...`;

        let subtotal = cart.reduce((s, i) => s + (i.price * i.qty), 0);
        let discountPercent = parseFloat(document.getElementById('discount').value) || 0;
        let discountAmount = subtotal * (discountPercent / 100);
        let totalPrice = subtotal - discountAmount;
        let paymentMethod = document.getElementById('paymentMethod').value;
        let cashReceived = parseFloat(document.getElementById('cashReceived').value) || 0;

        let payload = {
            cart: cart,
            subtotal: subtotal,
            discount_percent: discountPercent,
            discount_amount: discountAmount,
            total_price: totalPrice,
            payment_method: paymentMethod
        };

        try {
            let response = await fetch("{{ route('pos.store') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(payload)
            });

            let result = await response.json();

            if (response.ok && result.success) {
                showReceiptModal(cart, subtotal, discountPercent, discountAmount, totalPrice, cashReceived);
                
                cart = [];
                document.getElementById('discount').value = 0;
                document.getElementById('cashReceived').value = '';
                renderCart();
            } else {
                alert('⚠️ មានបញ្ហា៖ ' + (result.message || 'មិនអាចដំណើរការទូទាត់ប្រាក់បានទេ!'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('⚠️ មានបញ្ហាក្នុងការភ្ជាប់ទៅកាន់ Server!');
        } finally {
            btn.disabled = false;
            btn.innerHTML = `<i class="bi bi-credit-card-2-front me-1"></i> គិតលុយ (Checkout)`;
        }
    }

    function showReceiptModal(cartItems, subtotal, discountPercent, discountAmount, totalPrice, cashReceived) {
        document.getElementById('recInvoiceNo').innerText = '#INV-' + Math.floor(100000 + Math.random() * 900000);
        document.getElementById('recDate').innerText = new Date().toLocaleString('km-KH');
        
        let itemsHtml = '';
        cartItems.forEach(item => {
            itemsHtml += `
                <tr>
                    <td>${item.name}</td>
                    <td class="text-center">x${item.qty}</td>
                    <td class="text-end">$${(item.price * item.qty).toFixed(2)}</td>
                </tr>
            `;
        });
        document.getElementById('recItems').innerHTML = itemsHtml;
        document.getElementById('recSubtotal').innerText = '$' + subtotal.toFixed(2);
        document.getElementById('recDiscount').innerText = discountPercent + '% (-$' + discountAmount.toFixed(2) + ')';
        document.getElementById('recTotal').innerText = '$' + totalPrice.toFixed(2);
        
        let cash = cashReceived > 0 ? cashReceived : totalPrice;
        let change = cash - totalPrice;
        document.getElementById('recCashReceived').innerText = '$' + cash.toFixed(2);
        document.getElementById('recChange').innerText = '$' + (change > 0 ? change.toFixed(2) : '0.00');

        let modal = new bootstrap.Modal(document.getElementById('receiptModal'));
        modal.show();
    }

    // ចាប់ផ្តើមដំណើរការ
    renderProducts();
    renderCart();
</script>
@endsection