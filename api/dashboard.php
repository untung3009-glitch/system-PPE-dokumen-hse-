function loadDashboardStats() {
    fetch('api/dashboard.php', {
        method: 'GET'
    })
    .then(response => response.json())
    .then(data => {
        // Asumsikan struktur data dari backend mengembalikan objek statistik
        if(data) {
            let elTotalReq = document.getElementById('statTotalPengajuan');
            let elApproved = document.getElementById('statApproved');
            let elPending = document.getElementById('statPending');
            let elStockAlert = document.getElementById('statStockAlert');

            if(elTotalReq) elTotalReq.innerText = data.total_pengajuan || 0;
            if(elApproved) elApproved.innerText = data.total_approved || 0;
            if(elPending) elPending.innerText = data.total_pending || 0;
            if(elStockAlert) elStockAlert.innerText = data.stock_alert || 0;
        }
    })
    .catch(err => console.error("Gagal memuat statistik dashboard:", err));
}