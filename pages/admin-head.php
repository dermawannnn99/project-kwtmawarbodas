<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <!-- [SEC-3] CSRF token untuk diakses JS via meta tag -->
    <meta name="csrf-token" content="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
    <title>Dashboard Admin — KWT Mawar Bodas II</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.cdnfonts.com/css/satoshi" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Satoshi', 'sans-serif'] },
                    colors: {
                        brand: { light: '#E4F0EE', DEFAULT: '#1E6472', dark: '#123F48', accent: '#D4A017' }
                    }
                }
            }
        }
    </script>
    <style>
        #sidebar { transition: transform 0.25s ease; }
        #sidebar-overlay { transition: opacity 0.25s ease; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #1E6472; border-radius: 3px; }
        .section-content { display: none; }
        .section-content.active { display: block; box-sizing: border-box; }
        .nav-item.active { background: #374151; color: #D4A017; }
        .nav-item.active i { color: #D4A017; }
        @keyframes notif-in {
            from { opacity: 0; transform: scale(0.88); }
            to   { opacity: 1; transform: scale(1); }
        }
        @keyframes notif-out {
            from { opacity: 1; transform: scale(1); }
            to   { opacity: 0; transform: scale(0.88); }
        }
        .notif-backdrop { transition: opacity 0.25s ease; }
        .notif-card-enter { animation: notif-in 0.25s ease forwards; }
        .notif-card-exit  { animation: notif-out 0.22s ease forwards; }
    </style>
</head>
<body class="bg-slate-100 text-gray-800 font-sans antialiased">
