@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold m-0">ផ្ទាំងគ្រប់គ្រង (Dashboard)</h4>
        <span class="badge bg-success px-3 py-2" id="liveIndicator">● Live Updates</span>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- ១. ផ្នែកបង្ហាញ Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-primary border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">ចំណូលសរុបថ្ងៃនេះ</div>
                        <h3 class="fw-bold text-primary mb-0">${{ number_format($todayRevenue ?? 0, 2) }}</h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary">
                        <i class="bi bi-currency-dollar fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-success border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">ភេសជ្ជៈលក់បានថ្ងៃនេះ</div>
                        <h3 class="fw-bold text-success mb-0">{{ number_format($todayCupsSold ?? 0) }} ពែង</h3>
                    </div>
                    <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success">
                        <i class="bi bi-cup-hot fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-info border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">បុគ្គលិកសរុប</div>
                        <h3 class="fw-bold text-info mb-0">{{ number_format($totalStaff ?? 0) }} នាក់</h3>
                    </div>
                    <div class="bg-info bg-opacity-10 p-3 rounded-circle text-info">
                        <i class="bi bi-people fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ២. ផ្នែកបង្ហាញ ក្រាបសសរ ពីរ -->
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                <h6 class="fw-bold mb-3">ក្រាបសសរបង្ហាញប្រាក់ចំណូល ៧ ថ្ងៃចុងក្រោយ</h6>
                <div style="position: relative; height: 300px; width: 100%;">
                    <canvas id="barSalesChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                <h6 class="fw-bold mb-3">ក្រាបសសរបង្ហាញទំនិញដែលលក់បានច្រើនជាងគេ</h6>
                <div style="position: relative; height: 300px; width: 100%;">
                    <canvas id="barProductsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- ៣. តារាងរបាយការណ៍ភេសជ្ជៈ -->
    <div class="row g-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                <h6 class="fw-bold mb-3">បញ្ជីភេសជ្ជៈលក់ចុងក្រោយ</h6>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>ឈ្មោះភេសជ្ជៈ/ទំនិញ</th>
                                <th>ចំនួនលក់បាន (ពែង)</th>
                                <th>ទឹកប្រាក់សរុប ($)</th>
                                <th class="text-center">សកម្មភាព (Actions)</th>
                            </tr>
                        </thead>
                        <tbody id="drinkReportTableBody">
                            @forelse($dailyDrinkReport as $index => $item)
                                @php
                                    $productId = $item->id ?? $index + 1;
                                    $productName = $item->product_name ?? $item->name ?? 'N/A';
                                    $qty = $item->total_qty ?? $item->stock ?? 0;
                                    $amount = $item->total_amount ?? $item->price ?? 0;
                                @endphp
                                <tr id="row-product-{{ $productId }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td class="fw-semibold product-name">{{ $productName }}</td>
                                    <td><span class="badge bg-secondary product-qty">{{ $qty }}</span></td>
                                    <td class="text-success fw-bold product-amount">${{ number_format($amount, 2) }}</td>
                                    <td class="text-center">
                                        <!-- ប៊ូតុង Edit -->
                                        <button class="btn btn-sm btn-outline-primary me-1" 
                                                onclick="openEditModal('{{ $productId }}', '{{ addslashes($productName) }}', {{ $qty }}, {{ $amount }})">
                                            <i class="bi bi-pencil-square"></i> កែប្រែ
                                        </button>
                                        <!-- ប៊ូតុង Delete -->
                                        <button class="btn btn-sm btn-outline-danger" 
                                                onclick="deleteProduct('{{ $productId }}')">
                                            <i class="bi bi-trash"></i> លុប
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">មិនទាន់មានទិន្នន័យលក់ទេ</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal កែប្រែ (Edit Modal) -->
<div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="editProductModalLabel"><i class="bi bi-pencil-square me-2"></i>កែប្រែទិន្នន័យភេសជ្ជៈ</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editProductForm">
                    @csrf
                    <input type="hidden" id="edit_product_id">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">ឈ្មោះភេសជ្ជៈ</label>
                        <input type="text" class="form-control" id="edit_product_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">ចំនួនលក់បាន (ពែង)</label>
                        <input type="number" class="form-control" id="edit_product_qty" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">ទឹកប្រាក់សរុប ($)</label>
                        <input type="number" step="0.01" class="form-control" id="edit_product_amount" min="0" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">បោះបង់</button>
                <button type="button" class="btn btn-primary rounded-3" onclick="saveProductEdit()">រក្សាទុកការប្រែប្រួល</button>
            </div>
        </div>
    </div>
</div>

<!-- Icons & Chart.js CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    let barChart, productsChart;

    document.addEventListener("DOMContentLoaded", function () {
        // Chart 1: Sales Revenue
        const ctx1 = document.getElementById('barSalesChart').getContext('2d');
        const gradientBg1 = ctx1.createLinearGradient(0, 0, 0, 300);
        gradientBg1.addColorStop(0, 'rgba(13, 110, 253, 0.9)');
        gradientBg1.addColorStop(1, 'rgba(13, 110, 253, 0.2)');

        barChart = new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    label: 'ប្រាក់ចំណូលសរុប ($)',
                    data: [],
                    backgroundColor: gradientBg1,
                    borderColor: '#0d6efd',
                    borderWidth: 2,
                    borderRadius: 8,
                    barThickness: 30
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { grid: { display: false } },
                    y: {
                        beginAtZero: true,
                        ticks: { callback: function(value) { return '$' + value; } }
                    }
                }
            }
        });

        // Chart 2: Top Products
        const ctx2 = document.getElementById('barProductsChart').getContext('2d');
        const gradientBg2 = ctx2.createLinearGradient(0, 0, 0, 300);
        gradientBg2.addColorStop(0, 'rgba(25, 135, 84, 0.9)');
        gradientBg2.addColorStop(1, 'rgba(25, 135, 84, 0.2)');

        productsChart = new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    label: 'ចំនួនលក់បាន (ពែង/ចំនួន)',
                    data: [],
                    backgroundColor: gradientBg2,
                    borderColor: '#37b8dc',
                    borderWidth: 2,
                    borderRadius: 8,
                    barThickness: 30
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { grid: { display: false } },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            callback: function(value) { return value + ' ពែង'; }
                        }
                    }
                }
            }
        });

        updateDashboardRealtime();
        setInterval(updateDashboardRealtime, 5000);
    });

    function updateDashboardRealtime() {
        // Fetch Sales Revenue Chart
        fetch("{{ route('dashboard.chart') }}")
            .then(response => response.json())
            .then(res => {
                if (barChart && res.labels && res.data) {
                    barChart.data.labels = res.labels;
                    barChart.data.datasets[0].data = res.data;
                    barChart.update('none'); // Update ដោយមិនចាំបាច់ Animation រំខាន
                }
            }).catch(e => console.error(e));

        // Fetch Top Products Chart
        fetch("{{ route('dashboard.products_chart') }}")
            .then(response => response.json())
            .then(res => {
                if (productsChart && res.labels && res.data) {
                    productsChart.data.labels = res.labels;
                    productsChart.data.datasets[0].data = res.data;
                    productsChart.update('none');
                }
            }).catch(e => console.error(e));
    }

    // បើក Modal កែប្រែ
    function openEditModal(id, name, qty, amount) {
        document.getElementById('edit_product_id').value = id;
        document.getElementById('edit_product_name').value = name;
        document.getElementById('edit_product_qty').value = qty;
        document.getElementById('edit_product_amount').value = amount;
        
        var editModalElement = document.getElementById('editProductModal');
        var editModal = bootstrap.Modal.getInstance(editModalElement) || new bootstrap.Modal(editModalElement);
        editModal.show();
    }

    // រក្សាទុកការកែប្រែ (AJAX)
    function saveProductEdit() {
        let id = document.getElementById('edit_product_id').value;
        let name = document.getElementById('edit_product_name').value;
        let qty = document.getElementById('edit_product_qty').value;
        let amount = document.getElementById('edit_product_amount').value;

        fetch("{{ route('dashboard.product.update') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ id: id, name: name, qty: qty, amount: amount })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                let row = document.getElementById('row-product-' + id);
                if(row) {
                    row.querySelector('.product-name').innerText = name;
                    row.querySelector('.product-qty').innerText = qty;
                    row.querySelector('.product-amount').innerText = '$' + parseFloat(amount).toFixed(2);
                }
                
                var editModalElement = document.getElementById('editProductModal');
                var modalInstance = bootstrap.Modal.getInstance(editModalElement);
                if(modalInstance) modalInstance.hide();

                // អាប់ដេត Chart ភ្លាមៗក្រោយកែប្រែ
                updateDashboardRealtime();
                alert(data.message);
            } else {
                alert(data.message || 'មានបញ្ហាមិនអាចកែប្រែបាន!');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('មានបញ្ហាក្នុងការផ្ញើទិន្នន័យ!');
        });
    }

    // លុបទិន្នន័យ (AJAX)
    function deleteProduct(id) {
        if(!confirm('តើអ្នកពិតជាចង់លុបទិន្នន័យនេះមែនទេ?')) return;

        fetch("{{ url('/dashboard/product/delete') }}/" + id, {
            method: "DELETE",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                let row = document.getElementById('row-product-' + id);
                if(row) row.remove();
                
                // អាប់ដេត Chart ភ្លាមៗក្រោយលុប
                updateDashboardRealtime();
                alert(data.message);
            } else {
                alert(data.message || 'មិនអាចលុបបានទេ!');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('មានបញ្ហាក្នុងការលុប!');
        });
    }
</script>
@endsection