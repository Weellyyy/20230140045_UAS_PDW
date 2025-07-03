<?php
// 1. Definisi Variabel untuk Template
$pageTitle = 'Praktikum Saya';
$activePage = 'my_courses';
require_once '../config.php'; 
require_once 'templates/header_mahasiswa.php';

// 2. Logika untuk mengambil data praktikum yang diikuti oleh mahasiswa yang login
$id_mahasiswa = $_SESSION['user_id'];

$sql = "SELECT p.id, p.nama_praktikum, p.deskripsi 
        FROM mata_praktikum p
        JOIN pendaftaran_praktikum pp ON p.id = pp.id_praktikum
        WHERE pp.id_mahasiswa = ?
        ORDER BY p.nama_praktikum ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_mahasiswa);
$stmt->execute();
$result = $stmt->get_result();
?>

<!-- Tampilan Notifikasi (setelah mendaftar) -->
<?php if (isset($_SESSION['message'])): ?>
<div class="mb-6 p-4 rounded-lg <?php echo $_SESSION['message_type'] === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
    <?php echo $_SESSION['message']; unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
</div>
<?php endif; ?>

<!-- Header Halaman -->
<div class="mb-8">
    <h1 class="text-4xl font-bold text-slate-800">Praktikum Saya</h1>
    <p class="text-slate-500 mt-2">Berikut adalah daftar semua mata praktikum yang sedang Anda ikuti.</p>
</div>

<!-- Grid untuk Kartu Praktikum -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition-shadow overflow-hidden flex flex-col">
                <div class="p-6 flex-grow">
                    <h3 class="text-xl font-bold text-slate-800 mb-2"><?php echo htmlspecialchars($row['nama_praktikum']); ?></h3>
                    <p class="text-slate-600 text-sm line-clamp-3">
                        <?php echo htmlspecialchars($row['deskripsi']); ?>
                    </p>
                </div>
                <div class="p-6 bg-slate-50 border-t">
                    <!-- Tombol ini akan kita fungsikan di langkah selanjutnya -->
                    <a href="course_detail.php?id=<?php echo $row['id']; ?>" class="w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-300">
                        Lihat Detail & Tugas
                    </a>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="md:col-span-2 lg:col-span-3 text-center py-16 bg-white rounded-2xl shadow-md">
            <h3 class="text-2xl font-semibold text-slate-700">Anda Belum Mengikuti Praktikum Apapun</h3>
            <p class="text-slate-500 mt-2">Silakan cari praktikum yang Anda minati di halaman katalog.</p>
            <a href="courses.php" class="mt-6 inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition-colors duration-300">
                Cari Praktikum Sekarang
            </a>
        </div>
    <?php endif; ?>
</div>

<?php
// 3. Panggil Footer
$stmt->close();
$conn->close();
require_once 'templates/footer_mahasiswa.php';
?>
