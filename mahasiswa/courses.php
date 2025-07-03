<?php
// 1. Definisi Variabel untuk Template
$pageTitle = 'Cari Praktikum';
$activePage = 'courses';
require_once '../config.php'; 
require_once 'templates/header_mahasiswa.php';

// 2. Logika untuk mengambil data mata praktikum
$search_term = isset($_GET['search']) ? $_GET['search'] : '';
$sql = "SELECT * FROM mata_praktikum";
$params = [];
$types = '';

if (!empty($search_term)) {
    $sql .= " WHERE nama_praktikum LIKE ? OR deskripsi LIKE ?";
    $like_term = "%" . $search_term . "%";
    $params = [$like_term, $like_term];
    $types = 'ss';
}

$sql .= " ORDER BY nama_praktikum ASC";
$stmt = $conn->prepare($sql);
if (count($params) > 0) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!-- Header Halaman -->
<div class="mb-8">
    <h1 class="text-4xl font-bold text-slate-800">Katalog Praktikum</h1>
    <p class="text-slate-500 mt-2">Temukan dan daftar pada mata praktikum yang tersedia di bawah ini.</p>
</div>

<!-- Form Pencarian -->
<div class="mb-8">
    <form action="courses.php" method="GET">
        <div class="relative">
            <input type="text" name="search" value="<?php echo htmlspecialchars($search_term); ?>" placeholder="Cari nama atau deskripsi praktikum..." class="w-full pl-12 pr-4 py-3 border border-slate-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500">
            <div class="absolute top-0 left-0 inline-flex items-center justify-center h-full w-12 text-slate-400">
                <svg class="h-6 w-6" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                    <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </div>
    </form>
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
                    <a href="enroll.php?course_id=<?php echo $row['id']; ?>" class="w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-300">
                        Daftar Praktikum
                    </a>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="md:col-span-2 lg:col-span-3 text-center py-16">
            <h3 class="text-2xl font-semibold text-slate-700">Tidak Ada Praktikum Ditemukan</h3>
            <p class="text-slate-500 mt-2">
                <?php if (!empty($search_term)): ?>
                    Praktikum dengan kata kunci "<?php echo htmlspecialchars($search_term); ?>" tidak dapat ditemukan. Coba kata kunci lain.
                <?php else: ?>
                    Saat ini belum ada mata praktikum yang tersedia.
                <?php endif; ?>
            </p>
        </div>
    <?php endif; ?>
</div>

<?php
// 3. Panggil Footer
$stmt->close();
$conn->close();
require_once 'templates/footer_mahasiswa.php';
?>
