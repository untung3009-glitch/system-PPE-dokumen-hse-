<?php include 'templates/header.php'; include 'templates/sidebar.php'; ?>

<div class="card shadow border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-primary fw-bold"><i class="bi bi-graph-up me-2"></i>Laporan Pengajuan APD</h5>
        <select id="filterStatus" class="form-select form-select-sm" style="width: 200px;">
            <option value="all">Semua Status</option>
            <option value="Approved">Approved</option>
            <option value="Rejected">Rejected</option>
            <option value="Pending Review Safety">Pending Review Safety</option>
            <option value="Pending Review PM">Pending Review PM</option>
        </select>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Tanggal</th>
                        <th>User</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="reportBody">
                    <tr><td colspan="4" class="text-center">Loading data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
async function loadReport(status = 'all') {
    const res = await fetch('api/laporan.php?status=' + status);
    const data = await res.json();
    let html = '';
    if(data.length === 0) {
        html = '<tr><td colspan="4" class="text-center text-muted">Tidak ada data.</td></tr>';
    } else {
        data.forEach(item => {
            let badge = 'bg-secondary';
            if(item.status === 'Approved') badge = 'bg-success';
            else if(item.status === 'Rejected') badge = 'bg-danger';
            else if(item.status.includes('Review')) badge = 'bg-warning text-dark';
            
            html += `<tr><td>#${item.id}</td><td>${item.request_date}</td><td>${item.user_name}</td><td><span class="badge ${badge}">${item.status}</span></td></tr>`;
        });
    }
    document.getElementById('reportBody').innerHTML = html;
}

document.getElementById('filterStatus').addEventListener('change', function() {
    loadReport(this.value);
});

loadReport();
</script>

<?php include 'templates/footer.php'; ?>