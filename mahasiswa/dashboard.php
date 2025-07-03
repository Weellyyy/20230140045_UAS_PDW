<?php
// 1. Definisi Variabel untuk Template & Koneksi
$pageTitle = 'Dashboard';
$activePage = 'dashboard';
require_once '../config.php'; 
require_once 'templates/header_mahasiswa.php';

// 2. Logika untuk mengambil data statistik dinamis
$id_mahasiswa = $_SESSION['user_id'];

// Jumlah Praktikum Diikuti
$stmt_prak = $conn->prepare("SELECT COUNT(id) AS total FROM pendaftaran_praktikum WHERE id_mahasiswa = ?");
$stmt_prak->bind_param("i", $id_mahasiswa);
$stmt_prak->execute();
$prak_diikuti = $stmt_prak->get_result()->fetch_assoc()['total'];
$stmt_prak->close();

// Jumlah Tugas Selesai (dinilai)
$stmt_selesai = $conn->prepare("SELECT COUNT(id) AS total FROM laporan WHERE id_mahasiswa = ? AND status = 'dinilai'");
$stmt_selesai->bind_param("i", $id_mahasiswa);
$stmt_selesai->execute();
$tugas_selesai = $stmt_selesai->get_result()->fetch_assoc()['total'];
$stmt_selesai->close();

// Jumlah Tugas Menunggu (dikumpulkan, belum dinilai)
$stmt_tunggu = $conn->prepare("SELECT COUNT(id) AS total FROM laporan WHERE id_mahasiswa = ? AND status = 'dikumpulkan'");
$stmt_tunggu->bind_param("i", $id_mahasiswa);
$stmt_tunggu->execute();
$tugas_menunggu = $stmt_tunggu->get_result()->fetch_assoc()['total'];
$stmt_tunggu->close();

// Mengambil 3 notifikasi nilai terbaru
$notif_nilai = $conn->prepare("
    SELECT m.nama_modul, p.nama_praktikum, l.id as laporan_id, p.id as praktikum_id
    FROM laporan l
    JOIN modul m ON l.id_modul = m.id
    JOIN mata_praktikum p ON m.id_praktikum = p.id
    WHERE l.id_mahasiswa = ? AND l.status = 'dinilai'
    ORDER BY l.tanggal_kumpul DESC
    LIMIT 3
");
$notif_nilai->bind_param("i", $id_mahasiswa);
$notif_nilai->execute();
$result_notif = $notif_nilai->get_result();
$notif_nilai->close();

?>

<!-- Welcome Banner dengan Gradien Baru -->
<div class="bg-gradient-to-br from-blue-600 to-indigo-700 text-white p-8 rounded-2xl shadow-lg mb-8">
    <h1 class="text-4xl font-extrabold tracking-tight">Selamat Datang, <?php echo htmlspecialchars(explode(' ', $_SESSION['nama'])[0]); ?>!</h1>
    <p class="mt-2 text-lg opacity-90">Ini adalah pusat kendali untuk semua aktivitas praktikum Anda. Terus semangat!</p>
</div>

<!-- Grid untuk Kartu Statistik yang Ditingkatkan -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    
    <!-- Card: Praktikum Diikuti -->
    <div class="bg-white p-6 rounded-2xl shadow-md hover:shadow-xl transition-shadow flex items-center space-x-5">
        <div class="bg-blue-100 p-4 rounded-xl">
            <svg class="w-8 h-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
        </div>
        <div>
            <p class="text-sm font-medium text-slate-500">Praktikum Diikuti</p>
            <p class="text-3xl font-bold text-slate-800"><?php echo $prak_diikuti; ?></p>
        </div>
    </div>
    
    <!-- Card: Tugas Selesai -->
    <div class="bg-white p-6 rounded-2xl shadow-md hover:shadow-xl transition-shadow flex items-center space-x-5">
        <div class="bg-green-100 p-4 rounded-xl">
            <svg class="w-8 h-8 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </div>
        <div>
            <p class="text-sm font-medium text-slate-500">Tugas Selesai</p>
            <p class="text-3xl font-bold text-slate-800"><?php echo $tugas_selesai; ?></p>
        </div>
    </div>
    
    <!-- Card: Tugas Menunggu -->
    <div class="bg-white p-6 rounded-2xl shadow-md hover:shadow-xl transition-shadow flex items-center space-x-5">
        <div class="bg-amber-100 p-4 rounded-xl">
            <svg class="w-8 h-8 text-amber-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </div>
        <div>
            <p class="text-sm font-medium text-slate-500">Tugas Menunggu Nilai</p>
            <p class="text-3xl font-bold text-slate-800"><?php echo $tugas_menunggu; ?></p>
        </div>
    </div>
    
</div>

<!-- Notifikasi Terbaru yang Dinamis -->
<div class="bg-white p-6 rounded-2xl shadow-md">
    <h3 class="text-2xl font-bold text-slate-800 mb-4">Notifikasi Terbaru</h3>
    <div class="space-y-2">
        <?php if ($result_notif->num_rows > 0): ?>
            <?php while($notif = $result_notif->fetch_assoc()): ?>
            <a href="course_detail.php?id=<?php echo $notif['praktikum_id']; ?>" class="flex items-center p-4 rounded-lg hover:bg-slate-100">
                <span class="text-2xl mr-4">🔔</span>
                <div class="flex-grow">
                    <p class="text-slate-700">
                        Nilai untuk <strong class="font-semibold text-blue-600"><?php echo htmlspecialchars($notif['nama_modul']); ?></strong> 
                        (<?php echo htmlspecialchars($notif['nama_praktikum']); ?>) telah diberikan.
                    </p>
                </div>
                <svg class="w-5 h-5 text-slate-400 ml-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
            </a>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="text-center py-8">
                <p class="text-slate-500">Belum ada notifikasi baru untuk Anda.</p>
            </div>
        <?php endif; ?>
    </ul>
</div>

<?php
// Panggil Footer
$conn->close();
require_once 'templates/footer_mahasiswa.php';
?>
