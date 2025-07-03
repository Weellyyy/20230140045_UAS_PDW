<?php
// Pastikan session sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek jika pengguna belum login atau bukan asisten
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'asisten') {
    // Arahkan ke halaman login di root folder
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Panel Asisten - <?php echo $pageTitle ?? 'Dashboard'; ?></title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
</head>
<body class="bg-gray-100">

<div class="flex h-screen bg-gray-200 font-sans">
    <!-- Sidebar -->
    <aside class="w-64 bg-gray-800 text-white flex flex-col min-h-screen">
        <div class="p-6 text-center border-b border-gray-700">
            <h3 class="text-xl font-bold">SIMPRAK</h3>
            <p class="text-sm text-gray-400 mt-1">Panel Asisten</p>
        </div>
        <nav class="flex-grow p-4">
            <ul class="space-y-2">
                <?php 
                    // Menyiapkan class untuk link aktif dan tidak aktif
                    $activeClass = 'bg-gray-900 text-white';
                    $inactiveClass = 'text-gray-300 hover:bg-gray-700 hover:text-white';
                ?>
                <li>
                    <a href="dashboard.php" class="<?php echo ($activePage == 'dashboard') ? $activeClass : $inactiveClass; ?> flex items-center px-4 py-3 rounded-md transition-colors duration-200">
                        <!-- Icon Dashboard -->
                        <svg class="w-5 h-5 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" /></svg>
                        <span>Dashboard</span>
                    </a>
                </li>
                <!-- ====== NAVIGASI BARU DITAMBAHKAN DI SINI ====== -->
                <li>
                    <a href="praktikum.php" class="<?php echo ($activePage == 'praktikum') ? $activeClass : $inactiveClass; ?> flex items-center px-4 py-3 rounded-md transition-colors duration-200">
                        <!-- Icon Buku -->
                        <svg class="w-5 h-5 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
                        <span>Manajemen Praktikum</span>
                    </a>
                </li>
                 <!-- ====== AKHIR DARI NAVIGASI BARU ====== -->
            </ul>
        </nav>
        <div class="p-4 mt-auto">
             <a href="../logout.php" class="flex items-center justify-center bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-300 w-full">
                <!-- Icon Logout -->
                <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" /></svg>
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-6 lg:p-10 overflow-auto">
        <header class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-800"><?php echo $pageTitle ?? 'Halaman'; ?></h1>
             <div class="flex items-center">
                <span class="text-gray-600 mr-2">Selamat datang,</span>
                <span class="font-semibold text-gray-800"><?php echo htmlspecialchars($_SESSION['nama']); ?></span>
            </div>
        </header>
