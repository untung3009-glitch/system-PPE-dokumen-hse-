<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PPE Area Tambang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #003366 0%, #0055aa 100%); /* Warna biru khas Liugong */
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            overflow: hidden;
            width: 100%;
            max-width: 420px;
        }
        .login-header {
            background-color: #fff;
            padding: 25px 20px;
            text-align: center;
            border-bottom: 4px solid #e87722; /* Aksen oranye Liugong */
        }
        .login-header img {
            max-width: 180px;
            height: auto;
            margin-bottom: 10px;
        }
        .login-header h3 {
            color: #003366;
            font-weight: 700;
            margin: 0;
            font-size: 1.2rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .login-body {
            padding: 30px;
        }
        .form-control {
            border-radius: 8px;
            padding: 12px;
            border: 1px solid #ccc;
        }
        .form-control:focus {
            border-color: #0055aa;
            box-shadow: 0 0 0 0.2rem rgba(0, 85, 170, 0.25);
        }
        .btn-login {
            background-color: #e87722; /* Oranye Liugong */
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-weight: bold;
            color: #fff;
            width: 100%;
            transition: 0.3s;
        }
        .btn-login:hover {
            background-color: #d06615;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <!-- Logo Liugong. Pastikan file logo diletakkan di folder assets/ -->
            <img src="assets/logo-liugong.png" alt="Liugong Logo">
            <h3>PPE Safety Mine System</h3>
        </div>
        <div class="login-body">
            <div id="alertMsg"></div>
            <form id="loginForm">
                <div class="mb-3">
                    <label class="form-label text-muted fw-bold">NIK Karyawan</label>
                    <input type="text" class="form-control" id="nik" placeholder="Masukkan NIK" required>
                </div>
                <div class="mb-4">
                    <label class="form-label text-muted fw-bold">Password</label>
                    <input type="password" class="form-control" id="password" placeholder="Masukkan Password" required>
                </div>
                <button type="submit" class="btn btn-login">LOGIN</button>
            </form>
            <div class="mt-3 text-center text-muted small">
               
            </div>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const nik = document.getElementById('nik').value;
            const password = document.getElementById('password').value;
            const alertMsg = document.getElementById('alertMsg');

            try {
                const res = await fetch('api.php?action=login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ nik, password })
                });
                const data = await res.json();
                if(data.status === 'success') {
                    window.location.href = 'app.php';
                } else {
                    alertMsg.innerHTML = `<div class="alert alert-danger py-2">${data.message}</div>`;
                }
            } catch (err) {
                alertMsg.innerHTML = `<div class="alert alert-danger py-2">Terjadi kesalahan sistem.</div>`;
            }
        });
    </script>
</body>
</html>