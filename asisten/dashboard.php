<?php
// 1. Definisi Variabel untuk Template
$pageTitle = 'Dashboard';
$activePage = 'dashboard';
require_once '../config.php'; 
require_once 'templates/header.php';

// 2. Logika untuk mengambil data statistik dari database
// Total Mata Praktikum
$total_praktikum_res = $conn->query("SELECT COUNT(id) AS total FROM mata_praktikum");
$total_praktikum = $total_praktikum_res->fetch_assoc()['total'];

// Total Mahasiswa
$total_mahasiswa_res = $conn->query("SELECT COUNT(id) AS total FROM users WHERE role = 'mahasiswa'");
$total_mahasiswa = $total_mahasiswa_res->fetch_assoc()['total'];

// Total Laporan Masuk
$total_laporan_res = $conn->query("SELECT COUNT(id) AS total FROM laporan");
$total_laporan = $total_laporan_res->fetch_assoc()['total'];

// Laporan yang belum dinilai
$laporan_pending_res = $conn->query("SELECT COUNT(id) AS total FROM laporan WHERE status = 'dikumpulkan'");
$laporan_pending = $laporan_pending_res->fetch_assoc()['total'];

// 5 Laporan terbaru untuk aktivitas
$aktivitas_terbaru = $conn->query("
    SELECT u.nama AS nama_mahasiswa, m.nama_modul, l.tanggal_kumpul
    FROM laporan l
    JOIN users u ON l.id_mahasiswa = u.id
    JOIN modul m ON l.id_modul = m.id
    ORDER BY l.tanggal_kumpul DESC
    LIMIT 5
");

?>

<!-- Welcome Banner -->
<div class="bg-gradient-to-r from-blue-600 to-cyan-500 text-white p-8 rounded-2xl shadow-lg mb-8">
    <h2 class="text-3xl font-bold">Selamat Datang, <?php echo htmlspecialchars($_SESSION['nama']); ?>!</h2>
    <p class="mt-2 text-lg opacity-90">Ini adalah ringkasan aktivitas di sistem praktikum Anda.</p>
</div>


<!-- Grid untuk Kartu Statistik -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    
    <!-- Card 1: Total Praktikum -->
    <div class="bg-white p-6 rounded-2xl shadow-md hover:shadow-xl transition-shadow flex items-center space-x-4">
        <div class="bg-blue-100 p-4 rounded-full">
            <svg class="w-8 h-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
        </div>
        <div>
            <p class="text-sm text-slate-500">Total Praktikum</p>
            <p class="text-3xl font-bold text-slate-800"><?php echo $total_praktikum; ?></p>
        </div>
    </div>

    <!-- Card 2: Total Mahasiswa -->
    <div class="bg-white p-6 rounded-2xl shadow-md hover:shadow-xl transition-shadow flex items-center space-x-4">
        <div class="bg-green-100 p-4 rounded-full">
            <svg class="w-8 h-8 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m-7.5-2.962A3.75 3.75 0 0112 15v-2.25A3.75 3.75 0 0115.75 9v-2.25A3.75 3.75 0 0112 3V.75m-6.506 5.668a3.75 3.75 0 01-2.25-2.25A3.75 3.75 0 015.25 3V.75m8.25 0a2.25 2.25 0 012.25 2.25m0 0a2.25 2.25 0 01-2.25 2.25m0 0a2.25 2.25 0 01-2.25-2.25m2.25-2.25a2.25 2.25 0 012.25-2.25M9 15.75A3.75 3.75 0 0112 12v-2.25A3.75 3.75 0 019 5.25v-2.25A3.75 3.75 0 015.25.75m0 0A2.25 2.25 0 013 3m0 0a2.25 2.25 0 01-2.25 2.25m0 0a2.25 2.25 0 012.25 2.25m-2.25 2.25a2.25 2.25 0 012.25-2.25m0 0a2.25 2.25 0 01-2.25-2.25m6.75-3.375a3.75 3.75 0 01-2.25-2.25m2.25 2.25a3.75 3.75 0 012.25 2.25m-11.25 3.542a3.75 3.75 0 012.25-2.25m2.25 2.25a3.75 3.75 0 01-2.25 2.25m-2.25-2.25a3.75 3.75 0 01-2.25-2.25m11.25 0a3.75 3.75 0 012.25 2.25m-2.25-2.25a3.75 3.75 0 012.25-2.25" /></svg>
        </div>
        <div>
            <p class="text-sm text-slate-500">Total Mahasiswa</p>
            <p class="text-3xl font-bold text-slate-800"><?php echo $total_mahasiswa; ?></p>
        </div>
    </div>

    <!-- Card 3: Total Laporan -->
    <div class="bg-white p-6 rounded-2xl shadow-md hover:shadow-xl transition-shadow flex items-center space-x-4">
        <div class="bg-indigo-100 p-4 rounded-full">
            <svg class="w-8 h-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75c0-.231-.035-.454-.1-.664M6.75 7.5h1.5M6.75 12h1.5m6.75 0h1.5m-1.5 3h1.5m-1.5 3h1.5M4.5 6.75h1.5v1.5H4.5v-1.5zM4.5 12h1.5v1.5H4.5v-1.5zM4.5 17.25h1.5v1.5H4.5v-1.5z" /></svg>
        </div>
        <div>
            <p class="text-sm text-slate-500">Total Laporan Masuk</p>
            <p class="text-3xl font-bold text-slate-800"><?php echo $total_laporan; ?></p>
        </div>
    </div>

    <!-- Card 4: Laporan Pending -->
    <div class="bg-white p-6 rounded-2xl shadow-md hover:shadow-xl transition-shadow flex items-center space-x-4">
        <div class="bg-amber-100 p-4 rounded-full">
            <svg class="w-8 h-8 text-amber-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </div>
        <div>
            <p class="text-sm text-slate-500">Laporan Belum Dinilai</p>
            <p class="text-3xl font-bold text-slate-800"><?php echo $laporan_pending; ?></p>
        </div>
    </div>
</div>

<!-- Aktivitas Terbaru -->
<div class="bg-white p-6 rounded-2xl shadow-md mt-8">
    <h3 class="text-xl font-bold text-slate-800 mb-4">Aktivitas Laporan Terbaru</h3>
    <div class="space-y-4">
        <?php if ($aktivitas_terbaru->num_rows > 0): ?>
            <?php while($aktivitas = $aktivitas_terbaru->fetch_assoc()): ?>
            <div class="flex items-center p-3 hover:bg-slate-50 rounded-lg">
                <div class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center mr-4 flex-shrink-0">
                    <span class="font-bold text-slate-500">
                        <?php echo strtoupper(substr($aktivitas['nama_mahasiswa'], 0, 1)); ?>
                    </span>
                </div>
                <div class="flex-grow">
                    <p class="text-slate-800">
                        <strong class="font-semibold"><?php echo htmlspecialchars($aktivitas['nama_mahasiswa']); ?></strong> 
                        mengumpulkan laporan untuk 
                        <strong class="font-semibold"><?php echo htmlspecialchars($aktivitas['nama_modul']); ?></strong>.
                    </p>
                </div>
                <div class="text-sm text-slate-500 text-right flex-shrink-0 ml-4">
                    <?php echo date('d M Y, H:i', strtotime($aktivitas['tanggal_kumpul'])); ?>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-center text-slate-500 py-4">Belum ada aktivitas laporan.</p>
        <?php endif; ?>
    </div>
</div>


<?php
// 3. Panggil Footer
$conn->close();
require_once 'templates/footer.php';
?>
