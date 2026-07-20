<!-- Sidebar overlay (mobile) -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black/60 z-30 hidden opacity-0 lg:hidden" onclick="closeSidebar()"></div>

<!-- ============================================================ -->
<!-- SIDEBAR -->
<!-- ============================================================ -->
<aside id="sidebar" class="fixed top-0 left-0 h-full w-64 bg-gray-900 text-white z-40 flex flex-col -translate-x-full lg:translate-x-0">

    <!-- Brand -->
    <div class="px-5 py-5 border-b border-white/10 flex items-center gap-3">
        <div class="bg-brand rounded-xl p-2 shrink-0">
            <i class="ph-fill ph-package text-white text-xl"></i>
        </div>
        <div class="min-w-0">
            <p class="font-black text-sm leading-tight truncate">KWT Mawar Bodas II</p>
            <p class="text-xs text-gray-400 leading-tight">Dashboard Admin</p>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest px-3 mb-2">Menu Utama</p>

        <button onclick="showSection('overview')"
            class="nav-item active w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-gray-300 hover:bg-gray-800 hover:text-white transition-all text-left"
            data-section="overview">
            <i class="ph-bold ph-squares-four text-lg text-gray-400"></i>
            Ringkasan
        </button>

        <button onclick="showSection('tambah')"
            class="nav-item w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-gray-300 hover:bg-gray-800 hover:text-white transition-all text-left"
            data-section="tambah">
            <i class="ph-bold ph-plus-circle text-lg text-gray-400"></i>
            Kelola Produk
        </button>

        <button onclick="showSection('inventory')"
            class="nav-item w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-gray-300 hover:bg-gray-800 hover:text-white transition-all text-left"
            data-section="inventory">
            <i class="ph-bold ph-list-checks text-lg text-gray-400"></i>
            Data Inventory
        </button>

    </nav>

    <!-- Footer sidebar: Kembali ke Beranda + Logout -->
    <div class="px-3 py-4 border-t border-white/10 space-y-1">
        <a href="../index.php"
            class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-semibold text-gray-400 hover:bg-white/10 hover:text-white transition-all">
            <i class="ph-bold ph-arrow-left text-base"></i> Kembali ke Beranda
        </a>
        <a href="logout.php"
            class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-semibold text-red-400 hover:bg-red-500/10 hover:text-red-300 transition-all">
            <i class="ph-bold ph-sign-out text-base"></i> Logout
        </a>
    </div>
</aside>
