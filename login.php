<?php
session_start();

$koneksi = mysqli_connect(
    "localhost",
    "root",
    "",
    "hermosacare"
);

// cek koneksi
if(!$koneksi){
    die("Koneksi gagal: " . mysqli_connect_error());
}

// cek tombol login di klik
if(isset($_POST["login"])){

    // tangkap data dari input
    $username = mysqli_real_escape_string(
        $koneksi, $_POST["username"]
    );

    $password = $_POST["password"];

    // cek apakah username sudah ada
    $cekUsername = mysqli_query(
        $koneksi,
        "SELECT * FROM user WHERE username = '$username'"
    );

    if(mysqli_num_rows($cekUsername) >= 1){

        // ambil data user
        $user = mysqli_fetch_assoc($cekUsername);

        // cek password
        if(password_verify($password, $user["password"])){

            $_SESSION["username"] = $user["username"];

            header("Location: index.php");
            exit;

        }else{

            echo "<script>
                alert('Password anda salah.');
            </script>";

        }

    }else{

        // enkripsi password
        $passwordHash = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        // simpan akun baru ke database
        $query = mysqli_query(
            $koneksi,
            "INSERT INTO user (username, password)
            VALUES ('$username', '$passwordHash')"
        );

        if($query){

            $_SESSION["username"] = $username;

            echo "<script>
                alert('Akun berhasil dibuat!');
                window.location = 'index.php';
            </script>";

        }else{

            echo "<script>
                alert('Akun gagal dibuat.');
            </script>";

        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - HermosaCare</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Georgia, serif;
            background: #f5eef1;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 25px;
        }

        .container {
            width: 100%;
            max-width: 1250px;
            min-height: 750px;
            display: flex;
            background: #fff9fb;
            border: 7px solid #b9b5bc;
            border-radius: 35px;
            overflow: hidden;
            box-shadow: 0 10px 30px #d8c5ce;
        }

        .kiri {
            width: 50%;
            padding: 55px;
            background: linear-gradient(
                140deg,
                #f8dce9,
                #fce9e8,
                #f8e5d9
            );
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .brand {
            color: #62424c;
            font-size: 27px;
            font-weight: bold;
        }

        .brand small {
            display: block;
            font-family: Arial;
            font-size: 10px;
            letter-spacing: 2px;
        }

        .gambar {
            width: 100%;
            height: 280px;
            border-radius: 20px;
            background:
                linear-gradient(#0002, #0005),
                url('gambar/login.jpg');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: end;
            padding: 25px;
            color: white;
        }

        .gambar h2 {
            font-size: 27px;
            font-style: italic;
            line-height: 1.4;
        }

        .deskripsi {
            color: #765b64;
            font-family: Arial;
            font-size: 14px;
            line-height: 1.7;
        }

        .info {
            display: flex;
            gap: 12px;
        }

        .info div {
            flex: 1;
            background: #fff9f8;
            padding: 15px;
            border-radius: 12px;
            color: #875c6c;
            font-family: Arial;
            font-size: 12px;
        }

        .kanan {
            width: 50%;
            padding: 70px 65px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #fff9fb;
        }

        .label {
            color: #a66c7c;
            font-family: Arial;
            font-size: 12px;
            letter-spacing: 1px;
            font-weight: bold;
        }

        h1 {
            font-size: 38px;
            color: #382b32;
            margin-bottom: 10px;
        }

        .subjudul {
            font-family: Arial;
            color: #907d84;
            font-size: 14px;
            line-height: 1.7;
            margin-bottom: 35px;
        }

        label {
            font-family: Arial;
            font-size: 13px;
            color: #57454d;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 16px;
            margin-top: 10px;
            margin-bottom: 25px;
            border: 1px solid #eadce2;
            border-radius: 10px;
            background: #fff;
            outline: none;
            font-size: 14px;
        }

        input:focus {
            border-color: #b8798e;
        }

        button {
            width: 100%;
            padding: 17px;
            border: none;
            border-radius: 30px;
            background: #a95f72;
            color: white;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
        }

        button:hover {
            background: #914d61;
        }

        .footer {
            text-align: center;
            color: #a18b94;
            font-family: Arial;
            font-size: 11px;
            margin-top: auto;
            padding-top: 60px;
            line-height: 1.7;
        }

        @media (max-width: 800px) {
            body {
                padding: 12px;
            }

            .container {
                flex-direction: column;
                border-radius: 20px;
            }

            .kiri, .kanan {
                width: 100%;
                padding: 30px;
            }

            .gambar {
                height: 200px;
                margin: 30px 0;
            }

            .gambar h2 {
                font-size: 21px;
            }

            h1 {
                font-size: 30px;
            }

            .kanan {
                padding-top: 45px;
                padding-bottom: 45px;
            }

            .footer {
                padding-top: 35px;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <!-- BAGIAN KIRI -->
    <div class="kiri">

        <div class="brand">
            HermosaCare
            <small>DERMATOLOGY & BEAUTY</small>
        </div>

        <div>
            <div class="gambar">
                <h2>
                    Perjalanan merawat kulit sehat
                    dan percaya diri dimulai dari
                    panduan yang tepat.
                </h2>
            </div>

            <p class="deskripsi">
                Selamat datang di HermosaCare!
                Temukan panduan skincare, makeup,
                dan informasi kecantikan dalam satu tempat.
            </p>
        </div>

        <div class="info">
            <div>
                <b>100%</b><br>
                Panduan Kecantikan
            </div>

            <div>
                <b>24/7</b><br>
                Akses Informasi
            </div>

            <div>
                <b>V1.0</b><br>
                Edisi Eksklusif
            </div>
        </div>

    </div>

    <!-- BAGIAN KANAN -->
    <div class="kanan">

        <div class="label">
            ✦ PORTAL AUTENTIKASI
        </div>

        <h1>Masuk ke HermosaCare</h1>

        <p class="subjudul">
            Kelola catatan skincare, makeup,
            dan informasi kebutuhan kulitmu
            dalam satu ruang terpadu.
        </p>

        <form method="POST">

            <label>Email atau Nama Pengguna</label>

            <input
                type="text"
                name="username"
                placeholder="Masukkan username"
                required
            >

            <label>Kata Sandi</label>

            <input
                type="password"
                name="password"
                placeholder="Masukkan kata sandi"
                required
            >

            <button type="submit" name="login">
                Masuk ke Dashboard →
            </button>

        </form>

        <div class="footer">
            HermosaCare v1.0 • Proyek Website PPLG<br>
            © 2026 HermosaCare. Hak cipta dilindungi.
        </div>

    </div>

</div>

</body>
</html>