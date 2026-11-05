<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}

require_once "config/koneksi.php";

$halamanAktif = "panduan";

/* AMBIL DATA PANDUAN DARI DATABASE */
$queryPanduan = mysqli_query($conn, "SELECT * FROM panduan ORDER BY id_panduan DESC");

?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Kategori & Panduan | HermosaCare</title>

<style>
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: Arial, sans-serif;
    background: #fff8fb;
    color: #49383e;
}

.container {
    width: 90%;
    max-width: 1250px;
    margin: 35px auto 70px;
}

/* HEADER */
.header {
    position: relative;
    overflow: hidden;
    padding: 55px 45px;
    background: linear-gradient(120deg, #f4d6e5, #fbe9e3, #eee1f7);
    border-radius: 25px;
    margin-bottom: 45px;
}

.header span {
    color: #a45e73;
    font-size: 12px;
    font-weight: bold;
    letter-spacing: 3px;
}

.header h1 {
    font-family: Georgia, serif;
    font-size: 38px;
    margin: 18px 0;
    color: #573c49;
}

.header p {
    max-width: 650px;
    color: #795e69;
    font-size: 14px;
    line-height: 1.9;
}

/* JUDUL SECTION */
.section-title {
    margin-bottom: 22px;
}

.section-title span {
    color: #b47791;
    font-size: 11px;
    font-weight: bold;
    letter-spacing: 2px;
}

.section-title h2 {
    font-family: Georgia, serif;
    font-size: 27px;
    color: #684858;
    margin-top: 8px;
}

.section-title p {
    color: #927d86;
    font-size: 13px;
    margin-top: 8px;
    line-height: 1.7;
}

/* GRID KATEGORI */
.grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 50px;
}

/* CARD KATEGORI */
.card {
    background: white;
    border-radius: 20px;
    padding: 27px 23px;
    box-shadow: 0 5px 20px rgba(120,70,90,.07);
    border: 1px solid #f6e8ef;
    transition: .3s;
}

.card:hover {
    transform: translateY(-6px);
    box-shadow: 0 10px 25px rgba(120,70,90,.12);
}

.icon {
    width: 55px;
    height: 55px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 16px;
    background: #faeaf1;
    color: #a45e73;
    font-size: 27px;
    margin-bottom: 20px;
}

.card h3 {
    color: #a45e73;
    font-family: Georgia, serif;
    font-size: 21px;
    margin-bottom: 12px;
}

.card p {
    color: #806e73;
    font-size: 13px;
    line-height: 1.8;
    margin-bottom: 15px;
}

.card ul {
    padding-left: 18px;
}

.card li {
    color: #806e73;
    font-size: 12px;
    line-height: 1.9;
    margin-bottom: 5px;
}

/* TOMBOL PRODUK */
.btn-produk {
    display: block;
    margin-top: 22px;
    padding: 12px 15px;
    text-align: center;
    text-decoration: none;
    background: #a45e73;
    color: white;
    border-radius: 25px;
    font-size: 12px;
    font-weight: bold;
    transition: .3s;
}

.btn-produk:hover {
    background: #85465d;
}

/* PANDUAN */
.panduan-section {
    margin-top: 20px;
}

.panduan-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}

.panduan-card {
    background: white;
    border-radius: 18px;
    padding: 25px;
    border: 1px solid #f4e5ec;
    box-shadow: 0 4px 15px rgba(120,70,90,.05);
    transition: .3s;
}

.panduan-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(120,70,90,.10);
}

.panduan-card span {
    color: #b47791;
    font-size: 12px;
    font-weight: bold;
}

.panduan-card h3 {
    font-family: Georgia, serif;
    color: #684858;
    font-size: 19px;
    margin: 13px 0;
}

.panduan-card p {
    color: #806e73;
    font-size: 13px;
    line-height: 1.8;
}

/* ISI PANDUAN */
.isi-panduan {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #f4e5ec;
    color: #806e73;
    font-size: 13px;
    line-height: 1.9;
}

.isi-panduan summary {
    color: #a45e73;
    font-weight: bold;
    cursor: pointer;
    font-size: 12px;
    list-style: none;
}

.isi-panduan summary::-webkit-details-marker {
    display: none;
}

.isi-panduan summary::after {
    content: " +";
}

.isi-panduan[open] summary::after {
    content: " −";
}

.isi-panduan p {
    margin-top: 12px;
    white-space: pre-line;
}

/* KALAU DATA KOSONG */
.kosong {
    grid-column: 1 / -1;
    background: white;
    border: 1px solid #f4e5ec;
    border-radius: 18px;
    padding: 35px;
    text-align: center;
    color: #927d86;
    font-size: 14px;
}

/* TOMBOL KATALOG */
.tombol {
    display: inline-block;
    margin-top: 35px;
    padding: 14px 25px;
    background: #a45e73;
    color: white;
    border-radius: 30px;
    text-decoration: none;
    font-size: 13px;
    transition: .3s;
}

.tombol:hover {
    background: #85465d;
}

/* FOOTER */
footer {
    margin-top: 60px;
    padding: 25px;
    text-align: center;
    background: white;
    color: #927e87;
    font-size: 12px;
    border-top: 1px solid #f3e6ec;
}

/* TABLET */
@media (max-width: 1000px) {
    .grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .panduan-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

/* HP */
@media (max-width: 600px) {
    .container {
        width: 92%;
        margin-top: 25px;
    }

    .header {
        padding: 30px 24px;
        border-radius: 20px;
    }

    .header h1 {
        font-size: 28px;
    }

    .header p {
        font-size: 13px;
    }

    .grid,
    .panduan-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }

    .section-title h2 {
        font-size: 23px;
    }

    .card {
        padding: 23px;
    }
}
</style>
</head>

<body>

<?php include "includes/navbar.php"; ?>

<div class="container">

    <!-- HEADER -->
    <div class="header">

        <span>✦ BEAUTY KNOWLEDGE</span>

        <h1>Kategori & Panduan Kecantikan</h1>

        <p>
            Temukan berbagai informasi dan panduan perawatan
            kecantikan mulai dari skincare, makeup, bodycare,
            hingga haircare bersama HermosaCare.
        </p>

    </div>

    <!-- KATEGORI -->
    <div class="section-title">
        <span>EXPLORE CATEGORY</span>
        <h2>Jelajahi Kategori</h2>
        <p>Pilih kategori perawatan yang ingin kamu pelajari.</p>
    </div>

    <div class="grid">

        <!-- SKINCARE -->
        <div class="card">
            <div class="icon">♡</div>
            <h3>Skincare</h3>

            <p>
                Perawatan kulit wajah untuk membantu menjaga
                kebersihan, kelembapan, dan kesehatan kulit.
            </p>

            <ul>
                <li>Facial wash</li>
                <li>Toner</li>
                <li>Serum</li>
                <li>Moisturizer</li>
                <li>Sunscreen</li>
            </ul>

            <a href="products/katalog.php?kategori=Skincare" class="btn-produk">
                Lihat Produk Skincare →
            </a>
        </div>

        <!-- MAKEUP -->
        <div class="card">
            <div class="icon">✿</div>
            <h3>Makeup</h3>

            <p>
                Berbagai produk makeup untuk mempercantik
                penampilan dan mengekspresikan kreativitas.
            </p>

            <ul>
                <li>Foundation dan cushion</li>
                <li>Concealer</li>
                <li>Bedak</li>
                <li>Blush on</li>
                <li>Lipstick dan lip tint</li>
            </ul>

            <a href="products/katalog.php?kategori=Makeup" class="btn-produk">
                Lihat Produk Makeup →
            </a>
        </div>

        <!-- BODYCARE -->
        <div class="card">
            <div class="icon">❀</div>
            <h3>Bodycare</h3>

            <p>
                Perawatan tubuh untuk membantu menjaga
                kebersihan dan kelembapan kulit.
            </p>

            <ul>
                <li>Body wash</li>
                <li>Body lotion</li>
                <li>Body scrub</li>
                <li>Body serum</li>
                <li>Body sunscreen</li>
            </ul>

            <a href="products/katalog.php?kategori=Bodycare" class="btn-produk">
                Lihat Produk Bodycare →
            </a>
        </div>

        <!-- HAIRCARE -->
        <div class="card">
            <div class="icon">♧</div>
            <h3>Haircare</h3>

            <p>
                Perawatan rambut dan kulit kepala agar tetap
                bersih dan terawat.
            </p>

            <ul>
                <li>Shampoo</li>
                <li>Conditioner</li>
                <li>Hair mask</li>
                <li>Hair serum</li>
                <li>Hair vitamin</li>
            </ul>

            <a href="products/katalog.php?kategori=Haircare" class="btn-produk">
                Lihat Produk Haircare →
            </a>
        </div>

    </div>

    <!-- PANDUAN DARI DATABASE -->
    <div class="panduan-section">

        <div class="section-title">
            <span>BEAUTY TIPS</span>
            <h2>Panduan Kecantikan</h2>
            <p>
                Informasi sederhana untuk membantu memahami
                perawatan kecantikan.
            </p>
        </div>

        <div class="panduan-grid">

            <?php if ($queryPanduan && mysqli_num_rows($queryPanduan) > 0) { ?>

                <?php while ($data = mysqli_fetch_assoc($queryPanduan)) { ?>

                    <div class="panduan-card">

                        <span>
                            <?php echo htmlspecialchars(strtoupper($data["kategori"])); ?>
                        </span>

                        <h3>
                            <?php echo htmlspecialchars($data["judul"]); ?>
                        </h3>

                        <p>
                            <?php echo htmlspecialchars($data["deskripsi"]); ?>
                        </p>

                        <details class="isi-panduan">
                            <summary>Baca Selengkapnya</summary>

                            <p>
                                <?php echo htmlspecialchars($data["isi"]); ?>
                            </p>
                        </details>

                    </div>

                <?php } ?>

            <?php } else { ?>

                <div class="kosong">
                    Belum ada panduan kecantikan yang tersedia.
                </div>

            <?php } ?>

        </div>

    </div>

    <a href="products/katalog.php" class="tombol">
        Lihat Semua Katalog Produk →
    </a>

</div>

<footer>
    © 2026 HermosaCare. Hak cipta dilindungi.
</footer>

</body>
</html>