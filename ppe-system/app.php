<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - PPE Tambang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-warning shadow-sm">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1 fw-bold text-dark">PPE SAFETY MINE DASHBOARD</span>
            <div class="d-flex align-items-center">
                <span class="text-dark me-3" id="userRole"></span>
                <button class="btn btn-dark btn-sm" onclick="logout()">Logout</button>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item"><a class="nav-link text-white" href="#" onclick="showSection('dashboard')"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="#" onclick="showSection('form')"><i class="bi bi-plus-circle"></i> Ajukan PPE</a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="#" onclick="showSection('history')"><i class="bi bi-clock-history"></i> Riwayat & Approval</a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="#" onclick="exportExcel()"><i class="bi bi-file-earmark-excel"></i> Export Excel</a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="#" onclick="window.print()"><i class="bi bi-file-earmark-pdf"></i> Export PDF</a></li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 ms-sm-auto px-md-4">
                <!-- Dashboard Section -->
                <div id="section-dashboard">
                    <h3 class="mb-4">Statistik Pengajuan</h3>
                    <div class="row g-3 mb-4">
                        <div class="col-md-3"><div class="card bg-primary text-white p-3 shadow-sm"><h5>Total</h5><h2 id="statTotal">0</h2></div></div>
                        <div class="col-md-3"><div class="card bg-success text-white p-3 shadow-sm"><h5>Approved</h5><h2 id="statApproved">0</h2></div></div>
                        <div class="col-md-3"><div class="card bg-warning text-dark p-3 shadow-sm"><h5>Pending</h5><h2 id="statPending">0</h2></div></div>
                        <div class="col-md-3"><div class="card bg-danger text-white p-3 shadow-sm"><h5>Rejected</h5><h2 id="statRejected">0</h2></div></div>
                    </div>
                    <div class="card p-3 shadow-sm">
                        <h5>Grafik Penggunaan PPE</h5>
                        <canvas id="ppeChart" height="100"></canvas>
                    </div>
                </div>

                <!-- Form Section -->
                <div id="section-form" style="display:none;">
                    <h3 class="mb-4">Form Pengajuan PPE</h3>
                    <div class="card p-4 shadow-sm">
                        <form id="ppeForm" enctype="multipart/form-data">
                            <div class="row">
                                <div class="mb-3 col-md-6"><label>Nama Lengkap</label><input type="text" name="nama" class="form-control" required></div>
                                <div class="mb-3 col-md-6"><label>NIK</label><input type="text" name="nik" class="form-control" required></div>
                                <div class="mb-3 col-md-6"><label>Departemen</label><input type="text" name="departemen" class="form-control" required></div>
                                <div class="mb-3 col-md-6"><label>Jabatan</label><input type="text" name="jabatan" class="form-control" required></div>
                                <div class="mb-3 col-md-6"><label>Area Kerja</label><input type="text" name="area_kerja" class="form-control" placeholder="Pit A, Crushing Plant, dll" required></div>
                                <div class="mb-3 col-md-6"><label>Jenis PPE</label>
                                    <select name="jenis_ppe" class="form-control" required>
                                        <option value="Helm Safety">Helm Safety</option>
                                        <option value="Safety Shoes">Safety Shoes</option>
                                        <option value="Safety Glasses">Safety Glasses</option>
                                        <option value="Ear Plug">Ear Plug</option>
                                        <option value="Respirator/Masker">Respirator/Masker</option>
                                    </select>
                                </div>
                                <div class="mb-3 col-md-6"><label>Jumlah</label><input type="number" name="jumlah" class="form-control" min="1" required></div>
                                <div class="mb-3 col-md-6"><label>Upload Foto (Opsional)</label><input type="file" name="foto" class="form-control" accept="image/*"></div>
                                <div class="mb-3 col-12"><label>Alasan Pengajuan</label><textarea name="alasan" class="form-control" rows="3" required></textarea></div>
                            </div>
                            <button type="submit" class="btn btn-warning fw-bold">Kirim Pengajuan</button>
                        </form>
                    </div>
                </div>

                <!-- History Section -->
                <div id="section-history" style="display:none;">
                    <h3 class="mb-4">Riwayat & Persetujuan</h3>
                    <div class="card p-3 shadow-sm">
                        <input type="text" id="searchInput" class="form-control mb-3" placeholder="Cari berdasarkan nama, NIK, atau PPE...">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="historyTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Tanggal</th><th>Nama</th><th>PPE</th><th>Jumlah</th><th>Status</th><th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="historyBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Cek sesi & load awal
        document.addEventListener('DOMContentLoaded', () => {
            fetch('api.php?action=get_stats')
            .then(res => res.json())
            .then(data => {
                if(data.status === 'error') window.location.href = 'index.php';
                document.getElementById('statTotal').innerText = data.total;
                document.getElementById('statApproved').innerText = data.approved;
                document.getElementById('statPending').innerText = data.pending;
                document.getElementById('statRejected').innerText = data.rejected;
                renderChart(data.chart);
            });
            loadHistory();
        });

        // Chart JS
        let ppeChartInstance;
        function renderChart(chartData) {
            const ctx = document.getElementById('ppeChart').getContext('2d');
            if(ppeChartInstance) ppeChartInstance.destroy();
            ppeChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartData.map(c => c.jenis_ppe),
                    datasets: [{
                        label: 'Total PPE Diapprove',
                        data: chartData.map(c => c.total),
                        backgroundColor: '#ffc107'
                    }]
                }
            });
        }

        function showSection(section) {
            document.getElementById('section-dashboard').style.display = 'none';
            document.getElementById('section-form').style.display = 'none';
            document.getElementById('section-history').style.display = 'none';
            document.getElementById('section-' + section).style.display = 'block';
        }

        // Submit Form PPE
        document.getElementById('ppeForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const res = await fetch('api.php?action=submit_request', { method: 'POST', body: formData });
            const data = await res.json();
            if(data.status === 'success') {
                alert('Pengajuan berhasil dikirim!');
                this.reset();
                showSection('history');
                loadHistory();
            } else {
                alert('Gagal: ' + data.message);
            }
        });

        // Load History
        async function loadHistory() {
            const res = await fetch('api.php?action=get_requests');
            const data = await res.json();
            const tbody = document.getElementById('historyBody');
            tbody.innerHTML = '';
            const role = '<?php echo $_SESSION["role"] ?? ""; ?>';
            
            data.data.forEach(item => {
                let actionBtn = '';
                if ((role === 'Supervisor' && item.status === 'Pending Supervisor') ||
                    (role === 'Safety' && item.status === 'Pending Safety') ||
                    (role === 'Warehouse' && item.status === 'Pending Warehouse')) {
                    actionBtn = `
                        <button class="btn btn-success btn-sm" onclick="approve(${item.id}, 'approve')">Approve</button>
                        <button class="btn btn-danger btn-sm" onclick="approve(${item.id}, 'reject')">Reject</button>`;
                }
                
                let statusBadge = `<span class="badge bg-warning">${item.status}</span>`;
                if(item.status === 'Approved') statusBadge = `<span class="badge bg-success">${item.status}</span>`;
                if(item.status === 'Rejected') statusBadge = `<span class="badge bg-danger">${item.status}</span>`;

                tbody.innerHTML += `
                    <tr>
                        <td>${new Date(item.created_at).toLocaleDateString('id-ID')}</td>
                        <td>${item.nama}</td>
                        <td>${item.jenis_ppe}</td>
                        <td>${item.jumlah}</td>
                        <td>${statusBadge}</td>
                        <td>${actionBtn}</td>
                    </tr>`;
            });
        }

        // Approve/Reject Action
        async function approve(id, decision) {
            if(!confirm(`Yakin ingin ${decision} pengajuan ini?`)) return;
            const res = await fetch('api.php?action=approve_request', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ id, decision })
            });
            const data = await res.json();
            if(data.status === 'success') {
                alert('Status diperbarui!');
                loadHistory();
            } else {
                alert('Error: ' + data.message);
            }
        }

        // Search Filter
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#historyBody tr');
            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });

        // Export Excel (CSV)
        function exportExcel() {
            let table = document.getElementById('historyTable');
            let html = table.outerHTML;
            window.open('data:application/vnd.ms-excel,' + encodeURIComponent(html));
        }

        function logout() {
            fetch('api.php?action=logout').then(() => window.location.href = 'index.php');
        }
    </script>
</body>
</html>