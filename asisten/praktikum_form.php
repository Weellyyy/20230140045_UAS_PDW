<?php
// Include file konfigurasi dan header
require_once '../config.php';
$pageTitle = 'Form Mata Praktikum';
$activePage = 'praktikum';
require_once 'templates/header.php';

// Inisialisasi variabel
$id = '';
$nama_praktikum = '';
$deskripsi = '';
$form_title = 'Tambah Mata Praktikum Baru';
$action_url = 'praktikum.php';

// Cek jika ini adalah mode edit
if (isset($_GET['edit_id'])) {
    $id_to_edit = $_GET['edit_id'];
    $sql = "SELECT * FROM mata_praktikum WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_to_edit);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $id = $row['id'];
        $nama_praktikum = $row['nama_praktikum'];
        $deskripsi = $row['deskripsi'];
        $form_title = 'Edit Mata Praktikum';
    }
    $stmt->close();
}

?>

<div class="bg-white p-8 rounded-lg shadow-lg max-w-2xl mx-auto">
    <h2 class="text-2xl font-bold text-gray-800 mb-6"><?php echo $form_title; ?></h2>
    
    <form action="<?php echo $action_url; ?>" method="POST">
        <!-- Hidden input untuk ID (digunakan saat edit) -->
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        
        <!-- Form Group untuk Nama Praktikum -->
        <div class="mb-4">
            <label for="nama_praktikum" class="block text-gray-700 text-sm font-bold mb-2">Nama Praktikum</label>
            <input type="text" id="nama_praktikum" name="nama_praktikum" value="<?php echo htmlspecialchars($nama_praktikum); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
        </div>
        
        <!-- Form Group untuk Deskripsi -->
        <div class="mb-6">
            <label for="deskripsi" class="block text-gray-700 text-sm font-bold mb-2">Deskripsi</p>
            <textarea id="deskripsi" name="deskripsi" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"><?php echo htmlspecialchars($deskripsi); ?></textarea>
        </div>
        
        <!-- Tombol Aksi -->
        <div class="flex items-center justify-end">
            <a href="praktikum.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg mr-2">
                Batal
            </a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                Simpan
            </button>
        </div>
    </form>
</div>

<?php
// Include footer
$conn->close();
require_once 'templates/footer.php';
?>
