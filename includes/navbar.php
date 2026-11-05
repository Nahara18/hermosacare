<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$halamanAktif = $halamanAktif ?? "";
?>

<style>
.navbar {
    background: white;
    padding: 15px 5%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    flex-wrap: wrap;
    box-shadow: 0 3px 15px rgba(120,70,90,.08);
    border-top: 4px solid #d998a9;
    position: sticky;
    top: 0;
    z-index: 100;
}

.logo {
    font-family: Georgia, serif;
    font-size: 24px;
    color: #9b586d;
    font-weight: bold;
    text-decoration: none;
}

.logo small {
    display: block;
    font-family: Arial;
    font-size: 9px;
    letter-spacing: 2px;
    color: #a58b95;
}

.menu {
    display: flex;
    align-items: center;
    gap: 9px;
    flex-wrap: wrap;
}

.menu a {
    text-decoration: none;
    color: #66505a;
    font-size: 12px;
    padding: 11px 15px;
    border: 1px dashed #d8b4c2;
    border-radius: 25px;
    transition: .3s;
}

.menu a:hover,
.menu a.aktif {
    background: #c77d95;
    color: white;
    border-color: #c77d95;
}

.menu a.logout {
    background: #f8e8ee;
    border: none;
    color: #a45d74;
}

@media(max-width:700px) {
    .navbar {
        flex-direction: column;
        align-items: flex-start;
    }

    .menu {
        width: 100%;
        display: grid;
        grid-template-columns: repeat(2, 1fr);
    }

    .menu a {
        text-align: center;
    }
}
</style>

<nav class="navbar">

    <a href="/hermosacare/index.php" class="logo">
        HermosaCare
        <small>BEAUTY RECORDS & GUIDE</small>
    </a>

    <div class="menu">

        <a href="/hermosacare/index.php"
           class="<?= $halamanAktif == 'beranda' ? 'aktif' : '' ?>">
            Beranda
        </a>

        <a href="/hermosacare/products/katalog.php"
           class="<?= $halamanAktif == 'katalog' ? 'aktif' : '' ?>">
            Katalog Produk
        </a>

        <a href="/hermosacare/kelola.php"
           class="<?= $halamanAktif == 'kelola' ? 'aktif' : '' ?>">
            Kelola Produk
        </a>

        <a href="/hermosacare/panduan.php"
           class="<?= $halamanAktif == 'panduan' ? 'aktif' : '' ?>">
            Kategori & Panduan
        </a>

        <a href="/hermosacare/logout.php"
           class="logout"
           onclick="return confirm('Apakah kamu yakin ingin keluar?')">
            ↪ Keluar
        </a>

    </div>
</nav>