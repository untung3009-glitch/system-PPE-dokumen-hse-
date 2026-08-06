<?php include 'templates/header.php'; include 'templates/sidebar.php'; ?>

<div class="card shadow border-0">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 text-primary fw-bold"><i class="bi bi-box-seam me-2"></i>Manajemen Stock APD</h5>
    </div>
    <div class="card-body">
        <div class="row" id="stockCards">
            <!-- Diisi oleh JS -->
        </div>
    </div>
</div>

<script>
async function loadStockCards() {
    const res = await fetch('api/apd.php?action=fetch');
    const data = await res.json();
    let html = '';
    
    data.forEach(item => {
        let status = 'Aman';
        let badge = 'bg-success';
        let icon = 'bi-check-circle-fill';
        
        if(item.stock === 0) {
            status = 'Habis'; badge = 'bg-danger'; icon = 'bi-x-circle-fill';
        } else if(item.stock < 20) {
            status = 'Menipis'; badge = 'bg-warning text-dark'; icon = 'bi-exclamation-triangle-fill';
        }

        html += `
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="bi ${icon} fs-1 ${badge.replace('bg-', 'text-').replace(' text-dark','')}"></i>
                        <h5 class="mt-2 mb-0">${item.name}</h5>
                        <p class="text-muted mb-1">Ukuran: ${item.size}</p>
                        <hr>
                        <h2 class="fw-bold text-primary">${item.stock}</h2>
                        <span class="badge ${badge}">${status}</span>
                    </div>
                </div>
            </div>
        `;
    });
    
    document.getElementById('stockCards').innerHTML = html;
}
loadStockCards();
</script>

<?php include 'templates/footer.php'; ?>