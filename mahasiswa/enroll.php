<?php
session_start();
require_once '../config.php';

// 1. Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    // Jika belum login, tidak bisa mendaftar
    header("Location: ../login.php");
    exit();
}

// 2. Pastikan ada ID praktikum yang dikirim
if (!isset($_GET['course_id']) || empty($_GET['course_id'])) {
    // Jika tidak ada ID, kembalikan ke halaman katalog
    header("Location: courses.php");
    exit();
}

$id_mahasiswa = $_SESSION['user_id'];
$id_praktikum = $_GET['course_id'];

// 3. Cek apakah mahasiswa sudah terdaftar di praktikum ini sebelumnya
$sql_check = "SELECT id FROM pendaftaran_praktikum WHERE id_mahasiswa = ? AND id_praktikum = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("ii", $id_mahasiswa, $id_praktikum);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    // Jika sudah terdaftar, beri pesan dan arahkan
    $_SESSION['message'] = "Anda sudah terdaftar pada mata praktikum ini.";
    $_SESSION['message_type'] = 'error'; // Tipe pesan untuk notifikasi
} else {
    // 4. Jika belum terdaftar, masukkan data baru ke tabel pendaftaran
    $sql_insert = "INSERT INTO pendaftaran_praktikum (id_mahasiswa, id_praktikum) VALUES (?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("ii", $id_mahasiswa, $id_praktikum);

    if ($stmt_insert->execute()) {
        // Jika berhasil, beri pesan sukses
        $_SESSION['message'] = "Selamat! Anda berhasil mendaftar pada mata praktikum.";
        $_SESSION['message_type'] = 'success';
    } else {
        // Jika gagal, beri pesan error
        $_SESSION['message'] = "Terjadi kesalahan. Gagal mendaftar praktikum.";
        $_SESSION['message_type'] = 'error';
    }
    $stmt_insert->close();
}

$stmt_check->close();
$conn->close();

// 5. Arahkan pengguna ke halaman "Praktikum Saya" untuk melihat hasilnya
header("Location: my_courses.php");
exit();

?>
