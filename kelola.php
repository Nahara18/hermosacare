<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}

require_once "config/koneksi.php";

$result = mysqli_query(
    $conn,
    "SELECT * FROM produk ORDER BY id_produk DESC"
);

$total = mysqli_num_rows($result);

$halamanAktif = "kelola";
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kelola Produk | HermosaCare</title>

<style>
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Arial,sans-serif;
    background: #fff7fa;
    color: #49383e;
}

.container {
    width: 90%;
    max-width: 1200px;
    margin: 40px auto;
}

.header {
    padding: 35px;
    background: linear-gradient(120deg,#f7dce8,#fbe9e3);
    border-radius: 22px;
    margin-bottom: 25px;
}

.header span {
    color: #a45e73;
    font-size: 11px;
    font-weight: bold;
}

.header h1 {
    font-family: Georgia,serif;
    font-size: 35px;
}

.header p {
    color: #795e69;
    line-height: 1.7;
}

.section {
    background: white;
    padding: 25px;
    border-radius: 20px;
    box-shadow: 0 4px 15px #f0e3e8;
}

.tombol {
    display: inline-block;
    margin: 15px 0;
    padding: 12px 20px;
    background: #a45e73;
    color: white;
    text-decoration: none;
    border-radius: 25px;
    font-size: 12px;
}

.table-wrap {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    min-width: 650px;
}

th {
    background: #fbeaf1;
    color: #89566b;
    padding: 15px;
    text-align: left;
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
}

.btn-edit,.btn-hapus {
    display: inline-block;
    padding: 8px 13px;
    border: none;
    border-radius: 18px;
    text-decoration: none;
    cursor: pointer;
    font-size: 11px;
}

.btn-edit {
    background: #f2e5ed;
    color: #765461;
}

.btn-hapus {
    background: #fff0f1;
    color: #d26d77;
}

footer {
    margin-top: 50px;
    padding: 25px;
    text-align: center;
    color: #927e87;
}

@media(max-width:600px) {
    .container {
        width: 94%;
    }

    .header {
        padding: 25px;
    }

    .header h1 {
        font-size: 28px;
    }
}
</style>
</head>

<body>

<?php include "includes/navbar.php"; ?>

<div class="container">

    <div class="header">
        <span>✦ PRODUCT MANAGEMENT</span>
        <h1>Kelola Produk</h1>
        <p>
            Atur dan perbarui data produk kecantikan HermosaCare.
        </p>
    </div>

    <div class="section">

        <h2><?= $total ?> Produk Tersimpan</h2>

        <p>Daftar produk yang ada di database.</p>

        <a href="products/katalog.php" class="tombol">
            + Tambah Produk
        </a>

        <div class="table-wrap">
            <table>

                <tr>
                    <th>ID</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Aksi</th>
                </tr>

                <?php if ($total > 0): ?>

                    <?php while ($produk = mysqli_fetch_assoc($result)): ?>

                    <tr>

                        <td><?= (int)$produk["id_produk"] ?></td>

                        <td>
                            <?= htmlspecialchars($produk["nama_produk"]) ?>
                        </td>

                        <td>
                            <span class="badge-kategori">
                                <?= htmlspecialchars($produk["kategori"]) ?>
                            </span>
                        </td>

                        <td>
                            Rp <?= number_format((float)$produk["harga"],0,',','.') ?>
                        </td>

                        <td>
                            <a class="btn-edit"
                               href="products/edit.php?id=<?= (int)$produk["id_produk"] ?>">
                                Edit
                            </a>

                            <a class="btn-hapus"
                               href="products/hapus.php?id=<?= (int)$produk["id_produk"] ?>"
                               onclick="return confirm('Yakin ingin menghapus produk ini?')">
                                Hapus
                            </a>
                        </td>

                    </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="5" style="text-align:center;">
                            Belum ada produk.
                        </td>
                    </tr>

                <?php endif; ?>

            </table>
        </div>

    </div>
</div>

<footer>
    © 2026 HermosaCare. Hak cipta dilindungi.
</footer>

</body>
</html>