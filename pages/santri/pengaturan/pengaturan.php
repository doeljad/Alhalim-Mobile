<head>
    <style>
        .settings-container {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 16px;
        }

        .settings-container h2 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        .list-group-item {
            border: none;
            padding: 16px;
            border-bottom: 1px solid #e0e0e0;
        }

        .list-group-item:last-child {
            border-bottom: none;
        }

        .list-group-item-action:hover {
            background-color: #f5f5f5;
        }

        .list-group-item-action.list-group-item-danger {
            color: #fff;
            background-color: #d9534f;
        }

        .list-group-item-action.list-group-item-danger:hover {
            background-color: #c9302c;
        }
    </style>
</head>

<body>
    <div class="container mt-3">
        <h3 class="text-center">Pengaturan</h3>
        <div class="list-group">
            <!-- User Account Section -->
            <a href="#" class="list-group-item list-group-item-action">
                <div class="d-flex w-100 justify-content-between">
                    <h5 class="mb-1">Akun</h5>
                    <small>Edit</small>
                </div>
                <p class="mb-1">Ganti nama pengguna, email, atau kata sandi.</p>
            </a>

            <!-- Notifications Section -->
            <a href="#" class="list-group-item list-group-item-action">
                <div class="d-flex w-100 justify-content-between">
                    <h5 class="mb-1">Notifikasi</h5>
                    <small>Edit</small>
                </div>
                <p class="mb-1">Atur preferensi notifikasi dan suara.</p>
            </a>

            <!-- Privacy Section -->
            <a href="#" class="list-group-item list-group-item-action">
                <div class="d-flex w-100 justify-content-between">
                    <h5 class="mb-1">Privasi</h5>
                    <small>Edit</small>
                </div>
                <p class="mb-1">Kelola pengaturan privasi dan keamanan akun.</p>
            </a>

            <!-- About Section -->
            <a href="#" class="list-group-item list-group-item-action">
                <div class="d-flex w-100 justify-content-between">
                    <h5 class="mb-1">Tentang Aplikasi</h5>
                    <small>Edit</small>
                </div>
                <p class="mb-1">Informasi tentang aplikasi dan versi.</p>
            </a>

            <!-- Login/Logout Button -->
            <?php if (isset($_SESSION['id']) && !empty($_SESSION['id'])): ?>
                <a href="logout.php" class="list-group-item list-group-item-action list-group-item-danger">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1">Keluar</h5>
                        <small>Logout</small>
                    </div>
                </a>
            <?php else: ?>
                <a href="login.php" class="list-group-item list-group-item-action list-group-item-primary">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1">Login</h5>
                        <small>Masuk</small>
                    </div>
                </a>
            <?php endif; ?>
        </div>
    </div>
</body>

<?php include('pages/inc/sntr-sidebar.php') ?>
<div class="phone">
    <input type="radio" name="s" id="s1" value="dashboard">
    <input type="radio" name="s" id="s2" value="berita">
    <input type="radio" name="s" id="s3" value="belanja">
    <input type="radio" name="s" id="s4" value="notifikasi">
    <input type="radio" name="s" id="s5" value="pengaturan" checked="checked">

    <label for="s1" data-page="dashboard">
        <i class="fas fa-home"></i>
        <span>Dashboard</span>
    </label>
    <label for="s2" data-page="berita">
        <i class="fas fa-newspaper"></i>
        <span>Berita</span>
    </label>
    <label for="s3" data-page="belanja">
        <i class="fas fa-shopping-cart"></i>
        <span>Belanja</span>
    </label>
    <label for="s4" data-page="notifikasi" class="position-relative">
        <i class="fas fa-bell"></i>
        <span>Notifikasi</span>
        <?php if ($unread_count > 0): ?>
            <span class="position-absolute top-20 start-90 badge rounded-pill bg-danger"
                style="margin-left: 25px; margin-top: -10px; font-size: 0.7rem; padding: 2px 6px;">
                <?= $unread_count ?>
                <span class="visually-hidden">unread messages</span>
            </span>
        <?php endif; ?>
    </label>
    <label for="s5" data-page="pengaturan">
        <i class="fas fa-gear"></i>
        <span>Pengaturan</span>
    </label>

    <div class="circle bg-gradient-primary"></div>
    <div class="phone_content">
        <div class="phone_bottom">
            <span class="indicator"></span>
        </div>
    </div>
</div>