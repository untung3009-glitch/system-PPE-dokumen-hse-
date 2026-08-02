function handleLogout() {
    fetch('api/logout.php', {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        localStorage.clear();
        window.location.reload();
    })
    .catch(err => {
        console.error('Logout error:', err);
        localStorage.clear();
        window.location.reload();
    });
}