<?php include 'templates/header.php'; include 'templates/sidebar.php'; ?>

<div class="row" id="dashboardStats">
    <!-- Stats akan diisi oleh Fetch API -->
    <div class="col-md-3 mb-3"><div class="card bg-primary text-white shadow"><div class="card-body"><h6>Total Pengajuan</h6><h2 id="total_pengajuan">0</h2></div></div></div>
    <div class="col-md-3 mb-3"><div class="card bg-warning text-white shadow"><div class="card-body"><h6>Pending Review</h6><h2 id="pending_review">0</h2></div></div></div>
    <div class="col-md-3 mb-3"><div class="card bg-info text-white shadow"><div class="card-body"><h6>Pending Approval</h6><h2 id="pending_approval">0</h2></div></div></div>
    <div class="col-md-3 mb-3"><div class="card bg-success text-white shadow"><div class="card-body"><h6>Disetujui</h6><h2 id="disetujui">0</h2></div></div></div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-white"><h5 class="mb-0 text-primary">Grafik Pengajuan APD</h5></div>
            <div class="card-body"><canvas id="chartPengajuan" height="100"></canvas></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-header bg-white"><h5 class="mb-0 text-primary">Stock APD Terkini</h5></div>
            <div class="card-body" id="stockList">
                <!-- Diisi oleh Fetch API -->
            </div>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>