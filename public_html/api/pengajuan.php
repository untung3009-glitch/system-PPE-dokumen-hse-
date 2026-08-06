<?php include 'templates/header.php'; include 'templates/sidebar.php'; ?>

<div class="row">
    <div class="col-md-5">
        <div class="card shadow border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 text-primary fw-bold"><i class="bi bi-file-earmark-plus me-2"></i>Form Pengajuan APD</h5>
            </div>
            <div class="card-body">
                <form id="formPengajuan">
                    <input type="hidden" name="action" value="store">
                    <div id="apd-list-container">
                        <!-- Row pertama -->
                        <div class="row mb-2 align-items-end">
                            <div class="col-7">
                                <label class="form-label small">Pilih APD</label>
                                <select name="apd_id[]" class="form-select apd-select" required>
                                    <option value="">Loading...</option>
                                </select>
                            </div>
                            <div class="col-3">
                                <label class="form-label small">Qty</label>
                                <input type="number" name="quantity[]" class="form-control" min="1" value="1" required>
                            </div>
                            <div class="col-2">
                                <button type="button" class="btn btn-danger w-100" onclick="removeRow(this)"><i class="bi bi-dash"></i></button>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-secondary mt-2" onclick="addRow()"><i class="bi bi-plus"></i> Tambah Item</button>
                    <hr>
                    <button type="submit" class="btn btn-primary w-100 fw-bold"><i class="bi bi-send"></i> Ajukan Permintaan</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card shadow border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 text-primary fw-bold"><i class="bi bi-clock-history me-2"></i>Riwayat Pengajuan</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead class="table-light">
                            <tr><th>ID</th><th>Tanggal</th><th>Status</th></tr>
                        </thead>
                        <tbody id="historyTable">
                            <!-- Diisi oleh JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let apdListGlobal = [];

// Ambil daftar APD untuk Dropdown
async function loadAPDOptions() {
    const res = await fetch('api/pengajuan.php?action=fetch_apd_list');
    apdListGlobal = await res.json();
    
    document.querySelectorAll('.apd-select').forEach(select => {
        fillAPDOptions(select);
    });
}

function fillAPDOptions(selectElement) {
    selectElement.innerHTML = '<option value="">-- Pilih APD --</option>';
    apdListGlobal.forEach(apd => {
        selectElement.innerHTML += `<option value="${apd.id}">${apd.name} (${apd.size}) - Stok: ${apd.stock}</option>`;
    });
}

function addRow() {
    const container = document.getElementById('apd-list-container');
    const html = `
        <div class="row mb-2 align-items-end">
            <div class="col-7">
                <select name="apd_id[]" class="form-select apd-select" required></select>
            </div>
            <div class="col-3">
                <input type="number" name="quantity[]" class="form-control" min="1" value="1" required>
            </div>
            <div class="col-2">
                <button type="button" class="btn btn-danger w-100" onclick="removeRow(this)"><i class="bi bi-dash"></i></button>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    const newSelect = container.lastElementChild.querySelector('.apd-select');
    fillAPDOptions(newSelect);
}

function removeRow(button) {
    const container = document.getElementById('apd-list-container');
    if(container.children.length > 1) {
        button.closest('.row').remove();
    } else {
        Swal.fire('Info', 'Minimal 1 item harus ada', 'info');
    }
}

// Submit Pengajuan
document.getElementById('formPengajuan').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    Swal.fire({
        title: 'Kirim Pengajuan?',
        text: "Pastikan data APD dan jumlah sudah benar.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Kirim!'
    }).then(async (result) => {
        if (result.isConfirmed) {
            const res = await fetch('api/pengajuan.php', { method: 'POST', body: formData });
            const data = await res.json();
            if(data.status === 'success') {
                Swal.fire('Berhasil!', data.message, 'success');
                loadHistory();
                this.reset();
                loadAPDOptions(); // Refresh stok di dropdown
            } else {
                Swal.fire('Gagal!', data.message, 'error');
            }
        }
    });
});

// Load History Pengajuan
async function loadHistory() {
    const res = await fetch('api/pengajuan.php?action=history');
    const data = await res.json();
    let html = '';
    data.forEach(item => {
        let badge = 'bg-secondary';
        if(item.status === 'Approved') badge = 'bg-success';
        else if(item.status === 'Rejected') badge = 'bg-danger';
        else if(item.status.includes('Review')) badge = 'bg-warning text-dark';
        
        html += `<tr><td>#${item.id}</td><td>${item.request_date}</td><td><span class="badge ${badge}">${item.status}</span></td></tr>`;
    });
    document.getElementById('historyTable').innerHTML = html;
}

// Init
loadAPDOptions();
loadHistory();
</script>

<?php include 'templates/footer.php'; ?>