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
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: laporan.php");
    exit();
}
$laporan_id = $_GET['id'];

// Query untuk mengambil detail lengkap laporan, termasuk kolom link_laporan
$sql_detail = "SELECT 
            l.id, l.file_laporan, l.link_laporan, l.tanggal_kumpul, l.nilai, l.feedback,
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
            
            <!-- ====== BAGIAN YANG DIPERBARUI ====== -->
            <div>
                <p class="font-semibold">Hasil Laporan:</p>
                <?php if (!empty($laporan['file_laporan'])): // Jika ada file ?>
                    <a href="../uploads/laporan/<?php echo htmlspecialchars($laporan['file_laporan']); ?>" download class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg mt-2">
                        <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                        Unduh File Laporan
                    </a>
                <?php elseif (!empty($laporan['link_laporan'])): // Jika ada link ?>
                     <a href="<?php echo htmlspecialchars($laporan['link_laporan']); ?>" target="_blank" class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg mt-2">
                        <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" /></svg>
                        Buka Link Laporan
                    </a>
                <?php else: ?>
                    <p class="text-red-500 mt-2">Tidak ada laporan yang dikumpulkan.</p>
                <?php endif; ?>
            </div>
            <!-- ====== AKHIR DARI BAGIAN YANG DIPERBARUI ====== -->

        </div>
    </div>

    <!-- Kolom Kanan: Form Penilaian -->
    <div class="lg:col-span-2 bg-white p-8 rounded-lg shadow-lg">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Form Penilaian</h2>
        <form action="laporan_nilai.php" method="POST">
            <input type="hidden" name="laporan_id" value="<?php echo $laporan['id']; ?>">
            <div class="mb-4">
                <label for="nilai" class="block text-gray-700 text-sm font-bold mb-2">Nilai (Angka)</label>
                <input type="number" id="nilai" name="nilai" min="0" max="100" value="<?php echo htmlspecialchars($laporan['nilai']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="mb-6">
                <label for="feedback" class="block text-gray-700 text-sm font-bold mb-2">Feedback (Teks)</label>
                <textarea id="feedback" name="feedback" rows="8" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"><?php echo htmlspecialchars($laporan['feedback']); ?></textarea>
            </div>
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
$conn->close();
require_once 'templates/footer.php';
?>
