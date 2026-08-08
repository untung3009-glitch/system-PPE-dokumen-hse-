document.addEventListener("DOMContentLoaded", () => {
    checkSession();
});

// Check Sesi & Render UI Sesuai Role
async function checkSession() {
    try {
        const response = await fetch('api/auth.php?action=session');
        const res = await response.json();

        if (res.status === 'success') {
            document.getElementById('login-section').style.display = 'none';
            document.getElementById('app-section').style.display = 'block';
            document.getElementById('user-info').innerText = `${res.user.nama} (${res.user.role}) - ${res.user.area_kerja}`;
            
            // Load Module Data
            loadDashboard();
            loadPPEItems();
            loadRequests();
        } else {
            document.getElementById('login-section').style.display = 'block';
            document.getElementById('app-section').style.display = 'none';
        }
    } catch (e) {
        console.error("Session check error", e);
    }
}

// Handler Submit Login Form
document.getElementById('form-login')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);

    const res = await fetch('api/auth.php?action=login', {
        method: 'POST',
        body: formData
    });
    const data = await res.json();

    if (data.status === 'success') {
        checkSession();
    } else {
        alert(data.message);
    }
});

// Load Data Pengajuan PPE
async function loadRequests() {
    const res = await fetch('api/requests.php?action=list');
    const result = await res.json();
    
    if (result.status === 'success') {
        const tbody = document.getElementById('table-requests-body');
        if (!tbody) return;
        
        tbody.innerHTML = '';
        result.data.forEach(item => {
            let statusBadge = `<span class="badge badge-pending">${item.status}</span>`;
            if (item.status === 'Approved') statusBadge = `<span class="badge badge-approved">Approved</span>`;
            if (item.status === 'Rejected') statusBadge = `<span class="badge badge-rejected">Rejected</span>`;

            tbody.innerHTML += `
                <tr>
                    <td><b>${item.no_pengajuan}</b></td>
                    <td>${item.nama}<br><small>${item.nik} - ${item.departemen}</small></td>
                    <td>${item.nama_ppe}</td>
                    <td><b>${item.jumlah}</b> ${item.satuan}</td>
                    <td>${item.area_kerja}</td>
                    <td>${statusBadge}</td>
                    <td>
                        <button class="btn btn-primary" onclick="viewDetail(${item.id})">Detail</button>
                    </td>
                </tr>
            `;
        });
    }
}

// Submit Form Pengajuan PPE Baru
document.getElementById('form-request')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);

    const res = await fetch('api/requests.php?action=create', {
        method: 'POST',
        body: formData
    });
    const data = await res.json();

    alert(data.message);
    if (data.status === 'success') {
        e.target.reset();
        loadRequests();
        loadDashboard();
    }
});

// Load Combo Box PPE Items
async function loadPPEItems() {
    const res = await fetch('api/items.php');
    const result = await res.json();
    
    if (result.status === 'success') {
        const select = document.getElementById('select-ppe-item');
        if (!select) return;
        select.innerHTML = '<option value="">-- Pilih Jenis PPE --</option>';
        result.data.forEach(item => {
            select.innerHTML += `<option value="${item.id}">${item.nama_ppe} (Stok: ${item.stok} ${item.satuan})</option>`;
        });
    }
}

// Dashboard Realtime Analytics & Charts
async function loadDashboard() {
    const res = await fetch('api/dashboard.php');
    const data = await res.json();

    if (data.status === 'success') {
        document.getElementById('stat-total').innerText = data.summary.total;
        document.getElementById('stat-approved').innerText = data.summary.approved;
        document.getElementById('stat-pending').innerText = data.summary.pending;
        document.getElementById('stat-rejected').innerText = data.summary.rejected;
    }
}