<?php
require_once '../config.php';
$pageTitle = 'Form Pengguna';
$activePage = 'pengguna';
require_once 'templates/header.php';

// Inisialisasi variabel
$id = '';
$nama = '';
$email = '';
$role = 'mahasiswa'; // Default role
$form_title = 'Tambah Pengguna Baru';

// Logika untuk menangani form submission (CREATE/UPDATE)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $password = $_POST['password'];

    // Validasi dasar
    if (empty($nama) || empty($email) || empty($role)) {
        $_SESSION['message'] = "Nama, email, dan peran tidak boleh kosong.";
        $_SESSION['message_type'] = 'error';
    } else {
        // Jika ada ID, lakukan UPDATE
        if (!empty($id)) {
            // Jika password diisi, update password. Jika tidak, jangan ubah password.
            if (!empty($password)) {
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                $sql = "UPDATE users SET nama = ?, email = ?, role = ?, password = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssi", $nama, $email, $role, $hashed_password, $id);
            } else {
                $sql = "UPDATE users SET nama = ?, email = ?, role = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssi", $nama, $email, $role, $id);
            }
            $action = 'diperbarui';
        } 
        // Jika tidak ada ID, lakukan INSERT
        else {
            if (empty($password)) {
                 $_SESSION['message'] = "Password wajib diisi untuk pengguna baru.";
                 $_SESSION['message_type'] = 'error';
            } else {
                // Cek duplikasi email
                $sql_check = "SELECT id FROM users WHERE email = ?";
                $stmt_check = $conn->prepare($sql_check);
                $stmt_check->bind_param("s", $email);
                $stmt_check->execute();
                $stmt_check->store_result();

                if ($stmt_check->num_rows > 0) {
                    $_SESSION['message'] = "Email sudah terdaftar. Gunakan email lain.";
                    $_SESSION['message_type'] = 'error';
                } else {
                    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                    $sql = "INSERT INTO users (nama, email, role, password) VALUES (?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssss", $nama, $email, $role, $hashed_password);
                    $action = 'ditambahkan';
                }
                $stmt_check->close();
            }
        }
        
        // Eksekusi query jika statement sudah disiapkan
        if (isset($stmt)) {
            if ($stmt->execute()) {
                $_SESSION['message'] = "Pengguna berhasil $action.";
                $_SESSION['message_type'] = 'success';
            } else {
                $_SESSION['message'] = "Gagal memproses data pengguna.";
                $_SESSION['message_type'] = 'error';
            }
            $stmt->close();
            header("Location: pengguna.php");
            exit();
        }
    }
}

// Cek jika ini adalah mode edit untuk mengisi form
if (isset($_GET['edit_id'])) {
    $id_to_edit = $_GET['edit_id'];
    $sql = "SELECT id, nama, email, role FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_to_edit);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $id = $row['id'];
        $nama = $row['nama'];
        $email = $row['email'];
        $role = $row['role'];
        $form_title = 'Edit Pengguna';
    }
    $stmt->close();
}
?>

<div class="bg-white p-8 rounded-lg shadow-lg max-w-2xl mx-auto">
    <h2 class="text-2xl font-bold text-gray-800 mb-6"><?php echo $form_title; ?></h2>
    
    <form action="pengguna_form.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        
        <div class="mb-4">
            <label for="nama" class="block text-gray-700 text-sm font-bold mb-2">Nama Lengkap</label>
            <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($nama); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required>
        </div>
        
        <div class="mb-4">
            <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required>
        </div>
        
        <div class="mb-4">
            <label for="role" class="block text-gray-700 text-sm font-bold mb-2">Peran</label>
            <select id="role" name="role" class="shadow border rounded w-full py-2 px-3 text-gray-700" required>
                <option value="mahasiswa" <?php echo ($role == 'mahasiswa') ? 'selected' : ''; ?>>Mahasiswa</option>
                <option value="asisten" <?php echo ($role == 'asisten') ? 'selected' : ''; ?>>Asisten</option>
            </select>
        </div>

        <div class="mb-6">
            <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
            <input type="password" id="password" name="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
            <p class="text-xs text-gray-600 mt-1"><?php echo !empty($id) ? 'Kosongkan jika tidak ingin mengubah password.' : 'Wajib diisi untuk pengguna baru.'; ?></p>
        </div>
        
        <div class="flex items-center justify-end">
            <a href="pengguna.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg mr-2">
                Batal
            </a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                Simpan Pengguna
            </button>
        </div>
    </form>
</div>

<?php
$conn->close();
require_once 'templates/footer.php';
?>
