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
    "DELETE FROM produk WHERE id_produk = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    header("Location: katalog.php?hapus=1");
    exit;
}

die("Produk gagal dihapus.");
?>