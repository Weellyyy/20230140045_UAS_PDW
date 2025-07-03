<?php
// Pindahkan semua logika PHP ke bagian paling atas, sebelum ada output HTML.
session_start(); // Pastikan session dimulai di awal jika menggunakan $_SESSION
require_once '../config.php';

// --- LOGIKA FORM SUBMISSION (CREATE/UPDATE) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $id_praktikum = $_POST['id_praktikum'];
    $nama_modul = trim($_POST['nama_modul']);
    $deskripsi = trim($_POST['deskripsi']);
    $file_materi = $_FILES['file_materi'];

    // Validasi dasar
    if (empty($id_praktikum) || empty($nama_modul)) {
        $_SESSION['message'] = "Mata praktikum dan nama modul tidak boleh kosong.";
        $_SESSION['message_type'] = 'error';
        // Redirect kembali ke form jika ada error validasi
        header("Location: modul_form.php" . (!empty($id) ? "?edit_id=$id" : ""));
        exit();
    } else {
        $file_name_to_db = $_POST['current_file']; // Default ke file yang sudah ada

        // Proses upload file jika ada file baru yang diunggah
        if (isset($file_materi) && $file_materi['error'] == 0) {
            $upload_dir = '../uploads/materi/';
            $new_file_name = time() . '_' . basename($file_materi["name"]);
            $target_file = $upload_dir . $new_file_name;

            if (move_uploaded_file($file_materi["tmp_name"], $target_file)) {
                if (!empty($file_name_to_db) && file_exists($upload_dir . $file_name_to_db)) {
                    unlink($upload_dir . $file_name_to_db);
                }
                $file_name_to_db = $new_file_name;
            } else {
                 $_SESSION['message'] = "Gagal mengunggah file materi.";
                 $_SESSION['message_type'] = 'error';
                 header("Location: modul.php");
                 exit();
            }
        }

        // Jika ada ID, lakukan UPDATE
        if (!empty($id)) {
            $sql = "UPDATE modul SET id_praktikum = ?, nama_modul = ?, deskripsi = ?, file_materi = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isssi", $id_praktikum, $nama_modul, $deskripsi, $file_name_to_db, $id);
            $action = 'diperbarui';
        } 
        // Jika tidak ada ID, lakukan INSERT
        else {
            $sql = "INSERT INTO modul (id_praktikum, nama_modul, deskripsi, file_materi) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isss", $id_praktikum, $nama_modul, $deskripsi, $file_name_to_db);
            $action = 'ditambahkan';
        }

        if ($stmt->execute()) {
            $_SESSION['message'] = "Modul berhasil $action.";
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = "Gagal memproses data modul: " . $stmt->error;
            $_SESSION['message_type'] = 'error';
        }
        $stmt->close();
        // Setelah selesai, redirect ke halaman utama modul
        header("Location: modul.php");
        exit();
    }
}
// --- AKHIR LOGIKA FORM SUBMISSION ---


// --- LOGIKA PENGAMBILAN DATA UNTUK FORM ---
// Inisialisasi variabel
$id = '';
$id_praktikum = '';
$nama_modul = '';
$deskripsi = '';
$current_file = '';
$form_title = 'Tambah Modul Baru';

// Cek jika ini adalah mode edit untuk mengisi form
if (isset($_GET['edit_id'])) {
    $id_to_edit = $_GET['edit_id'];
    $sql = "SELECT * FROM modul WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_to_edit);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $id = $row['id'];
        $id_praktikum = $row['id_praktikum'];
        $nama_modul = $row['nama_modul'];
        $deskripsi = $row['deskripsi'];
        $current_file = $row['file_materi'];
        $form_title = 'Edit Modul';
    }
    $stmt->close();
}

// Mengambil daftar mata praktikum untuk dropdown
$praktikum_list = $conn->query("SELECT id, nama_praktikum FROM mata_praktikum ORDER BY nama_praktikum ASC");
// --- AKHIR LOGIKA PENGAMBILAN DATA ---


// Sekarang baru kita panggil header, setelah semua logika selesai
$pageTitle = 'Form Modul';
$activePage = 'modul';
require_once 'templates/header.php';
?>

<!-- Tampilan Notifikasi untuk error validasi -->
<?php if (isset($_SESSION['message']) && $_SESSION['message_type'] == 'error'): ?>
<div class="mb-4 p-4 rounded-md bg-red-100 text-red-800">
    <?php echo $_SESSION['message']; unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
</div>
<?php endif; ?>

<div class="bg-white p-8 rounded-lg shadow-lg max-w-2xl mx-auto">
    <h2 class="text-2xl font-bold text-gray-800 mb-6"><?php echo $form_title; ?></h2>
    
    <form action="modul_form.php" method="POST" enctype="multipart/form-data">
        <!-- Hidden input untuk ID (digunakan saat edit) -->
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <input type="hidden" name="current_file" value="<?php echo $current_file; ?>">
        
        <!-- Dropdown untuk Mata Praktikum -->
        <div class="mb-4">
            <label for="id_praktikum" class="block text-gray-700 text-sm font-bold mb-2">Mata Praktikum</label>
            <select id="id_praktikum" name="id_praktikum" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                <option value="">-- Pilih Mata Praktikum --</option>
                <?php while($prak = $praktikum_list->fetch_assoc()): ?>
                    <option value="<?php echo $prak['id']; ?>" <?php echo ($id_praktikum == $prak['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($prak['nama_praktikum']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <!-- Form Group untuk Nama Modul -->
        <div class="mb-4">
            <label for="nama_modul" class="block text-gray-700 text-sm font-bold mb-2">Nama Modul</label>
            <input type="text" id="nama_modul" name="nama_modul" value="<?php echo htmlspecialchars($nama_modul); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
        </div>
        
        <!-- Form Group untuk Deskripsi -->
        <div class="mb-4">
            <label for="deskripsi" class="block text-gray-700 text-sm font-bold mb-2">Deskripsi</label>
            <textarea id="deskripsi" name="deskripsi" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"><?php echo htmlspecialchars($deskripsi); ?></textarea>
        </div>

        <!-- Form Group untuk File Materi -->
        <div class="mb-6">
            <label for="file_materi" class="block text-gray-700 text-sm font-bold mb-2">File Materi (PDF/DOCX)</label>
            <input type="file" id="file_materi" name="file_materi" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            <?php if ($current_file): ?>
                <p class="text-sm text-gray-600 mt-2">File saat ini: <a href="../uploads/materi/<?php echo $current_file; ?>" target="_blank" class="text-blue-500 hover:underline"><?php echo $current_file; ?></a></p>
                <p class="text-xs text-gray-500">Kosongkan jika tidak ingin mengubah file.</p>
            <?php endif; ?>
        </div>
        
        <!-- Tombol Aksi -->
        <div class="flex items-center justify-end">
            <a href="modul.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg mr-2">
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
