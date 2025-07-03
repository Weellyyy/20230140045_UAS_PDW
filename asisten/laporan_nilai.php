<?php
// Pindahkan semua logika PHP ke bagian paling atas
session_start();
require_once '../config.php';

// --- LOGIKA FORM SUBMISSION (UPDATE NILAI) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['laporan_id'])) {
    $laporan_id = $_POST['laporan_id'];
    $nilai = !empty($_POST['nilai']) ? (int)$_POST['nilai'] : null;
    $feedback = trim($_POST['feedback']);
    $status = 'dinilai';

    $sql = "UPDATE laporan SET nilai = ?, feedback = ?, status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issi", $nilai, $feedback, $status, $laporan_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Nilai dan feedback berhasil disimpan.";
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = "Gagal menyimpan nilai.";
        $_SESSION['message_type'] = 'error';
    }
    $stmt->close();
    // Redirect kembali ke halaman daftar laporan
    header("Location: laporan.php");
    exit();
}
// --- AKHIR LOGIKA FORM SUBMISSION ---


// --- LOGIKA PENGAMBILAN DATA UNTUK DITAMPILKAN ---
// Pastikan ID laporan ada di URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: laporan.php");
    exit();
}
$laporan_id = $_GET['id'];

// Query untuk mengambil detail lengkap laporan
$sql_detail = "SELECT 
            l.id, l.file_laporan, l.tanggal_kumpul, l.nilai, l.feedback,
            u.nama AS nama_mahasiswa,
            m.nama_modul,
            p.nama_praktikum
        FROM laporan l
        JOIN users u ON l.id_mahasiswa = u.id
        JOIN modul m ON l.id_modul = m.id
        JOIN mata_praktikum p ON m.id_praktikum = p.id
        WHERE l.id = ?";
$stmt_detail = $conn->prepare($sql_detail);
$stmt_detail->bind_param("i", $laporan_id);
$stmt_detail->execute();
$result_detail = $stmt_detail->get_result();

if ($result_detail->num_rows === 0) {
    $_SESSION['message'] = "Laporan tidak ditemukan.";
    $_SESSION['message_type'] = 'error';
    header("Location: laporan.php");
    exit();
}
$laporan = $result_detail->fetch_assoc();
$stmt_detail->close();
// --- AKHIR LOGIKA PENGAMBILAN DATA ---


// Sekarang baru kita panggil header, setelah semua logika selesai
$pageTitle = 'Detail & Penilaian Laporan';
$activePage = 'laporan';
require_once 'templates/header.php';
?>

<!-- Tampilan Detail dan Form Penilaian -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- Kolom Kiri: Detail Laporan -->
    <div class="lg:col-span-1 bg-white p-6 rounded-lg shadow-lg h-fit">
        <h3 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Detail Pengumpulan</h3>
        <div class="space-y-3 text-gray-700">
            <div>
                <p class="font-semibold">Mahasiswa:</p>
                <p><?php echo htmlspecialchars($laporan['nama_mahasiswa']); ?></p>
            </div>
            <div>
                <p class="font-semibold">Praktikum:</p>
                <p><?php echo htmlspecialchars($laporan['nama_praktikum']); ?></p>
            </div>
            <div>
                <p class="font-semibold">Modul:</p>
                <p><?php echo htmlspecialchars($laporan['nama_modul']); ?></p>
            </div>
            <div>
                <p class="font-semibold">Tanggal Kumpul:</p>
                <p><?php echo date('d M Y, H:i', strtotime($laporan['tanggal_kumpul'])); ?></p>
            </div>
            <div>
                <p class="font-semibold">File Laporan:</p>
                <?php if (!empty($laporan['file_laporan'])): 
                    $file_path = '../uploads/laporan/' . htmlspecialchars($laporan['file_laporan']);
                ?>
                    <a href="<?php echo $file_path; ?>" download class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg inline-flex items-center mt-2">
                        <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                        Unduh Laporan
                    </a>
                <?php else: ?>
                    <p class="text-red-500">File tidak ditemukan.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Form Penilaian -->
    <div class="lg:col-span-2 bg-white p-8 rounded-lg shadow-lg">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Form Penilaian</h2>
        
        <form action="laporan_nilai.php" method="POST">
            <!-- Hidden input untuk ID Laporan -->
            <input type="hidden" name="laporan_id" value="<?php echo $laporan['id']; ?>">
            
            <!-- Form Group untuk Nilai -->
            <div class="mb-4">
                <label for="nilai" class="block text-gray-700 text-sm font-bold mb-2">Nilai (Angka)</label>
                <input type="number" id="nilai" name="nilai" min="0" max="100" value="<?php echo htmlspecialchars($laporan['nilai']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            
            <!-- Form Group untuk Feedback -->
            <div class="mb-6">
                <label for="feedback" class="block text-gray-700 text-sm font-bold mb-2">Feedback (Teks)</label>
                <textarea id="feedback" name="feedback" rows="8" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"><?php echo htmlspecialchars($laporan['feedback']); ?></textarea>
            </div>
            
            <!-- Tombol Aksi -->
            <div class="flex items-center justify-end">
                <a href="laporan.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg mr-2">
                    Kembali
                </a>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg">
                    Simpan Penilaian
                </button>
            </div>
        </form>
    </div>
</div>

<?php
// Include footer
$conn->close();
require_once 'templates/footer.php';
?>
