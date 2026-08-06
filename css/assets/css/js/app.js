const API_URL = 'api/';

// Helper Fetch with Spinner & SweetAlert
async function fetchAPI(endpoint, method = 'GET', formData = null) {
    document.getElementById('globalSpinner').classList.add('active');
    try {
        let options = { method, headers: {} };
        if (formData) {
            options.body = formData; // Untuk FormData, jangan set Content-Type manual
        }
        const res = await fetch(API_URL + endpoint, options);
        const data = await res.json();
        
        if (res.status === 401) {
            Swal.fire('Session Habis', 'Silakan login kembali', 'warning').then(() => location.reload());
            return null;
        }
        return data;
    } catch (err) {
        Swal.fire('Error', 'Terjadi kesalahan jaringan', 'error');
        return null;
    } finally {
        document.getElementById('globalSpinner').classList.remove('active');
    }
}

// LOGIN LOGIC
document.getElementById('loginForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    
    const res = await fetchAPI('login.php', 'POST', JSON.stringify({ username, password }));
    // Karena koneksi.php mengharapkan raw JSON untuk login, kita ubah sedikit:
    // (Catatan: Implementasi fetchAPI di atas memakai formData, untuk JSON sesuaikan dibawah)
});

// Khusus Login karena mengirim JSON
async function doLogin(username, password) {
    document.getElementById('globalSpinner').classList.add('active');
    const res = await fetch('api/login.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ username, password })
    });
    const data = await res.json();
    document.getElementById('globalSpinner').classList.remove('active');
    
    if (data.status === 'success') { location.reload(); } 
    else { Swal.fire('Gagal', data.message, 'error'); }
}

// Pindah Halaman
function showPage(pageId) {
    document.querySelectorAll('.page-content').forEach(p => p.style.display = 'none');
    document.getElementById('page-' + pageId).style.display = 'block';
    document.querySelectorAll('.sidebar-menu a').forEach(a => a.classList.remove('active'));
    event.target.closest('a').classList.add('active');
    document.getElementById('pageTitle').innerText = pageId.replace('_', ' ').toUpperCase();

    if (pageId === 'dashboard') loadDashboard();
    if (pageId === 'ppe_approval') loadApprovals();
}

// DASHBOARD AUTO REFRESH
async function loadDashboard() {
    const res = await fetchAPI('dashboard.php');
    if (res && res.status === 'success') {
        document.getElementById('dashTotal').innerText = res.total;
        document.getElementById('dashPending').innerText = res.pending;
        document.getElementById('dashStock').innerText = res.stock;
    }
}
// Refresh setiap 3 detik
setInterval(() => {
    if (document.getElementById('page-dashboard').style.display !== 'none') {
        loadDashboard();
    }
}, 3000);

// SUBMIT PENGAJUAN APD
document.getElementById('ppeForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData();
    formData.append('reqName', document.getElementById('reqName').value);
    formData.append('apdName', document.getElementById('apdName').value);
    formData.append('qty', document.getElementById('qty').value);
    formData.append('photoApd', document.getElementById('photoApd').files[0]);
    formData.append('note', 'Pengajuan via web');

    const res = await fetchAPI('pengajuan.php', 'POST', formData);
    if (res && res.status === 'success') {
        Swal.fire('Berhasil!', res.message, 'success');
        e.target.reset();
    }
});

// LOAD APPROVALS
async function loadApprovals() {
    const res = await fetchAPI('pengajuan.php');
    if (res && res.status === 'success') {
        const queue = document.getElementById('approvalQueue');
        queue.innerHTML = res.data.map(d => `
            <div class="card mb-2">
                <div class="card-body d-flex justify-content-between">
                    <div>
                        <h6>${d.doc_no} - ${d.apd_name} (${d.qty})</h6>
                        <span class="badge bg-warning">${d.status}</span>
                    </div>
                    <div>
                        <button class="btn btn-success btn-sm" onclick="approve(${d.id}, 'pm', true)">Approve</button>
                        <button class="btn btn-danger btn-sm" onclick="approve(${d.id}, 'pm', false)">Reject</button>
                    </div>
                </div>
            </div>
        `).join('');
    }
}

async function approve(id, role, isApproved) {
    const formData = new FormData();
    formData.append('id', id);
    formData.append('role', role);
    formData.append('isApproved', isApproved);
    
    const res = await fetchAPI('approval.php', 'POST', formData);
    if (res && res.status === 'success') {
        Swal.fire('Done!', res.message, 'success');
        loadApprovals();
    }
}

// Init Dashboard if logged in
if (document.getElementById('page-dashboard')) {
    loadDashboard();
}