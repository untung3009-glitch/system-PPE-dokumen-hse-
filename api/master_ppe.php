function loadMasterPPE() {
    fetch('api/master_ppe.php', {
        method: 'GET'
    })
    .then(response => response.json())
    .then(data => {
        let rows = '';
        let items = data.records || data; // Menyesuaikan format output PHP
        
        if(Array.isArray(items)) {
            items.forEach((item, index) => {
                rows += `<tr>
                    <td>${index + 1}</td>
                    <td><img src="uploads/${item.foto || 'default.jpg'}" width="40" height="40" class="rounded" style="object-fit: cover;"></td>
                    <td>${item.nama_alat}</td>
                    <td>${item.kategori}</td>
                    <td>${item.stok}</td>
                    <td>${item.satuan || 'Pcs'}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="editMasterPPE(${item.id})"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteMasterPPE(${item.id})"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>`;
            });
            let tableEl = document.getElementById('masterPPETableBody');
            if(tableEl) tableEl.innerHTML = rows;
        }
    })
    .catch(err => console.error("Gagal memuat Master PPE:", err));
}