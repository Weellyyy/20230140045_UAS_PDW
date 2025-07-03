<?php
// Include file konfigurasi dan header
require_once '../config.php';
$pageTitle = 'Manajemen Modul';
$activePage = 'modul';
require_once 'templates/header.php';

// Inisialisasi pesan
$message = '';
$message_type = '';

// Logika untuk menghapus modul
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $id_to_delete = $_POST['delete_id'];
    
    // 1. Dapatkan path file materi sebelum menghapus dari DB
    $sql_select = "SELECT file_materi FROM modul WHERE id = ?";
    $stmt_select = $conn->prepare($sql_select);
    $stmt_select->bind_param("i", $id_to_delete);
    $stmt_select->execute();
    $result_select = $stmt_select->get_result();
    if ($row = $result_select->fetch_assoc()) {
        $file_path = '../uploads/materi/' . $row['file_materi'];
        // 2. Hapus file fisik jika ada
        if (!empty($row['file_materi']) && file_exists($file_path)) {
            unlink($file_path);
        }
    }
    $stmt_select->close();

    // 3. Hapus record dari database
    $sql_delete = "DELETE FROM modul WHERE id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $id_to_delete);
    if ($stmt_delete->execute()) {
        $message = "Modul berhasil dihapus.";
        $message_type = 'success';
    } else {
        $message = "Gagal menghapus modul.";
        $message_type = 'error';
    }
    $stmt_delete->close();
}

// Mengambil semua data modul dengan join ke mata_praktikum
$sql = "SELECT m.id, m.nama_modul, m.file_materi, p.nama_praktikum 
        FROM modul m 
        JOIN mata_praktikum p ON m.id_praktikum = p.id 
        ORDER BY p.nama_praktikum, m.nama_modul ASC";
$result = $conn->query($sql);

?>

<!-- Tampilan Notifikasi -->
<?php if (isset($_SESSION['message'])): ?>
<div class="mb-4 p-4 rounded-md <?php echo $_SESSION['message_type'] === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
    <?php echo $_SESSION['message']; unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
</div>
<?php elseif ($message): ?>
<div class="mb-4 p-4 rounded-md <?php echo $message_type === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
    <?php echo $message; ?>
</div>
<?php endif; ?>


<!-- Tombol untuk menambah data baru -->
<div class="mb-6">
    <a href="modul_form.php" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg inline-flex items-center">
        <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
        Tambah Modul Baru
    </a>
</div>

<!-- Tabel untuk menampilkan data -->
<div class="bg-white p-6 rounded-lg shadow-lg">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Daftar Modul</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Nama Modul</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Mata Praktikum</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">File Materi</th>
                    <th class="text-center py-3 px-4 uppercase font-semibold text-sm">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-4"><?php echo htmlspecialchars($row['nama_modul']); ?></td>
                        <td class="py-3 px-4"><?php echo htmlspecialchars($row['nama_praktikum']); ?></td>
                        <td class="py-3 px-4">
                            <?php if (!empty($row['file_materi'])): ?>
                                <a href="../uploads/materi/<?php echo htmlspecialchars($row['file_materi']); ?>" target="_blank" class="text-blue-500 hover:underline">
                                    <?php echo htmlspecialchars($row['file_materi']); ?>
                                </a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <!-- Tombol Edit -->
                            <a href="modul_form.php?edit_id=<?php echo $row['id']; ?>" class="text-blue-500 hover:text-blue-700 mr-4">
                                <svg class="w-5 h-5 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                            </a>
                            <!-- Form untuk Hapus -->
                            <form action="modul.php" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus modul ini?');" class="inline-block">
                                <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="text-red-500 hover:text-red-700">
                                    <svg class="w-5 h-5 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center py-4">Belum ada data modul.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
// Include footer
$conn->close();
require_once 'templates/footer.php';
?>
