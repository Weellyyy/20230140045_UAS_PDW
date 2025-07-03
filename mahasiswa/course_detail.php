<?php
// Pindahkan semua logika PHP ke bagian paling atas
session_start();
require_once '../config.php';

// --- LOGIKA PENGUMPULAN LAPORAN (FILE ATAU LINK) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_modul'])) {
    $id_mahasiswa = $_SESSION['user_id'];
    $id_modul = $_POST['id_modul'];
    $id_praktikum = $_POST['id_praktikum'];
    $submission_type = $_POST['submission_type'];

    $file_laporan_name = null;
    $link_laporan_url = null;

    $is_success = false;

    // Jika tipe pengumpulan adalah 'file'
    if ($submission_type === 'file') {
        $file_laporan = $_FILES['file_laporan'];
        if (isset($file_laporan) && $file_laporan['error'] == 0) {
            $upload_dir = '../uploads/laporan/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $new_file_name = $id_mahasiswa . '_' . $id_modul . '_' . time() . '_' . basename($file_laporan["name"]);
            $target_file = $upload_dir . $new_file_name;

            if (move_uploaded_file($file_laporan["tmp_name"], $target_file)) {
                $file_laporan_name = $new_file_name;
                $is_success = true;
            } else {
                $_SESSION['message'] = "Gagal mengunggah file laporan.";
                $_SESSION['message_type'] = 'error';
            }
        } else {
            $_SESSION['message'] = "Tidak ada file yang dipilih atau terjadi error saat upload.";
            $_SESSION['message_type'] = 'error';
        }
    } 
    // Jika tipe pengumpulan adalah 'link'
    elseif ($submission_type === 'link') {
        $link_laporan_url = trim($_POST['link_laporan']);
        if (!empty($link_laporan_url) && filter_var($link_laporan_url, FILTER_VALIDATE_URL)) {
            $is_success = true;
        } else {
            $_SESSION['message'] = "URL yang Anda masukkan tidak valid.";
            $_SESSION['message_type'] = 'error';
        }
    }

    // Jika salah satu metode berhasil, simpan ke database
    if ($is_success) {
        $sql = "INSERT INTO laporan (id_modul, id_mahasiswa, file_laporan, link_laporan, status) VALUES (?, ?, ?, ?, 'dikumpulkan')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiss", $id_modul, $id_mahasiswa, $file_laporan_name, $link_laporan_url);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = "Laporan berhasil dikumpulkan.";
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = "Gagal menyimpan data laporan ke database.";
            $_SESSION['message_type'] = 'error';
        }
        $stmt->close();
    }
    
    header("Location: course_detail.php?id=" . $id_praktikum);
    exit();
}
// --- AKHIR LOGIKA PENGUMPULAN ---

// --- LOGIKA PENGAMBILAN DATA ---
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: my_courses.php");
    exit();
}
$id_praktikum = $_GET['id'];
$id_mahasiswa = $_SESSION['user_id'];

$stmt_praktikum = $conn->prepare("SELECT nama_praktikum, deskripsi FROM mata_praktikum WHERE id = ?");
$stmt_praktikum->bind_param("i", $id_praktikum);
$stmt_praktikum->execute();
$praktikum = $stmt_praktikum->get_result()->fetch_assoc();
$stmt_praktikum->close();

$sql_modul = "SELECT 
                m.id, m.nama_modul, m.deskripsi, m.file_materi,
                l.file_laporan, l.link_laporan, l.nilai, l.feedback, l.status
              FROM modul m
              LEFT JOIN laporan l ON m.id = l.id_modul AND l.id_mahasiswa = ?
              WHERE m.id_praktikum = ?
              ORDER BY m.id ASC";
$stmt_modul = $conn->prepare($sql_modul);
$stmt_modul->bind_param("ii", $id_mahasiswa, $id_praktikum);
$stmt_modul->execute();
$result_modul = $stmt_modul->get_result();
// --- AKHIR LOGIKA PENGAMBILAN DATA ---

$pageTitle = 'Detail Praktikum';
$activePage = 'my_courses';
require_once 'templates/header_mahasiswa.php';
?>

<!-- Tampilan Notifikasi -->
<?php if (isset($_SESSION['message'])): ?>
<div class="mb-6 p-4 rounded-lg <?php echo $_SESSION['message_type'] === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
    <?php echo $_SESSION['message']; unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
</div>
<?php endif; ?>

<!-- Header Halaman -->
<div class="mb-8 bg-white p-8 rounded-2xl shadow-md">
    <h1 class="text-4xl font-bold text-slate-800"><?php echo htmlspecialchars($praktikum['nama_praktikum']); ?></h1>
    <p class="text-slate-600 mt-2"><?php echo htmlspecialchars($praktikum['deskripsi']); ?></p>
</div>

<!-- Daftar Modul (Accordion) -->
<div class="space-y-4">
    <?php if ($result_modul->num_rows > 0): ?>
        <?php while($modul = $result_modul->fetch_assoc()): ?>
            <div class="bg-white rounded-2xl shadow-md overflow-hidden" x-data="{ open: false }">
                <div @click="open = !open" class="p-6 cursor-pointer flex justify-between items-center">
                    <h3 class="text-xl font-bold text-slate-800"><?php echo htmlspecialchars($modul['nama_modul']); ?></h3>
                    <svg class="w-6 h-6 text-slate-500 transition-transform" :class="{ 'rotate-180': open }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                </div>
                <div x-show="open" x-transition class="p-6 border-t border-slate-200">
                    <p class="text-slate-600 mb-6"><?php echo htmlspecialchars($modul['deskripsi']); ?></p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-slate-50 p-4 rounded-lg">
                            <h4 class="font-bold text-slate-700 mb-2">Materi Pembelajaran</h4>
                            <?php if (!empty($modul['file_materi'])): ?>
                                <a href="../uploads/materi/<?php echo htmlspecialchars($modul['file_materi']); ?>" download class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                                    <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                                    Unduh Materi
                                </a>
                            <?php else: ?>
                                <p class="text-sm text-slate-500">Materi belum tersedia.</p>
                            <?php endif; ?>
                        </div>
                        <div class="bg-slate-50 p-4 rounded-lg">
                            <h4 class="font-bold text-slate-700 mb-2">Laporan & Penilaian</h4>
                            <?php if ($modul['status']): // Jika sudah ada laporan ?>
                                <div class="space-y-3">
                                    <p class="text-sm font-semibold">Status: 
                                        <span class="capitalize px-2 py-1 text-xs font-semibold rounded-full <?php echo $modul['status'] == 'dinilai' ? 'bg-green-200 text-green-800' : 'bg-yellow-200 text-yellow-800'; ?>">
                                            <?php echo $modul['status'] == 'dinilai' ? 'Sudah Dinilai' : 'Telah Dikumpulkan'; ?>
                                        </span>
                                    </p>
                                    <p class="text-sm font-semibold">Nilai: 
                                        <span class="font-normal text-blue-600 text-lg"><?php echo $modul['nilai'] ?? 'Belum dinilai'; ?></span>
                                    </p>
                                    <div>
                                        <p class="text-sm font-semibold">Feedback Asisten:</p>
                                        <p class="text-sm text-slate-600 p-3 bg-white rounded-md mt-1"><?php echo !empty($modul['feedback']) ? htmlspecialchars($modul['feedback']) : '<i>Tidak ada feedback.</i>'; ?></p>
                                    </div>
                                </div>
                            <?php else: // Jika belum ada laporan ?>
                                <div x-data="{ submissionType: 'file' }">
                                    <!-- Tab Buttons -->
                                    <div class="mb-4 border-b border-slate-200">
                                        <nav class="flex -mb-px space-x-4">
                                            <button @click="submissionType = 'file'" :class="submissionType === 'file' ? 'border-blue-500 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'" class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm">
                                                Upload File
                                            </button>
                                            <button @click="submissionType = 'link'" :class="submissionType === 'link' ? 'border-blue-500 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'" class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm">
                                                Submit Link
                                            </button>
                                        </nav>
                                    </div>
                                    <!-- Form -->
                                    <form action="course_detail.php?id=<?php echo $id_praktikum; ?>" method="POST" enctype="multipart/form-data">
                                        <input type="hidden" name="id_modul" value="<?php echo $modul['id']; ?>">
                                        <input type="hidden" name="id_praktikum" value="<?php echo $id_praktikum; ?>">
                                        <input type="hidden" name="submission_type" x-model="submissionType">
                                        
                                        <!-- File Upload Tab -->
                                        <div x-show="submissionType === 'file'">
                                            <input type="file" name="file_laporan" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                        </div>
                                        <!-- Link Submit Tab -->
                                        <div x-show="submissionType === 'link'" style="display: none;">
                                            <input type="url" name="link_laporan" placeholder="https://docs.google.com/..." class="shadow appearance-none border rounded w-full py-2 px-3 text-slate-700 leading-tight focus:outline-none focus:shadow-outline">
                                        </div>
                                        
                                        <button type="submit" class="mt-4 w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg">
                                            Kumpulkan Laporan
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="text-center py-16 bg-white rounded-2xl shadow-md">
            <h3 class="text-2xl font-semibold text-slate-700">Modul Belum Tersedia</h3>
            <p class="text-slate-500 mt-2">Asisten belum menambahkan modul untuk mata praktikum ini.</p>
        </div>
    <?php endif; ?>
</div>

<script src="//unpkg.com/alpinejs" defer></script>

<?php
$stmt_modul->close();
$conn->close();
require_once 'templates/footer_mahasiswa.php';
?>