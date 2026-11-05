<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: ../login.php");
    exit;
}

require_once "../config/koneksi.php";

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: katalog.php");
    exit;
}

$id = (int)$_GET["id"];

$stmt = mysqli_prepare(
    $conn,
    "SELECT * FROM produk WHERE id_produk = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$produk = mysqli_fetch_assoc($result);

if (!$produk) {
    die("Produk tidak ditemukan.");
}

$halamanAktif = "katalog";
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Detail Produk | HermosaCare</title>

<style>
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    background: #fff7fa;
    font-family: Arial,sans-serif;
    color: #49383e;
}

.detail-container {
    max-width: 1000px;
    width: 90%;
    margin: 45px auto;
}

.detail-card {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    background: white;
    padding: 30px;
    border-radius: 22px;
    box-shadow: 0 4px 18px #eadce5;
}

.gambar-produk {
    width: 100%;
    height: 350px;
    object-fit: contain;
    background: #fff7fa;
    border-radius: 15px;
}

.detail-info h1 {
    color: #91466f;
    font-family: Georgia,serif;
}

.detail-info p {
    line-height: 1.8;
    margin-bottom: 15px;
}

.harga {
    color: #a64d83;
    font-size: 25px;
    font-weight: bold;
}

.btn-kembali {
    display: inline-block;
    margin-top: 15px;
    padding: 12px 20px;
    background: #a64d83;
    color: white;
    text-decoration: none;
    border-radius: 25px;
}

@media(max-width:650px) {
    .detail-card {
        grid-template-columns: 1fr;
        padding: 20px;
    }

    .gambar-produk {
        height: 250px;
    }
}
</style>
</head>

<body>

<?php include "../includes/navbar.php"; ?>

<main class="detail-container">

    <div class="detail-card">

        <div>

            <?php if (!empty($produk["gambar"])): ?>

                <img
                    class="gambar-produk"
                    src="../images/<?= htmlspecialchars(basename($produk["gambar"])) ?>"
                    alt="<?= htmlspecialchars($produk["nama_produk"]) ?>"
                >

            <?php else: ?>

                <div style="height:250px;display:grid;place-items:center;background:#fff0f6;">
                    Gambar belum tersedia.
                </div>

            <?php endif; ?>

        </div>

        <div class="detail-info">

            <h1>
                <?= htmlspecialchars($produk["nama_produk"]) ?>
            </h1>

            <p class="harga">
                Rp <?= number_format((float)$produk["harga"],0,',','.') ?>
            </p>

            <p>
                <b>Kategori:</b>
                <?= htmlspecialchars($produk["kategori"]) ?>
            </p>

            <p>
                <b>Manfaat:</b><br>
                <?= nl2br(htmlspecialchars($produk["manfaat"])) ?>
            </p>

            <p>
                <b>Cara Penggunaan:</b><br>
                <?= nl2br(htmlspecialchars($produk["cara_penggunaan"])) ?>
            </p>

            <a href="katalog.php" class="btn-kembali">
                ← Kembali ke Katalog
            </a>

        </div>

    </div>

</main>

</body>
</html>