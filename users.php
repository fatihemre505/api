<?php
session_start();

// Oturum kontrolü
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanıcı Yönetim Sistemi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    .error-message {
        color: red;
        margin-top: 5px;
    }

    .success-message {
        color: green;
        margin-top: 5px;
    }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Kullanıcı Yönetimi</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">
                    Hoş geldiniz, <?php echo htmlspecialchars($_SESSION['user_email']); ?>
                </span>
                <a href="logout.php" class="btn btn-outline-light">Çıkış Yap</a>
            </div>
        </div>
    </nav>
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <h1 class="text-center mb-4">Kullanıcı Yönetim Sistemi</h1>
            </div>
        </div>

        <!-- Yeni Kullanıcı Formu -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Yeni Kullanıcı Ekle</h5>
            </div>
            <div class="card-body">
                <form id="userForm">
                    <div class="mb-3">
                        <label for="name" class="form-label">Ad Soyad:</label>
                        <input type="text" class="form-control" id="name" name="name" required
                            placeholder="Örnek: Ahmet Yılmaz">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">E-posta Adresi:</label>
                        <input type="email" class="form-control" id="email" name="email" required
                            placeholder="ornek@email.com">
                    </div>
                    <div class="mb-3">
                        <label for="age" class="form-label">Yaş:</label>
                        <input type="number" class="form-control" id="age" name="age" required placeholder="Örnek: 25">
                    </div>
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                    <button type="reset" class="btn btn-secondary">Formu Temizle</button>
                </form>
            </div>
        </div>

        <!-- Kullanıcı Listesi -->
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Kayıtlı Kullanıcılar</h5>
            </div>
            <div class="card-body">
                <div id="userList">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Yükleniyor...</span>
                        </div>
                        <p>Kullanıcılar yükleniyor...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    const API_URL = 'http://127.0.0.1:5000';
    const messages = {
        loading: 'Yükleniyor...',
        noUsers: 'Henüz kayıtlı kullanıcı bulunmamaktadır.',
        addSuccess: 'Kullanıcı başarıyla eklendi!',
        addError: 'Kullanıcı eklenirken bir hata oluştu!',
        loadError: 'Kullanıcılar yüklenirken bir hata oluştu!',
        tableHeaders: {
            id: 'Kullanıcı No',
            name: 'Ad Soyad',
            email: 'E-posta',
            age: 'Yaş'
        }
    };

    function loadUsers() {
        const userList = document.getElementById('userList');
        userList.innerHTML = `
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">${messages.loading}</span>
                    </div>
                    <p>${messages.loading}</p>
                </div>`;

        fetch(`${API_URL}/users`)
            .then(response => {
                if (!response.ok) throw new Error(response.statusText);
                return response.json();
            })
            .then(data => {
                if (!data.users || data.users.length === 0) {
                    userList.innerHTML = `<div class="alert alert-info">${messages.noUsers}</div>`;
                    return;
                }

                let html = '<div class="table-responsive"><table class="table table-striped table-hover">';
                html += `<thead class="table-dark">
                        <tr>
                            <th>${messages.tableHeaders.id}</th>
                            <th>${messages.tableHeaders.name}</th>
                            <th>${messages.tableHeaders.email}</th>
                            <th>${messages.tableHeaders.age}</th>
                        </tr>
                    </thead><tbody>`;

                data.users.forEach(user => {
                    html += `<tr>
                            <td>${user.id}</td>
                            <td>${user.name}</td>
                            <td>${user.email}</td>
                            <td>${user.age}</td>
                        </tr>`;
                });

                html += '</tbody></table></div>';
                userList.innerHTML = html;
            })
            .catch(error => {
                console.error('Hata:', error);
                userList.innerHTML = `<div class="alert alert-danger">${messages.loadError}</div>`;
            });
    }

    document.addEventListener('DOMContentLoaded', loadUsers);

    document.getElementById('userForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = {
            name: document.getElementById('name').value,
            email: document.getElementById('email').value,
            age: parseInt(document.getElementById('age').value)
        };

        fetch(`${API_URL}/users`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            })
            .then(response => {
                if (!response.ok) throw new Error(response.statusText);
                return response.json();
            })
            .then(data => {
                alert(messages.addSuccess);
                document.getElementById('userForm').reset();
                loadUsers();
            })
            .catch(error => {
                console.error('Hata:', error);
                alert(messages.addError);
            });
    });
    </script>
</body>

</html>