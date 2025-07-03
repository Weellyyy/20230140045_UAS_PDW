<?php
// Pastikan session sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek jika pengguna belum login atau bukan asisten
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'asisten') {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Asisten - <?php echo $pageTitle ?? 'Dashboard'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Menambahkan transisi halus untuk semua elemen */
        * {
            transition: all 0.2s ease-in-out;
        }
    </style>
</head>
<body class="bg-slate-100 font-sans">

<div class="flex h-screen">
    <!-- Sidebar -->
    <aside class="w-64 bg-slate-900 text-slate-300 flex flex-col">
        <!-- Logo/Header Sidebar -->
        <div class="flex items-center justify-center h-20 border-b border-slate-800">
            <svg class="w-8 h-8 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-1.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" />
            </svg>
            <h1 class="text-2xl font-bold text-white ml-3">WELLYY</h1>
        </div>

        <!-- Navigasi Utama -->
        <nav class="flex-grow p-4">
            <ul class="space-y-2">
                <?php 
                    // Kelas untuk link aktif: background lebih terang, teks putih, dan border biru di kiri
                    $activeClass = 'bg-slate-800 text-white border-l-4 border-blue-500';
                    // Kelas untuk link tidak aktif: transparan, teks abu-abu, dan hover effect
                    $inactiveClass = 'border-l-4 border-transparent hover:bg-slate-800 hover:text-white';
                ?>
                <li>
                    <a href="dashboard.php" class="<?php echo ($activePage == 'dashboard') ? $activeClass : $inactiveClass; ?> flex items-center px-4 py-3 rounded-r-lg">
                        <svg class="w-5 h-5 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" /></svg>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="praktikum.php" class="<?php echo ($activePage == 'praktikum') ? $activeClass : $inactiveClass; ?> flex items-center px-4 py-3 rounded-r-lg">
                        <svg class="w-5 h-5 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
                        <span>Praktikum</span>
                    </a>
                </li>
                <li>
                    <a href="modul.php" class="<?php echo ($activePage == 'modul') ? $activeClass : $inactiveClass; ?> flex items-center px-4 py-3 rounded-r-lg">
                        <svg class="w-5 h-5 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                        <span>Modul</span>
                    </a>
                </li>
                <li>
                    <a href="laporan.php" class="<?php echo ($activePage == 'laporan') ? $activeClass : $inactiveClass; ?> flex items-center px-4 py-3 rounded-r-lg">
                        <svg class="w-5 h-5 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75c0-.231-.035-.454-.1-.664M6.75 7.5h1.5M6.75 12h1.5m6.75 0h1.5m-1.5 3h1.5m-1.5 3h1.5M4.5 6.75h1.5v1.5H4.5v-1.5zM4.5 12h1.5v1.5H4.5v-1.5zM4.5 17.25h1.5v1.5H4.5v-1.5z" /></svg>
                        <span>Laporan Masuk</span>
                    </a>
                </li>
                <li>
                    <a href="pengguna.php" class="<?php echo ($activePage == 'pengguna') ? $activeClass : $inactiveClass; ?> flex items-center px-4 py-3 rounded-r-lg">
                        <svg class="w-5 h-5 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m-7.5-2.962A3.75 3.75 0 0112 15v-2.25A3.75 3.75 0 0115.75 9v-2.25A3.75 3.75 0 0112 3V.75m-6.506 5.668a3.75 3.75 0 01-2.25-2.25A3.75 3.75 0 015.25 3V.75m8.25 0a2.25 2.25 0 012.25 2.25m0 0a2.25 2.25 0 01-2.25 2.25m0 0a2.25 2.25 0 01-2.25-2.25m2.25-2.25a2.25 2.25 0 012.25-2.25M9 15.75A3.75 3.75 0 0112 12v-2.25A3.75 3.75 0 019 5.25v-2.25A3.75 3.75 0 015.25.75m0 0A2.25 2.25 0 013 3m0 0a2.25 2.25 0 01-2.25 2.25m0 0a2.25 2.25 0 012.25 2.25m-2.25 2.25a2.25 2.25 0 012.25-2.25m0 0a2.25 2.25 0 01-2.25-2.25m6.75-3.375a3.75 3.75 0 01-2.25-2.25m2.25 2.25a3.75 3.75 0 012.25 2.25m-11.25 3.542a3.75 3.75 0 012.25-2.25m2.25 2.25a3.75 3.75 0 01-2.25 2.25m-2.25-2.25a3.75 3.75 0 01-2.25-2.25m11.25 0a3.75 3.75 0 012.25 2.25m-2.25-2.25a3.75 3.75 0 012.25-2.25" /></svg>
                        <span>Kelola Mahasiswa</span>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- User Profile & Logout -->
        <div class="p-4 mt-auto border-t border-slate-800">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center font-bold text-white">
                    <?php echo strtoupper(substr($_SESSION['nama'], 0, 1)); ?>
                </div>
                <div class="ml-3">
                    <p class="font-semibold text-white text-sm"><?php echo htmlspecialchars($_SESSION['nama']); ?></p>
                    <p class="text-xs text-slate-400">Asisten</p>
                </div>
            </div>
            <a href="../logout.php" class="flex items-center justify-center bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg w-full">
                <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" /></svg>
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Top Header Bar -->
        <header class="bg-white shadow-md">
            <div class="flex items-center justify-between h-16 px-6 lg:px-8">
                <h1 class="text-2xl font-semibold text-slate-800"><?php echo $pageTitle ?? 'Halaman'; ?></h1>

            </div>
        </header>

        <!-- Scrollable Main Content -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-100 p-6 lg:p-8">
            <!-- Konten halaman akan dimulai di sini -->
