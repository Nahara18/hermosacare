<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}

require_once "config/koneksi.php";

$halamanAktif = "kelola";

$pesan = "";

/* TAMBAH KATEGORI */
if (isset($_POST["tambah"])) {

    $nama = mysqli_real_escape_string($conn, $_POST["nama_kategori"]);
    $deskripsi = mysqli_real_escape_string($conn, $_POST["deskripsi"]);
    $gambar = mysqli_real_escape_string($conn, $_POST["gambar"]);

    if ($nama != "") {

        $cek = mysqli_query(
            $conn,
            "SELECT * FROM kategori WHERE nama_kategori='$nama'"
        );

        if (mysqli_num_rows($cek) > 0) {
            $pesan = "Kategori sudah tersedia!";
        } else {

            mysqli_query(
                $conn,
                "INSERT INTO kategori (nama_kategori, deskripsi, gambar)
                 VALUES ('$nama', '$deskripsi', '$gambar')"
            );

            header("Location: kategori.php?status=tambah");
            exit;
        }

    } else {
        $pesan = "Nama kategori wajib diisi!";
    }
}

/* AMBIL DATA EDIT */
$edit = null;

if (isset($_GET["edit"])) {

    $id = (int) $_GET["edit"];

    $queryEdit = mysqli_query(
        $conn,
        "SELECT * FROM kategori WHERE id_kategori=$id"
    );

    $edit = mysqli_fetch_assoc($queryEdit);
}

/* UPDATE KATEGORI */
if (isset($_POST["update"])) {

    $id = (int) $_POST["id_kategori"];

    $nama = mysqli_real_escape_string($conn, $_POST["nama_kategori"]);
    $deskripsi = mysqli_real_escape_string($conn, $_POST["deskripsi"]);
    $gambar = mysqli_real_escape_string($conn, $_POST["gambar"]);

    if ($nama != "") {

        $cek = mysqli_query(
            $conn,
            "SELECT * FROM kategori
             WHERE nama_kategori='$nama'
             AND id_kategori!=$id"
        );

        if (mysqli_num_rows($cek) > 0) {
            $pesan = "Nama kategori sudah digunakan!";
        } else {

            mysqli_query(
                $conn,
                "UPDATE kategori SET
                nama_kategori='$nama',
                deskripsi='$deskripsi',
                gambar='$gambar'
                WHERE id_kategori=$id"
            );

            header("Location: kategori.php?status=update");
            exit;
        }

    } else {
        $pesan = "Nama kategori wajib diisi!";
    }
}

/* HAPUS KATEGORI */
if (isset($_POST["hapus"])) {

    $id = (int) $_POST["id_kategori"];

    mysqli_query(
        $conn,
        "DELETE FROM kategori WHERE id_kategori=$id"
    );

    header("Location: kategori.php?status=hapus");
    exit;
}

/* PESAN BERHASIL */
if (isset($_GET["status"])) {

    if ($_GET["status"] == "tambah") {
        $pesan = "Kategori berhasil ditambahkan!";
    } elseif ($_GET["status"] == "update") {
        $pesan = "Kategori berhasil diperbarui!";
    } elseif ($_GET["status"] == "hapus") {
        $pesan = "Kategori berhasil dihapus!";
    }
}

/* TAMPILKAN KATEGORI */
$result = mysqli_query(
    $conn,
    "SELECT * FROM kategori ORDER BY id_kategori DESC"
);

$total = mysqli_num_rows($result);
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Kelola Kategori | HermosaCare</title>

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
    max-width: 1150px;
    margin: 40px auto;
}

.header {
    padding: 35px;
    background: linear-gradient(120deg, #f7dce8, #fbe9e3);
    border-radius: 22px;
    margin-bottom: 25px;
}

.header span {
    color: #a45e73;
    font-size: 11px;
    font-weight: bold;
    letter-spacing: 2px;
}

.header h1 {
    font-family: Georgia, serif;
    font-size: 35px;
    margin: 15px 0;
}

.header p {
    color: #795e69;
    line-height: 1.7;
    font-size: 14px;
}

.section {
    background: white;
    padding: 28px;
    border-radius: 20px;
    box-shadow: 0 4px 15px #f0e3e8;
    margin-bottom: 25px;
}

.section h2 {
    font-family: Georgia, serif;
    color: #a45e73;
    margin-top: 0;
}

/* FORM */
.form-group {
    margin-bottom: 18px;
}

.form-group label {
    display: block;
    font-size: 13px;
    font-weight: bold;
    margin-bottom: 8px;
    color: #765461;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 13px;
    border: 1px solid #ead9e1;
    border-radius: 10px;
    font-family: Arial, sans-serif;
    outline: none;
}

.form-group input:focus,
.form-group textarea:focus {
    border-color: #a45e73;
}

.form-group textarea {
    min-height: 100px;
    resize: vertical;
}

.tombol {
    display: inline-block;
    padding: 12px 22px;
    background: #a45e73;
    color: white;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    text-decoration: none;
    font-size: 12px;
}

.tombol:hover {
    background: #85465d;
}

.batal {
    background: #eee4e9;
    color: #765461;
    margin-left: 7px;
}

/* PESAN */
.pesan {
    background: #f8eaf0;
    color: #96566e;
    padding: 14px;
    border-radius: 10px;
    font-size: 13px;
    margin-bottom: 20px;
}

/* TABEL */
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
    font-size: 13px;
}

td {
    padding: 15px;
    border-bottom: 1px solid #f3e8ed;
    font-size: 13px;
}

.btn-edit,
.btn-hapus {
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
    font-size: 12px;
}

@media(max-width: 600px) {

    .container {
        width: 94%;
    }

    .header {
        padding: 25px;
    }

    .header h1 {
        font-size: 28px;
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

        <span>✦ CATEGORY MANAGEMENT</span>

        <h1>Kelola Kategori</h1>

        <p>
            Tambahkan dan atur kategori produk kecantikan
            seperti skincare, makeup, bodycare, dan haircare.
        </p>

    </div>

    <!-- PESAN -->
    <?php if ($pesan != ""): ?>

        <div class="pesan">
            <?= htmlspecialchars($pesan) ?>
        </div>

    <?php endif; ?>

    <!-- FORM TAMBAH DAN EDIT -->
    <div class="section">

        <h2>
            <?= $edit ? "Edit Kategori" : "Tambah Kategori Baru" ?>
        </h2>

        <form method="POST" action="kategori.php">

            <?php if ($edit): ?>

                <input type="hidden"
                       name="id_kategori"
                       value="<?= (int)$edit["id_kategori"] ?>">

            <?php endif; ?>

            <div class="form-group">

                <label>Nama Kategori</label>

                <input type="text"
                       name="nama_kategori"
                       placeholder="Contoh: Skincare"
                       value="<?= htmlspecialchars($edit["nama_kategori"] ?? "") ?>"
                       required>

            </div>

            <div class="form-group">

                <label>Deskripsi Kategori</label>

                <textarea name="deskripsi"
                          placeholder="Masukkan deskripsi kategori..."
                          ><?= htmlspecialchars($edit["deskripsi"] ?? "") ?></textarea>

            </div>

            <div class="form-group">

                <label>Nama File Gambar</label>

                <input type="text"
                       name="gambar"
                       placeholder="Contoh: skincare.jpg"
                       value="<?= htmlspecialchars($edit["gambar"] ?? "") ?>">

            </div>

            <?php if ($edit): ?>

                <button type="submit" name="update" class="tombol">
                    Simpan Perubahan
                </button>

                <a href="kategori.php" class="tombol batal">
                    Batal
                </a>

            <?php else: ?>

                <button type="submit" name="tambah" class="tombol">
                    + Tambah Kategori
                </button>

            <?php endif; ?>

        </form>

    </div>

    <!-- DAFTAR KATEGORI -->
    <div class="section">

        <h2><?= $total ?> Kategori Tersimpan</h2>

        <p>Daftar kategori yang ada di database.</p>

        <div class="table-wrap">

            <table>

                <tr>
                    <th>ID</th>
                    <th>Nama Kategori</th>
                    <th>Deskripsi</th>
                    <th>Gambar</th>
                    <th>Aksi</th>
                </tr>

                <?php if ($total > 0): ?>

                    <?php while ($kategori = mysqli_fetch_assoc($result)): ?>

                    <tr>

                        <td>
                            <?= (int)$kategori["id_kategori"] ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($kategori["nama_kategori"]) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($kategori["deskripsi"] ?? "") ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($kategori["gambar"] ?? "-") ?>
                        </td>

                        <td>

                            <a class="btn-edit"
                               href="kategori.php?edit=<?= (int)$kategori["id_kategori"] ?>">
                                Edit
                            </a>

                            <form method="POST"
                                  action="kategori.php"
                                  style="display:inline;"
                                  onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">

                                <input type="hidden"
                                       name="id_kategori"
                                       value="<?= (int)$kategori["id_kategori"] ?>">

                                <button type="submit"
                                        name="hapus"
                                        class="btn-hapus">
                                    Hapus
                                </button>

                            </form>

                        </td>

                    </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="5" style="text-align:center;">
                            Belum ada kategori.
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