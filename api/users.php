function loadUsersData() {
    fetch('api/users.php', {
        method: 'GET'
    })
    .then(response => response.json())
    .then(data => {
        let rows = '';
        let users = data.records || data;
        
        if(Array.isArray(users)) {
            users.forEach((user, index) => {
                rows += `<tr>
                    <td>${index + 1}</td>
                    <td>${user.nama_lengkap}</td>
                    <td>${user.username}</td>
                    <td><span class="badge bg-secondary">${user.role}</span></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="editUser(${user.id})"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteUser(${user.id})"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>`;
            });
            let tableEl = document.getElementById('usersTableBody');
            if(tableEl) tableEl.innerHTML = rows;
        }
    })
    .catch(err => console.error("Gagal memuat data users:", err));
}

function saveUser() {
    let userId = document.getElementById('userId').value;
    let userData = {
        id: userId ? userId : null,
        nama_lengkap: document.getElementById('userFullName').value,
        username: document.getElementById('userUsername').value,
        password: document.getElementById('userPassword').value,
        role: document.getElementById('userRoleSelect').value
    };

    let methodType = userId ? 'PUT' : 'POST';

    fetch('api/users.php', {
        method: methodType,
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(userData)
    })
    .then(response => response.json())
    .then(data => {
        if(data.status === 'success' || data.message) {
            alert('Sukses: ' + (data.message || 'Data user berhasil disimpan'));
            // Tutup modal menggunakan Bootstrap
            let modalEl = document.getElementById('userModal');
            let modalObj = bootstrap.Modal.getInstance(modalEl);
            if(modalObj) modalObj.hide();
            
            // Reload tabel user
            loadUsersData();
        } else {
            alert('Gagal: ' + (data.message || 'Terjadi kesalahan'));
        }
    })
    .catch(err => {
        console.error('Error saving user:', err);
        alert('Gagal terhubung ke server API users.');
    });
}