// === FITUR NOTIFIKASI NAVBAR ===
async function loadNotifs() {
    try {
        const res = await fetch('api/notifikasi.php?action=fetch');
        const data = await res.json();
        
        const notifCount = document.getElementById('notifCount');
        const notifList = document.getElementById('notifList');
        
        if(data.unread > 0) {
            notifCount.innerText = data.unread;
            notifCount.style.display = 'inline';
        } else {
            notifCount.style.display = 'none';
        }

        if(data.notifs.length === 0) {
            notifList.innerHTML = '<li class="dropdown-item text-center text-muted py-3">Tidak ada notifikasi</li>';
        } else {
            let html = '';
            data.notifs.forEach(n => {
                let bg = n.is_read == 0 ? 'bg-light fw-bold' : '';
                html += `<li class="dropdown-item ${bg} py-2 border-bottom" style="white-space: normal;">${n.message}<br><small class="text-muted">${new Date(n.created_at).toLocaleString('id-ID')}</small></li>`;
            });
            notifList.innerHTML = html;
        }
    } catch (e) {
        console.error('Error loading notifs');
    }
}

// Tandai dibaca saat dropdown diklik
document.getElementById('notifDropdown')?.addEventListener('click', function() {
    fetch('api/notifikasi.php?action=read').then(() => {
        setTimeout(loadNotifs, 500); // Refresh icon setelah 0.5 detik
    });
});

// Load notifikasi setiap 10 detik
loadNotifs();
setInterval(loadNotifs, 10000);