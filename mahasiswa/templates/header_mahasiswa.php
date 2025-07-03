<?php
// Pastikan session sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek jika pengguna belum login. Jika belum, arahkan ke halaman login.
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Mahasiswa - <?php echo $pageTitle ?? 'SIMPRAK'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Menambahkan transisi halus untuk semua elemen */
        * {
            transition: all 0.2s ease-in-out;
        }
    </style>
</head>
<body class="bg-slate-50 font-sans">

    <!-- Navbar Utama -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="dashboard.php" class="flex-shrink-0 flex items-center">
                        <svg class="w-8 h-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-1.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" />
                        </svg>
                        <span class="text-2xl font-bold text-slate-800 ml-2">SIMPRAK</span>
                    </a>
                </div>

                <!-- Menu Navigasi (Desktop) -->
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <?php 
                            $activeClass = 'bg-blue-600 text-white';
                            $inactiveClass = 'text-slate-500 hover:bg-blue-600 hover:text-white';
                        ?>
                        <a href="dashboard.php" class="<?php echo ($activePage == 'dashboard') ? $activeClass : $inactiveClass; ?> px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
                        <a href="my_courses.php" class="<?php echo ($activePage == 'my_courses') ? $activeClass : $inactiveClass; ?> px-3 py-2 rounded-md text-sm font-medium">Praktikum Saya</a>
                        <a href="courses.php" class="<?php echo ($activePage == 'courses') ? $activeClass : $inactiveClass; ?> px-3 py-2 rounded-md text-sm font-medium">Cari Praktikum</a>
                    </div>
                </div>

                <!-- Profil Pengguna & Logout -->
                <div class="hidden md:block">
                    <div class="ml-4 flex items-center md:ml-6">
                        <span class="text-slate-600 mr-3">
                            Halo, <strong class="font-semibold"><?php echo htmlspecialchars($_SESSION['nama']); ?></strong>
                        </span>
                        <a href="../logout.php" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-300 text-sm">
                            Logout
                        </a>
                    </div>
                </div>

                <!-- Tombol Hamburger (Mobile) -->
                <div class="-mr-2 flex md:hidden">
                    <button id="mobile-menu-button" type="button" class="bg-slate-100 inline-flex items-center justify-center p-2 rounded-md text-slate-600 hover:text-white hover:bg-blue-600 focus:outline-none">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Menu Mobile -->
        <div class="md:hidden hidden" id="mobile-menu">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <a href="dashboard.php" class="text-slate-600 hover:bg-blue-600 hover:text-white block px-3 py-2 rounded-md text-base font-medium">Dashboard</a>
                <a href="my_courses.php" class="text-slate-600 hover:bg-blue-600 hover:text-white block px-3 py-2 rounded-md text-base font-medium">Praktikum Saya</a>
                <a href="courses.php" class="text-slate-600 hover:bg-blue-600 hover:text-white block px-3 py-2 rounded-md text-base font-medium">Cari Praktikum</a>
            </div>
            <div class="pt-4 pb-3 border-t border-slate-200">
                <div class="flex items-center px-5">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center font-bold text-white">
                        <?php echo strtoupper(substr($_SESSION['nama'], 0, 1)); ?>
                    </div>
                    <div class="ml-3">
                        <div class="text-base font-medium leading-none text-slate-800"><?php echo htmlspecialchars($_SESSION['nama']); ?></div>
                        <div class="text-sm font-medium leading-none text-slate-500 mt-1"><?php echo htmlspecialchars($_SESSION['role']); ?></div>
                    </div>
                </div>
                <div class="mt-3 px-2 space-y-1">
                    <a href="../logout.php" class="block rounded-md px-3 py-2 text-base font-medium text-slate-600 hover:bg-red-600 hover:text-white">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Wrapper Konten Utama -->
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <!-- Konten halaman akan dimulai di sini -->
