<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: ../login.php");
    exit;
}

require_once "../config/koneksi.php";

$pesan = "";

/* DAFTAR KATEGORI */
$daftarKategori = ["Skincare", "Makeup", "Bodycare", "Haircare"];

/* AMBIL KATEGORI DARI URL */
$kategoriFilter = $_GET["kategori"] ?? "";

if (!in_array($kategoriFilter, $daftarKategori)) {
    $kategoriFilter = "";
}

/* TAMBAH PRODUK */
if (isset($_POST["simpan"])) {

    $nama = trim($_POST["nama_produk"]);
    $merk = trim($_POST["merk"]);
    $harga = $_POST["harga"];
    $kategori = $_POST["kategori"];
    $jenis = trim($_POST["jenis_produk"]);
    $manfaat = trim($_POST["manfaat"]);
    $cara = trim($_POST["cara_penggunaan"]);
    $gambar = basename(trim($_POST["gambar"]));

    if ($nama == "" || $merk == "" || $harga == "" ||
        $kategori == "" || $jenis == "") {

        $pesan = "Nama, merk, harga, kategori, dan jenis produk wajib diisi.";

    } elseif (!is_numeric($harga) || $harga < 0) {

        $pesan = "Harga harus berupa angka yang valid.";

    } else {

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO produk
            (nama_produk, merk, harga, kategori, jenis_produk,
            manfaat, cara_penggunaan, gambar)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "ssdsssss",
            $nama,
            $merk,
            $harga,
            $kategori,
            $jenis,
            $manfaat,
            $cara,
            $gambar
        );

        if (mysqli_stmt_execute($stmt)) {
            header("Location: katalog.php?berhasil=1");
            exit;
        } else {
            $pesan = "Produk gagal disimpan.";
        }
    }
}

/* PESAN NOTIFIKASI */
if (isset($_GET["berhasil"])) {
    $pesan = "Produk berhasil ditambahkan!";
}

if (isset($_GET["ubah"])) {
    $pesan = "Produk berhasil diperbarui!";
}

if (isset($_GET["hapus"])) {
    $pesan = "Produk berhasil dihapus!";
}

/* HITUNG TOTAL SELURUH PRODUK */
$queryTotal = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM produk"
);

$total_produk = mysqli_fetch_assoc($queryTotal)["total"];

/* HITUNG NILAI KOLEKSI */
$queryHarga = mysqli_query(
    $conn,
    "SELECT SUM(harga) AS total FROM produk"
);

$dataHarga = mysqli_fetch_assoc($queryHarga);
$total_harga = $dataHarga["total"] ?? 0;

/* HITUNG KATEGORI */
$queryKategori = mysqli_query(
    $conn,
    "SELECT COUNT(DISTINCT kategori) AS total FROM produk"
);

$dataKategori = mysqli_fetch_assoc($queryKategori);
$total_kategori = $dataKategori["total"] ?? 0;

/* AMBIL PRODUK SESUAI KATEGORI */
if ($kategoriFilter != "") {

    $stmtProduk = mysqli_prepare(
        $conn,
        "SELECT * FROM produk
         WHERE kategori = ?
         ORDER BY id_produk DESC"
    );

    mysqli_stmt_bind_param($stmtProduk, "s", $kategoriFilter);
    mysqli_stmt_execute($stmtProduk);

    $hasil = mysqli_stmt_get_result($stmtProduk);

} else {

    $hasil = mysqli_query(
        $conn,
        "SELECT * FROM produk ORDER BY id_produk DESC"
    );

}

$jumlahTampil = mysqli_num_rows($hasil);

/* FORMAT RUPIAH */
function rupiah($angka) {
    return "Rp " . number_format((float)$angka, 0, ",", ".");
}

$halamanAktif = "katalog";
?>

<!DOCTYPE html>
<html lang="id">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Katalog Produk | HermosaCare</title>

<link rel="stylesheet" href="/hermosacare/assets/style.css">

<style>
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #fff8fb;
    color: #49383e;
}

.container {
    width: 90%;
    max-width: 1300px;
    margin: 35px auto;
}

.welcome {
    padding: 40px;
    border-radius: 25px;
    background: linear-gradient(120deg,#fff0f6,#fde5ef);
}

.label {
    color: #9a6b78;
    font-size: 11px;
    font-weight: bold;
}

.welcome h1 {
    font-family: Georgia,serif;
    font-size: 34px;
}

.welcome h1 span {
    color: #a05c71;
}

.welcome p {
    color: #806e73;
    line-height: 1.8;
    font-size: 13px;
}

.add-product {
    padding: 13px 22px;
    border: none;
    border-radius: 25px;
    background: #914c5c;
    color: white;
    cursor: pointer;
}

.stats {
    display: grid;
    grid-template-columns: repeat(3,1fr);
    gap: 18px;
    margin-top: 25px;
}

.stat {
    background: white;
    padding: 20px;
    border-radius: 16px;
    border: 1px solid #f0dce2;
}

.stat-title {
    font-size: 11px;
    color: #99858a;
}

.stat-number {
    margin-top: 12px;
    font-size: 23px;
    color: #87495c;
    font-family: Georgia,serif;
}

.message {
    margin-top: 20px;
    padding: 14px;
    background: #f8e4eb;
    color: #87495c;
    border-radius: 12px;
}

.filter {
    margin-top: 30px;
    padding: 22px;
    background: #fff0f6;
    border-radius: 20px;
}

.search-product {
    width: 100%;
    padding: 13px;
    background: white;
    border-radius: 25px;
}

.search-product input {
    width: 95%;
    border: none;
    outline: none;
    padding: 4px;
}

.categories {
    display: flex;
    flex-wrap: wrap;
    gap: 9px;
    margin-top: 17px;
}

.category-btn {
    padding: 10px 17px;
    border: none;
    border-radius: 20px;
    background: white;
    cursor: pointer;
    color: #604d53;
}

.category-btn.active {
    background: #974e60;
    color: white;
}

.product-heading {
    margin: 35px 0 20px;
}

.product-heading h2 {
    font-family: Georgia,serif;
}

.product-grid {
    display: grid;
    grid-template-columns: repeat(3,1fr);
    gap: 22px;
}

.product-card {
    background: white;
    border: 1px solid #f0e0e5;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(90,40,60,.06);
}

.product-image {
    height: 220px;
    padding: 12px;
    position: relative;
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    background: #fff7fa;
    border-radius: 14px;
}

.category-badge {
    position: absolute;
    top: 20px;
    left: 20px;
    background: #fff0d8;
    padding: 7px 11px;
    border-radius: 20px;
    font-size: 10px;
}

.product-body {
    padding: 15px 18px 20px;
}

.product-brand {
    color: #a05c71;
    font-size: 12px;
    font-weight: bold;
    letter-spacing: 1.5px;
    margin-bottom: 8px;
}

.product-type {
    display: inline-block;
    padding: 6px 10px;
    background: #fcecf2;
    color: #96556b;
    border-radius: 15px;
    font-size: 10px;
    margin-bottom: 8px;
}

.product-body h3 {
    font-family: Georgia,serif;
    min-height: 40px;
    margin-top: 5px;
}

.price {
    font-size: 20px;
    color: #a04f62;
    margin: 10px 0;
}

.info {
    padding: 11px;
    margin-top: 9px;
    background: #fff4f8;
    border-radius: 10px;
    font-size: 11px;
    line-height: 1.6;
    overflow-wrap: anywhere;
}

.product-buttons {
    display: flex;
    gap: 8px;
    margin-top: 15px;
}

.detail-btn {
    flex: 1;
    padding: 12px;
    text-align: center;
    text-decoration: none;
    border-radius: 22px;
    background: #efd6e0;
    color: #634751;
    font-size: 12px;
}

.edit-btn,.delete-btn {
    width: 40px;
    display: flex;
    justify-content: center;
    align-items: center;
    text-decoration: none;
    border-radius: 50%;
}

.edit-btn {
    background: #f2e5ed;
    color: #765461;
}

.delete-btn {
    background: #fff0f1;
    color: #d26d77;
}

footer {
    margin-top: 60px;
    padding: 25px;
    text-align: center;
    color: #8c777d;
    border-top: 1px solid #eadde1;
}

.modal {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 999;
    background: rgba(50,25,35,.5);
    justify-content: center;
    align-items: center;
    padding: 15px;
}

.modal-box {
    width: 500px;
    max-width: 100%;
    max-height: 90vh;
    overflow-y: auto;
    padding: 25px;
    background: white;
    border-radius: 20px;
}

.modal-header {
    display: flex;
    justify-content: space-between;
}

.close {
    border: none;
    background: none;
    font-size: 25px;
    cursor: pointer;
}

.form-group {
    margin-top: 14px;
}

.form-group label {
    display: block;
    font-size: 12px;
    font-weight: bold;
    margin-bottom: 7px;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 11px;
    border: 1px solid #dfcdd4;
    border-radius: 9px;
}

.form-submit {
    width: 100%;
    margin-top: 18px;
    padding: 13px;
    border: none;
    background: #934e60;
    color: white;
    border-radius: 20px;
    cursor: pointer;
}

@media(max-width:900px) {
    .product-grid {
        grid-template-columns: repeat(2,1fr);
    }
}

@media(max-width:600px) {
    .stats,.product-grid {
        grid-template-columns: 1fr;
    }

    .welcome {
        padding: 25px;
    }

    .welcome h1 {
        font-size: 27px;
    }
}
</style>
</head>

<body>

<?php include "../includes/navbar.php"; ?>

<div class="container">

<section class="welcome">

    <div class="label">
        ✦ KOLEKSI PRODUK KECANTIKAN
    </div>

    <h1>
        Katalog <span>Produk Kecantikan</span>
    </h1>

    <p>
        Temukan dan kelola berbagai produk skincare, makeup,
        bodycare, dan haircare dalam satu tempat.
    </p>

    <button class="add-product" onclick="bukaModal()">
        + Tambah Produk Baru
    </button>

    <div class="stats">

        <div class="stat">
            <div class="stat-title">TOTAL PRODUK</div>
            <div class="stat-number">
                <?= $total_produk ?> produk
            </div>
        </div>

        <div class="stat">
            <div class="stat-title">TOTAL KATEGORI</div>
            <div class="stat-number">
                <?= $total_kategori ?> kategori
            </div>
        </div>

        <div class="stat">
            <div class="stat-title">NILAI KOLEKSI</div>
            <div class="stat-number">
                <?= rupiah($total_harga) ?>
            </div>
        </div>

    </div>

</section>

<?php if ($pesan != ""): ?>
    <div class="message">
        <?= htmlspecialchars($pesan) ?>
    </div>
<?php endif; ?>

<!-- FILTER -->

<section class="filter">

    <div class="search-product">
        🔍
        <input
            type="text"
            id="searchProduk"
            placeholder="Cari merk, nama, atau jenis produk..."
            onkeyup="filterProduk()"
        >
    </div>

    <div class="categories">

        <button
            class="category-btn <?= $kategoriFilter == "" ? "active" : "" ?>"
            onclick="filterKategori('')">
            Semua
        </button>

        <?php foreach ($daftarKategori as $kategori): ?>

            <button
                class="category-btn <?= $kategoriFilter == $kategori ? "active" : "" ?>"
                onclick="filterKategori('<?= htmlspecialchars($kategori) ?>')">

                <?= htmlspecialchars($kategori) ?>

            </button>

        <?php endforeach; ?>

    </div>

</section>

<!-- DAFTAR PRODUK -->

<div class="product-heading">

    <h2>
        <?= $kategoriFilter != "" ? "Produk " . htmlspecialchars($kategoriFilter) : "Daftar Produk" ?>
    </h2>

    <small>
        <?= $jumlahTampil ?> produk ditampilkan
    </small>

</div>

<section class="product-grid" id="productGrid">

<?php if ($jumlahTampil > 0): ?>

    <?php while ($p = mysqli_fetch_assoc($hasil)): ?>

        <article class="product-card"

            data-search="<?= htmlspecialchars(strtolower(
                $p["merk"] . " " .
                $p["nama_produk"] . " " .
                $p["jenis_produk"]
            )) ?>">

            <div class="product-image">

                <?php if (!empty($p["gambar"])): ?>

                    <img
                        src="../images/<?= htmlspecialchars(basename($p["gambar"])) ?>"
                        alt="<?= htmlspecialchars($p["nama_produk"]) ?>"
                        onerror="this.style.display='none'"
                    >

                <?php else: ?>

                    <div style="
                        height:100%;
                        display:grid;
                        place-items:center;
                        background:#fcebf2;
                        border-radius:14px;
                    ">
                        ♡
                    </div>

                <?php endif; ?>

                <span class="category-badge">
                    <?= htmlspecialchars($p["kategori"]) ?>
                </span>

            </div>

            <div class="product-body">

                <div class="product-brand">
                    <?= htmlspecialchars($p["merk"] ?? "") ?>
                </div>

                <div class="product-type">
                    <?= htmlspecialchars($p["jenis_produk"] ?? "") ?>
                </div>

                <h3>
                    <?= htmlspecialchars($p["nama_produk"]) ?>
                </h3>

                <div class="price">
                    <?= rupiah($p["harga"]) ?>
                </div>

                <div class="info">
                    <b>Manfaat:</b><br>
                    <?= nl2br(htmlspecialchars($p["manfaat"])) ?>
                </div>

                <div class="info">
                    <b>Cara Penggunaan:</b><br>
                    <?= nl2br(htmlspecialchars($p["cara_penggunaan"])) ?>
                </div>

                <div class="product-buttons">

                    <a
                        href="detail.php?id=<?= (int)$p["id_produk"] ?>"
                        class="detail-btn">
                        Lihat Detail
                    </a>

                    <a
                        href="edit.php?id=<?= (int)$p["id_produk"] ?>"
                        class="edit-btn"
                        title="Edit">
                        ✎
                    </a>

                    <a
                        href="hapus.php?id=<?= (int)$p["id_produk"] ?>"
                        class="delete-btn"
                        onclick="return confirm('Yakin ingin menghapus produk ini?')"
                        title="Hapus">
                        ×
                    </a>

                </div>

            </div>

        </article>

    <?php endwhile; ?>

<?php else: ?>

    <div style="
        grid-column:1/-1;
        text-align:center;
        padding:45px;
        background:white;
        border-radius:20px;
    ">

        <h3>Belum ada produk di kategori ini</h3>

        <p>Silakan pilih kategori lain atau tambahkan produk.</p>

    </div>

<?php endif; ?>

</section>

</div>

<footer>
    HermosaCare • Panduan & Pencatatan Produk Kecantikan
    <br><br>
    © 2026 HermosaCare
</footer>

<!-- MODAL TAMBAH PRODUK -->

<div class="modal" id="modalTambah">

    <div class="modal-box">

        <div class="modal-header">
            <h2>Tambah Produk</h2>
            <button class="close" onclick="tutupModal()">×</button>
        </div>

        <form method="POST">

            <div class="form-group">
                <label>Merk Produk *</label>
                <input type="text" name="merk"
                    placeholder="Contoh: Skintific, Wardah, Hanasui"
                    required>
            </div>

            <div class="form-group">
                <label>Nama Produk *</label>
                <input type="text" name="nama_produk"
                    placeholder="Contoh: 5X Ceramide Moisture Gel"
                    required>
            </div>

            <div class="form-group">
                <label>Harga *</label>
                <input type="number" name="harga" min="0"
                    placeholder="Contoh: 125000" required>
            </div>

            <div class="form-group">
                <label>Kategori *</label>
                <select name="kategori" required>
                    <option value="">Pilih kategori</option>
                    <option value="Skincare">Skincare</option>
                    <option value="Makeup">Makeup</option>
                    <option value="Bodycare">Bodycare</option>
                    <option value="Haircare">Haircare</option>
                </select>
            </div>

            <div class="form-group">
                <label>Jenis Produk *</label>
                <input type="text" name="jenis_produk"
                    placeholder="Contoh: Facial Wash, Moisturizer, Lip Tint"
                    required>
            </div>

            <div class="form-group">
                <label>Manfaat</label>
                <textarea name="manfaat" rows="5"
                    placeholder="Tuliskan manfaat produk..."></textarea>
            </div>

            <div class="form-group">
                <label>Cara Penggunaan</label>
                <textarea name="cara_penggunaan" rows="5"
                    placeholder="Tuliskan cara penggunaan produk..."></textarea>
            </div>

            <div class="form-group">
                <label>Nama File Gambar</label>
                <input type="text" name="gambar"
                    placeholder="Contoh: skintific.jpg">
                <small>Letakkan gambar di folder images.</small>
            </div>

            <button type="submit" name="simpan" class="form-submit">
                Simpan Produk
            </button>

        </form>

    </div>

</div>

<script>

/* MODAL */

function bukaModal() {
    document.getElementById("modalTambah").style.display = "flex";
}

function tutupModal() {
    document.getElementById("modalTambah").style.display = "none";
}

window.onclick = function(event) {
    let modal = document.getElementById("modalTambah");

    if (event.target === modal) {
        tutupModal();
    }
};

/* FILTER KATEGORI MELALUI URL */

function filterKategori(kategori) {

    if (kategori === "") {
        window.location.href = "katalog.php";
    } else {
        window.location.href =
            "katalog.php?kategori=" + encodeURIComponent(kategori);
    }

}

/* PENCARIAN PRODUK */

function filterProduk() {

    let input = document
        .getElementById("searchProduk")
        .value.toLowerCase();

    document.querySelectorAll(".product-card").forEach(function(card) {

        let dataSearch = card.dataset.search;

        card.style.display =
            dataSearch.includes(input) ? "" : "none";

    });

}

</script>

</body>
</html>