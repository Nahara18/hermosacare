<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}

require_once "config/koneksi.php";

/* TOTAL PRODUK */
$queryProduk = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM produk"
);
$dataProduk = mysqli_fetch_assoc($queryProduk);
$totalProduk = $dataProduk["total"];

/* TOTAL KATEGORI */
$queryKategori = mysqli_query(
    $conn,
    "SELECT COUNT(DISTINCT kategori) AS total FROM produk"
);
$dataKategori = mysqli_fetch_assoc($queryKategori);
$totalKategori = $dataKategori["total"];

/* NILAI PRODUK */
$queryNilai = mysqli_query(
    $conn,
    "SELECT SUM(harga) AS total FROM produk"
);
$dataNilai = mysqli_fetch_assoc($queryNilai);
$totalNilai = $dataNilai["total"] ?? 0;

/* PRODUK TERBARU */
$queryTerbaru = mysqli_query(
    $conn,
    "SELECT * FROM produk ORDER BY id_produk DESC LIMIT 5"
);

$halamanAktif = "beranda";
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Beranda | HermosaCare</title>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial,sans-serif;
    background: #fff7fa;
    color: #403039;
}

.container {
    max-width: 1400px;
    margin: auto;
    padding: 35px 5%;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    margin-bottom: 30px;
    flex-wrap: wrap;
}

.header span {
    display: inline-block;
    background: #f8e8d5;
    color: #a47751;
    font-size: 10px;
    padding: 9px 15px;
    border-radius: 20px;
    margin-bottom: 15px;
}

.header h1 {
    font-family: Georgia,serif;
    font-size: 38px;
    margin-bottom: 12px;
}

.header p {
    color: #927e87;
    font-size: 14px;
    line-height: 1.8;
}

.profil {
    background: white;
    padding: 20px;
    border-radius: 17px;
    color: #9b586d;
    box-shadow: 0 4px 15px #f0e3e8;
    font-size: 13px;
}

.banner {
    background: linear-gradient(110deg,#f7dce8,#fbe9e3);
    border-radius: 25px;
    padding: 40px;
    margin-bottom: 30px;
}

.banner h2 {
    font-family: Georgia,serif;
    font-size: 31px;
    margin-bottom: 13px;
}

.banner p {
    color: #795e69;
    font-size: 14px;
    line-height: 1.8;
    max-width: 650px;
}

.tombol {
    display: inline-block;
    margin-top: 20px;
    background: #a45e73;
    color: white;
    text-decoration: none;
    padding: 13px 23px;
    border-radius: 30px;
    font-size: 13px;
}

.tombol:hover {
    background: #85465c;
}

.statistik {
    display: grid;
    grid-template-columns: repeat(3,1fr);
    gap: 18px;
    margin-bottom: 35px;
}

.card-stat {
    background: white;
    border-radius: 18px;
    padding: 25px;
    box-shadow: 0 4px 15px rgba(120,70,90,.06);
}

.card-stat p {
    color: #927d87;
    font-size: 12px;
    margin-bottom: 15px;
}

.card-stat h2 {
    font-family: Georgia,serif;
    font-size: 27px;
    color: #a45e73;
}

.card-stat small {
    display: block;
    color: #a18c95;
    margin-top: 10px;
    font-size: 11px;
}

.section {
    background: white;
    padding: 30px;
    border-radius: 22px;
    margin-bottom: 30px;
    box-shadow: 0 4px 15px rgba(120,70,90,.05);
}

.label-section {
    color: #a45e73;
    font-size: 11px;
    font-weight: bold;
    letter-spacing: 1px;
    margin-bottom: 12px;
}

.section h2 {
    font-family: Georgia,serif;
    font-size: 27px;
    margin-bottom: 10px;
}

.subtitle {
    color: #9a8790;
    font-size: 13px;
    margin-bottom: 25px;
}

.table-wrap {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    min-width: 500px;
}

th {
    background: #fbeaf1;
    color: #89566b;
    padding: 15px;
    text-align: left;
    font-size: 12px;
}

td {
    padding: 15px;
    border-bottom: 1px solid #f3e8ed;
    font-size: 13px;
}

.badge-kategori {
    background: #f8e1eb;
    color: #a45e73;
    padding: 7px 12px;
    border-radius: 20px;
    font-size: 11px;
}

.kosong {
    text-align: center;
    color: #a18c95;
    padding: 25px;
}

.panduan {
    display: grid;
    grid-template-columns: repeat(3,1fr);
    gap: 18px;
}

.card-panduan {
    background: #fff9fb;
    border: 1px solid #f4e4eb;
    border-radius: 18px;
    padding: 25px;
}

.card-panduan .icon {
    background: #f8e3eb;
    width: 45px;
    height: 45px;
    border-radius: 14px;
    display: grid;
    place-items: center;
    font-size: 21px;
    margin-bottom: 18px;
}

.card-panduan h3 {
    font-family: Georgia,serif;
    color: #a45e73;
    margin-bottom: 12px;
}

.card-panduan p {
    color: #927e87;
    font-size: 13px;
    line-height: 1.8;
}

footer {
    background: white;
    padding: 28px 5%;
    display: flex;
    justify-content: space-between;
    gap: 15px;
    flex-wrap: wrap;
    color: #9a8790;
    font-size: 12px;
}

@media(max-width:850px) {
    .statistik,.panduan {
        grid-template-columns: 1fr;
    }

    .header h1 {
        font-size: 30px;
    }

    .container {
        padding: 25px 4%;
    }
}

@media(max-width:480px) {
    .banner {
        padding: 25px;
    }

    .banner h2 {
        font-size: 25px;
    }

    .section {
        padding: 20px;
    }
}
</style>
</head>

<body>

<?php include "includes/navbar.php"; ?>

<div class="container">

    <!-- HEADER -->
    <div class="header">

        <div>
            <span>✦ BEAUTY & SKINCARE GUIDE</span>

            <h1>Selamat Datang di HermosaCare</h1>

            <p>
                Kelola informasi produk kecantikan dan temukan
                panduan perawatan kulit dalam satu ruang.
            </p>
        </div>

        <div class="profil">
            ✿ KURATOR RESMI
            <br><br>

            <b><?= htmlspecialchars($_SESSION["username"]) ?></b>

            <br>
            <small>HermosaCare Member</small>
        </div>

    </div>

    <!-- BANNER -->
    <div class="banner">

        <h2>
            Halo, <?= htmlspecialchars($_SESSION["username"]) ?>!
        </h2>

        <p>
            Selamat datang kembali di HermosaCare.
            Rawat dan kenali kebutuhan kulitmu dengan informasi
            produk serta panduan kecantikan.
        </p>

        <a href="products/katalog.php" class="tombol">
            Jelajahi Produk →
        </a>

    </div>

    <!-- STATISTIK -->
    <div class="statistik">

        <div class="card-stat">
            <p>TOTAL PRODUK</p>
            <h2><?= $totalProduk ?></h2>
            <small>Produk tersimpan di database</small>
        </div>

        <div class="card-stat">
            <p>TOTAL KATEGORI</p>
            <h2><?= $totalKategori ?></h2>
            <small>Kategori produk kecantikan</small>
        </div>

        <div class="card-stat">
            <p>NILAI SELURUH PRODUK</p>
            <h2 style="font-size:23px;">
                Rp <?= number_format((float)$totalNilai,0,',','.') ?>
            </h2>
            <small>Total harga produk tercatat</small>
        </div>

    </div>

    <!-- PRODUK TERBARU -->
    <div class="section">

        <div class="label-section">✦ PUSTAKA PRODUK</div>

        <h2>Produk Terbaru</h2>

        <p class="subtitle">
            Daftar produk yang terakhir ditambahkan.
        </p>

        <div class="table-wrap">

            <table>

                <tr>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                </tr>

                <?php if (mysqli_num_rows($queryTerbaru) > 0): ?>

                    <?php while ($produk = mysqli_fetch_assoc($queryTerbaru)): ?>

                    <tr>

                        <td>
                            <b>
                                <?= htmlspecialchars($produk["nama_produk"]) ?>
                            </b>
                        </td>

                        <td>
                            <span class="badge-kategori">
                                <?= htmlspecialchars($produk["kategori"]) ?>
                            </span>
                        </td>

                        <td>
                            Rp <?= number_format((float)$produk["harga"],0,',','.') ?>
                        </td>

                    </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="3" class="kosong">
                            Belum ada produk yang ditambahkan.
                        </td>
                    </tr>

                <?php endif; ?>

            </table>

        </div>

        <a href="products/katalog.php" class="tombol">
            Lihat Semua Produk →
        </a>

    </div>

    <!-- PANDUAN -->
    <div class="section">

        <div class="label-section">✦ BEAUTY KNOWLEDGE</div>

        <h2>Jelajahi Panduan Kecantikan</h2>

        <p class="subtitle">
            Pelajari dasar perawatan kulit dan penggunaan makeup.
        </p>

        <div class="panduan">

            <div class="card-panduan">
                <div class="icon">♡</div>
                <h3>Skincare</h3>
                <p>
                    Kenali tahapan perawatan kulit mulai dari
                    membersihkan wajah hingga sunscreen.
                </p>
            </div>

            <div class="card-panduan">
                <div class="icon">✿</div>
                <h3>Makeup</h3>
                <p>
                    Temukan informasi dasar tentang produk makeup
                    dan cara penggunaannya.
                </p>
            </div>

            <div class="card-panduan">
                <div class="icon">❀</div>
                <h3>Perawatan Kulit</h3>
                <p>
                    Pelajari manfaat produk dan cara memilih
                    perawatan sesuai kebutuhan kulit.
                </p>
            </div>

        </div>

        <a href="panduan.php" class="tombol">
            Lihat Panduan →
        </a>

    </div>

</div>

<footer>
    <div>
        <b>HermosaCare</b>
        <br>
        Panduan & Pencatatan Produk Kecantikan
    </div>

    <div>
        © 2026 HermosaCare. Hak cipta dilindungi.
    </div>
</footer>

</body>
</html>