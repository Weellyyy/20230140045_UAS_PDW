<?php
// Include file konfigurasi dan header
require_once '../config.php';
$pageTitle = 'Laporan Masuk';
$activePage = 'laporan';
require_once 'templates/header.php';

// --- LOGIKA FILTER ---
// Ambil nilai filter dari URL (jika ada)
$filter_modul = isset($_GET['modul']) ? $_GET['modul'] : '';
$filter_mahasiswa = isset($_GET['mahasiswa']) ? $_GET['mahasiswa'] : '';
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';

// Bangun query dasar
$sql = "SELECT 
            l.id, 
            l.tanggal_kumpul, 
            l.status,
            u.nama AS nama_mahasiswa,
            m.nama_modul,
            p.nama_praktikum
        FROM laporan l
        JOIN users u ON l.id_mahasiswa = u.id
        JOIN modul m ON l.id_modul = m.id
        JOIN mata_praktikum p ON m.id_praktikum = p.id";

// Tambahkan kondisi WHERE berdasarkan filter
$where_clauses = [];
$params = [];
$types = '';

if (!empty($filter_modul)) {
    $where_clauses[] = "l.id_modul = ?";
    $params[] = $filter_modul;
    $types .= 'i';
}
if (!empty($filter_mahasiswa)) {
    $where_clauses[] = "l.id_mahasiswa = ?";
    $params[] = $filter_mahasiswa;
    $types .= 'i';
}
if (!empty($filter_status)) {
    $where_clauses[] = "l.status = ?";
    $params[] = $filter_status;
    $types .= 's';
}

if (count($where_clauses) > 0) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
}

$sql .= " ORDER BY l.tanggal_kumpul DESC";

// Siapkan dan eksekusi statement
$stmt = $conn->prepare($sql);
if (count($params) > 0) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
// --- AKHIR LOGIKA FILTER ---


// --- Mengambil data untuk dropdown filter ---
$modul_list = $conn->query("SELECT id, nama_modul FROM modul ORDER BY nama_modul ASC");
$mahasiswa_list = $conn->query("SELECT id, nama FROM users WHERE role = 'mahasiswa' ORDER BY nama ASC");
?>

<!-- Form Filter -->
<div class="bg-white p-6 rounded-lg shadow-lg mb-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Filter Laporan</h2>
    <form action="laporan.php" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Filter Modul -->
        <div>
            <label for="modul" class="block text-sm font-medium text-gray-700">Modul</label>
            <select name="modul" id="modul" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                <option value="">Semua Modul</option>
                <?php while($modul = $modul_list->fetch_assoc()): ?>
                    <option value="<?php echo $modul['id']; ?>" <?php echo ($filter_modul == $modul['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($modul['nama_modul']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <!-- Filter Mahasiswa -->
        <div>
            <label for="mahasiswa" class="block text-sm font-medium text-gray-700">Mahasiswa</label>
            <select name="mahasiswa" id="mahasiswa" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                <option value="">Semua Mahasiswa</option>
                <?php while($mhs = $mahasiswa_list->fetch_assoc()): ?>
                    <option value="<?php echo $mhs['id']; ?>" <?php echo ($filter_mahasiswa == $mhs['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($mhs['nama']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <!-- Filter Status -->
        <div>
            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
            <select name="status" id="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                <option value="">Semua Status</option>
                <option value="dikumpulkan" <?php echo ($filter_status == 'dikumpulkan') ? 'selected' : ''; ?>>Dikumpulkan</option>
                <option value="dinilai" <?php echo ($filter_status == 'dinilai') ? 'selected' : ''; ?>>Sudah Dinilai</option>
            </select>
        </div>
        <!-- Tombol Aksi Filter -->
        <div class="self-end">
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">Filter</button>
        </div>
    </form>
</div>


<!-- Tabel untuk menampilkan data laporan -->
<div class="bg-white p-6 rounded-lg shadow-lg">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Daftar Laporan Masuk</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Mahasiswa</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Modul</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Praktikum</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Tanggal Kumpul</th>
                    <th class="text-center py-3 px-4 uppercase font-semibold text-sm">Status</th>
                    <th class="text-center py-3 px-4 uppercase font-semibold text-sm">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-4"><?php echo htmlspecialchars($row['nama_mahasiswa']); ?></td>
                        <td class="py-3 px-4"><?php echo htmlspecialchars($row['nama_modul']); ?></td>
                        <td class="py-3 px-4"><?php echo htmlspecialchars($row['nama_praktikum']); ?></td>
                        <td class="py-3 px-4"><?php echo date('d M Y, H:i', strtotime($row['tanggal_kumpul'])); ?></td>
                        <td class="py-3 px-4 text-center">
                            <?php if ($row['status'] == 'dinilai'): ?>
                                <span class="bg-green-200 text-green-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded-full">Dinilai</span>
                            <?php else: ?>
                                <span class="bg-yellow-200 text-yellow-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded-full">Dikumpulkan</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <a href="laporan_nilai.php?id=<?php echo $row['id']; ?>" class="bg-gray-600 hover:bg-gray-700 text-white text-xs font-bold py-1 px-3 rounded-lg inline-flex items-center">
                                Lihat & Nilai
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center py-4">Tidak ada laporan yang ditemukan sesuai filter.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
// Include footer
$stmt->close();
$conn->close();
require_once 'templates/footer.php';
?>
