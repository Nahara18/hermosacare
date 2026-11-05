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
$pesan = "";

/* AMBIL DATA PRODUK */
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

/* UPDATE DATA */
if (isset($_POST["ubah"])) {

    $nama = trim($_POST["nama_produk"]);
    $merk = trim($_POST["merk"]);
    $harga = $_POST["harga"];
    $kategori = $_POST["kategori"];
    $jenis = trim($_POST["jenis_produk"]);
    $manfaat = trim($_POST["manfaat"]);
    $cara = trim($_POST["cara_penggunaan"]);

    $gambarInput = trim($_POST["gambar"]);

    $gambar = $gambarInput != ""
        ? basename($gambarInput)
        : basename($produk["gambar"] ?? "");

    if ($nama == "" || $merk == "" || $harga == "" ||
        $kategori == "" || $jenis == "") {

        $pesan = "Nama, merk, harga, kategori, dan jenis produk wajib diisi.";

    } elseif (!is_numeric($harga) || $harga < 0) {

        $pesan = "Harga tidak valid.";

    } else {

        $update = mysqli_prepare(
            $conn,
            "UPDATE produk SET
            nama_produk = ?,
            merk = ?,
            harga = ?,
            kategori = ?,
            jenis_produk = ?,
            manfaat = ?,
            cara_penggunaan = ?,
            gambar = ?
            WHERE id_produk = ?"
        );

        mysqli_stmt_bind_param(
            $update,
            "ssdsssssi",
            $nama,
            $merk,
            $harga,
            $kategori,
            $jenis,
            $manfaat,
            $cara,
            $gambar,
            $id
        );

        if (mysqli_stmt_execute($update)) {
            header("Location: katalog.php?ubah=1");
            exit;
        } else {
            $pesan = "Data gagal diperbarui.";
        }
    }
}

$halamanAktif = "katalog";
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Edit Produk | HermosaCare</title>

<style>
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #fff7fa;
    color: #49383e;
}

.container {
    width: 90%;
    max-width: 700px;
    margin: 40px auto;
}

.section {
    background: white;
    padding: 30px;
    border-radius: 22px;
    box-shadow: 0 4px 18px #eadce5;
}

.section h2 {
    color: #91466f;
    font-family: Georgia, serif;
}

.form-group {
    margin-top: 16px;
}

label {
    display: block;
    margin-bottom: 7px;
    font-size: 13px;
    font-weight: bold;
}

input, select, textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid #dfcdd4;
    border-radius: 9px;
    font-family: Arial;
}

textarea {
    resize: vertical;
}

button, .kembali {
    display: inline-block;
    margin-top: 20px;
    padding: 12px 20px;
    border: none;
    border-radius: 22px;
    background: #934e60;
    color: white;
    text-decoration: none;
    cursor: pointer;
}

.pesan {
    padding: 12px;
    background: #f8e4eb;
    border-radius: 10px;
    color: #87495c;
}

small {
    color: #99858a;
}
</style>

</head>

<body>

<?php include "../includes/navbar.php"; ?>

<div class="container">

    <div class="section">

        <h2>Edit Produk</h2>

        <p>Perbarui informasi produk kecantikan HermosaCare.</p>

        <?php if ($pesan != ""): ?>
            <p class="pesan">
                <?= htmlspecialchars($pesan) ?>
            </p>
        <?php endif; ?>

        <form method="POST">

            <!-- MERK -->
            <div class="form-group">
                <label>Merk Produk *</label>

                <input type="text"
                    name="merk"
                    value="<?= htmlspecialchars($produk["merk"]) ?>"
                    placeholder="Contoh: Skintific, Wardah, Hanasui"
                    required>
            </div>

            <!-- NAMA PRODUK -->
            <div class="form-group">
                <label>Nama Produk *</label>

                <input type="text"
                    name="nama_produk"
                    value="<?= htmlspecialchars($produk["nama_produk"]) ?>"
                    required>
            </div>

            <!-- HARGA -->
            <div class="form-group">
                <label>Harga *</label>

                <input type="number"
                    name="harga"
                    min="0"
                    value="<?= htmlspecialchars($produk["harga"]) ?>"
                    required>
            </div>

            <!-- KATEGORI -->
            <div class="form-group">
                <label>Kategori *</label>

                <select name="kategori" required>

                    <?php
                    $kategoriList = [
                        "Skincare",
                        "Makeup",
                        "Bodycare",
                        "Haircare"
                    ];

                    foreach ($kategoriList as $k):
                    ?>

                    <option value="<?= $k ?>"
                        <?= $produk["kategori"] == $k ? "selected" : "" ?>>
                        <?= $k ?>
                    </option>

                    <?php endforeach; ?>

                </select>
            </div>

            <!-- JENIS PRODUK -->
            <div class="form-group">
                <label>Jenis Produk *</label>

                <input type="text"
                    name="jenis_produk"
                    value="<?= htmlspecialchars($produk["jenis_produk"]) ?>"
                    placeholder="Contoh: Facial Wash, Moisturizer, Lip Tint"
                    required>
            </div>

            <!-- MANFAAT -->
            <div class="form-group">
                <label>Manfaat</label>

                <textarea name="manfaat" rows="5"><?= htmlspecialchars($produk["manfaat"]) ?></textarea>
            </div>

            <!-- CARA PENGGUNAAN -->
            <div class="form-group">
                <label>Cara Penggunaan</label>

                <textarea name="cara_penggunaan" rows="5"><?= htmlspecialchars($produk["cara_penggunaan"]) ?></textarea>
            </div>

            <!-- GAMBAR -->
            <div class="form-group">
                <label>Nama File Gambar</label>

                <input type="text"
                    name="gambar"
                    value="<?= htmlspecialchars(basename($produk["gambar"] ?? "")) ?>"
                    placeholder="Contoh: moisturizer.jpg">

                <small>Gambar disimpan di folder images.</small>
            </div>

            <button type="submit" name="ubah">
                Simpan Perubahan
            </button>

            <a href="katalog.php" class="kembali">
                Kembali
            </a>

        </form>

    </div>

</div>

</body>
</html>