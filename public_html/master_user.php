<?php 
include 'templates/header.php'; 
include 'templates/sidebar.php'; 

if($_SESSION['role_name'] !== 'Admin') {
    echo "<div class='alert alert-danger'>Akses Ditolak</div>"; 
    include 'templates/footer.php'; 
    exit;
}

// Ambil data roles untuk dropdown
 $roles = $pdo->query("SELECT * FROM roles")->fetchAll();
?>

<div class="card shadow border-0">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 text-primary fw-bold"><i class="bi bi-people me-2"></i>Manajemen User</h5>
        <button class="btn btn-primary btn-sm" onclick="openUserModal()"><i class="bi bi-plus-lg"></i> Tambah User</button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="userTableBody"></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal User -->
<div class="modal fade" id="userModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="userModalTitle">Tambah User</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="userForm">
        <div class="modal-body">
          <input type="hidden" name="id" id="user_id">
          <input type="hidden" name="action" id="user_action" value="store">
          
          <div class="mb-3">
            <label class="form-label">Nama Lengkap</label>
            <input type="text" name="name" id="user_name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" id="user_email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Password <small class="text-muted">(Kosongkan jika tidak diubah)</small></label>
            <input type="password" name="password" id="user_password" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Role</label>
            <select name="role_id" id="user_role" class="form-select" required>
                <?php foreach($roles as $r): ?>
                <option value="<?= $r['id'] ?>"><?= $r['name'] ?></option>
                <?php endforeach; ?>
            </select>
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
async function fetchUsers() {
    const res = await fetch('api/user.php?action=fetch');
    const data = await res.json();
    let html = '';
    let no = 1;
    data.forEach(item => {
        let role_badge = item.role_name === 'Admin' ? 'bg-danger' : (item.role_name === 'Safety' ? 'bg-warning text-dark' : (item.role_name === 'Project Manager' ? 'bg-info' : 'bg-secondary'));
        html += `
            <tr>
                <td>${no++}</td>
                <td>${item.name}</td>
                <td>${item.email}</td>
                <td><span class="badge ${role_badge}">${item.role_name}</span></td>
                <td>
                    <button class="btn btn-sm btn-warning btn-edit-user" data-id='${JSON.stringify(item)}'><i class="bi bi-pencil"></i></button>
                    <button class="btn btn-sm btn-danger btn-delete-user" data-id="${item.id}" data-name="${item.name}"><i class="bi bi-trash"></i></button>
                </td>
            </tr>
        `;
    });
    document.getElementById('userTableBody').innerHTML = html;
}

function openUserModal(editData = null) {
    const modal = new bootstrap.Modal(document.getElementById('userModal'));
    document.getElementById('userForm').reset();
    
    if(editData) {
        document.getElementById('userModalTitle').innerText = 'Edit User';
        document.getElementById('user_action').value = 'update';
        document.getElementById('user_id').value = editData.id;
        document.getElementById('user_name').value = editData.name;
        document.getElementById('user_email').value = editData.email;
        document.getElementById('user_role').value = editData.role_id; // Asumsi role_id ikut terkirim, jika tidak perlu fetch ulang
        document.getElementById('user_password').required = false;
    } else {
        document.getElementById('userModalTitle').innerText = 'Tambah User';
        document.getElementById('user_action').value = 'store';
        document.getElementById('user_password').required = true;
    }
    modal.show();
}

document.addEventListener('click', function(e) {
    if(e.target.closest('.btn-edit-user')) {
        const data = JSON.parse(e.target.closest('.btn-edit-user').dataset.id);
        openUserModal(data);
    }
    if(e.target.closest('.btn-delete-user')) {
        const id = e.target.closest('.btn-delete-user').dataset.id;
        const name = e.target.closest('.btn-delete-user').dataset.name;
        Swal.fire({
            title: 'Hapus User?',
            text: `Anda yakin menghapus ${name}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('id', id);
                fetch('api/user.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    Swal.fire('Sukses!', data.message, 'success');
                    fetchUsers();
                });
            }
        });
    }
});

document.getElementById('userForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const res = await fetch('api/user.php', { method: 'POST', body: formData });
    const data = await res.json();
    if(data.status === 'success') {
        bootstrap.Modal.getInstance(document.getElementById('userModal')).hide();
        Swal.fire('Sukses!', data.message, 'success');
        fetchUsers();
    } else {
        Swal.fire('Error!', data.message, 'error');
    }
});

fetchUsers();
</script>

<?php include 'templates/footer.php'; ?>