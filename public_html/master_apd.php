<?php include 'templates/header.php'; include 'templates/sidebar.php'; ?>

<div class="card shadow border-0">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 text-primary fw-bold"><i class="bi bi-box-seam me-2"></i>Master APD</h5>
        <button class="btn btn-primary btn-sm" onclick="openModal()"><i class="bi bi-plus-lg"></i> Tambah APD</button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nama APD</th>
                        <th>Ukuran</th>
                        <th>Stock</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="apdTableBody">
                    <!-- Data akan diisi oleh JS -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah/Edit APD -->
<div class="modal fade" id="apdModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modalTitle">Tambah APD</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="apdForm">
        <div class="modal-body">
          <input type="hidden" id="apd_id" name="id">
          <input type="hidden" name="action" id="formAction" value="store">
          <div class="mb-3">
            <label class="form-label">Nama APD</label>
            <input type="text" name="name" id="apd_name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Ukuran</label>
            <input type="text" name="size" id="apd_size" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Jumlah Stock</label>
            <input type="number" name="stock" id="apd_stock" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
// JavaScript khusus Master APD
async function fetchAPD() {
    const res = await fetch('api/apd.php?action=fetch');
    const data = await res.json();
    let html = '';
    let no = 1;
    data.forEach(item => {
        html += `
            <tr>
                <td>${no++}</td>
                <td>${item.name}</td>
                <td>${item.size}</td>
                <td><span class="badge ${item.stock < 20 ? 'bg-danger' : 'bg-success'}">${item.stock}</span></td>
                <td>
                    <button class="btn btn-sm btn-warning btn-edit" data-id="${item.id}" data-name="${item.name}" data-size="${item.size}" data-stock="${item.stock}"><i class="bi bi-pencil"></i></button>
                    <button class="btn btn-sm btn-danger btn-delete" data-id="${item.id}"><i class="bi bi-trash"></i></button>
                </td>
            </tr>
        `;
    });
    document.getElementById('apdTableBody').innerHTML = html;
}

function openModal(editData = null) {
    const modal = new bootstrap.Modal(document.getElementById('apdModal'));
    if(editData) {
        document.getElementById('modalTitle').innerText = 'Edit APD';
        document.getElementById('formAction').value = 'update';
        document.getElementById('apd_id').value = editData.id;
        document.getElementById('apd_name').value = editData.name;
        document.getElementById('apd_size').value = editData.size;
        document.getElementById('apd_stock').value = editData.stock;
    } else {
        document.getElementById('apdForm').reset();
        document.getElementById('modalTitle').innerText = 'Tambah APD';
        document.getElementById('formAction').value = 'store';
    }
    modal.show();
}

// Event Listeners
document.addEventListener('click', function(e) {
    if(e.target.classList.contains('btn-edit')) {
        const btn = e.target.closest('.btn-edit');
        openModal({ id: btn.dataset.id, name: btn.dataset.name, size: btn.dataset.size, stock: btn.dataset.stock });
    }
    if(e.target.classList.contains('btn-delete')) {
        const id = e.target.closest('.btn-delete').dataset.id;
        Swal.fire({
            title: 'Yakin hapus data ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('id', id);
                fetch('api/apd.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    Swal.fire('Sukses!', data.message, 'success');
                    fetchAPD();
                });
            }
        });
    }
});

document.getElementById('apdForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const res = await fetch('api/apd.php', { method: 'POST', body: formData });
    const data = await res.json();
    if(data.status === 'success') {
        bootstrap.Modal.getInstance(document.getElementById('apdModal')).hide();
        Swal.fire('Sukses!', data.message, 'success');
        fetchAPD();
    }
});

// Load data saat halaman dibuka
fetchAPD();
</script>

<?php include 'templates/footer.php'; ?>