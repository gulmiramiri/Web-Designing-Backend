<?php
require_once __DIR__ . '/functions.php';
$currentUser = $_SESSION['full_name'] ?? null;
$currentRole = $_SESSION['role'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo isset($pageTitle) ? e($pageTitle) . ' — ShopEase' : 'ShopEase'; ?></title>

<!-- Prevent flash of wrong theme: must run before Tailwind CDN paints -->
<script>
  (function () {
    const stored = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const theme = stored || (prefersDark ? 'dark' : 'light');
    if (theme === 'dark') document.documentElement.classList.add('dark');
  })();
</script>

<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = {
    darkMode: 'class',
    theme: {
      extend: {
        colors: {
          primary: {
            50: '#eff6ff', 100: '#dbeafe', 200: '#bfdbfe', 300: '#93c5fd',
            400: '#60a5fa', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8',
            800: '#1e40af', 900: '#1e3a8a',
          },
        },
      },
    },
  };
</script>
<link rel="stylesheet" href="<?php echo isset($isAdmin) ? '../assets/css/style.css' : 'assets/css/style.css'; ?>">
<script>
  window.APP_BASE = <?php echo isset($isAdmin) ? "'../'" : "''"; ?>;
  window.CURRENT_USER_ID = <?php echo isLoggedIn() ? (int)$_SESSION['user_id'] : 'null'; ?>;
</script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100 transition-colors duration-300 min-h-screen flex flex-col">

<nav class="bg-white dark:bg-gray-800 shadow-sm sticky top-0 z-40 transition-colors duration-300">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between h-16 gap-4">

      <a href="<?php echo isset($isAdmin) ? '../index.php' : 'index.php'; ?>" class="flex items-center gap-2 font-bold text-xl text-primary-600 dark:text-primary-400 shrink-0">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
        ShopEase
      </a>

      <div class="hidden md:flex flex-1 max-w-xl mx-4">
        <form id="navSearchForm" class="w-full relative" action="<?php echo isset($isAdmin) ? '../index.php' : 'index.php'; ?>" method="get">
          <input type="text" name="search" id="navSearchInput" placeholder="Search products..."
                 class="w-full rounded-full border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 py-2 pl-4 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 transition-colors duration-300">
          <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-primary-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </button>
        </form>
      </div>

      <div class="flex items-center gap-1 shrink-0">
        <?php if (isLoggedIn()): ?>
        <!-- Cart Button -->
        <button data-toggle-cart aria-label="Shopping cart"
                class="relative p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-300">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
          <span class="cart-count-badge hidden absolute -top-0.5 -right-0.5 bg-red-500 text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center">0</span>
        </button>

        <!-- Notifications Bell -->
        <button id="notifBellBtn" aria-label="Notifications"
                class="relative p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-300">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
          </svg>
          <span id="notifBadge" class="hidden absolute -top-0.5 -right-0.5 bg-red-500 text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center">0</span>
        </button>
        <?php endif; ?>

        <button id="themeToggle" aria-label="Toggle dark mode"
                class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-300">
          <svg id="iconSun" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden dark:block text-yellow-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="4" />
            <path stroke-linecap="round" d="M12 2v2m0 16v2M4.93 4.93l1.41 1.41m11.32 11.32l1.41 1.41M2 12h2m16 0h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41" />
          </svg>
          <svg id="iconMoon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 block dark:hidden text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" />
          </svg>
        </button>

        <div class="hidden md:flex items-center gap-2">
          <?php if ($currentUser): ?>
            <div class="relative" id="userMenuWrapper">
              <button id="userMenuBtn" class="flex items-center gap-2 px-3 py-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-300 text-sm font-medium">
                <?php echo e($currentUser); ?>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
              </button>
              <div id="userMenu" class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden z-50 transition-colors duration-300">
                <a href="<?php echo isset($isAdmin) ? '../dashboard.php' : 'dashboard.php'; ?>" class="block px-4 py-2 text-sm hover:bg-gray-50 dark:hover:bg-gray-700">Dashboard</a>
                <a href="<?php echo isset($isAdmin) ? '../cart.php' : 'cart.php'; ?>" class="block px-4 py-2 text-sm hover:bg-gray-50 dark:hover:bg-gray-700">Shopping Cart</a>
                <a href="<?php echo isset($isAdmin) ? '../tickets.php' : 'tickets.php'; ?>" class="block px-4 py-2 text-sm hover:bg-gray-50 dark:hover:bg-gray-700">Support Tickets</a>
                <?php if ($currentRole === 'admin'): ?>
                  <a href="<?php echo isset($isAdmin) ? 'index.php' : 'admin/index.php'; ?>" class="block px-4 py-2 text-sm hover:bg-gray-50 dark:hover:bg-gray-700">Admin Panel</a>
                <?php endif; ?>
                <button id="logoutBtn" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-50 dark:hover:bg-gray-700">Logout</button>
              </div>
            </div>
          <?php else: ?>
            <a href="<?php echo isset($isAdmin) ? '../login.php' : 'login.php'; ?>" class="px-4 py-2 text-sm font-medium rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-300">Login</a>
            <a href="<?php echo isset($isAdmin) ? '../register.php' : 'register.php'; ?>" class="px-4 py-2 text-sm font-medium rounded-full bg-primary-600 text-white hover:bg-primary-700 transition-colors duration-300">Register</a>
          <?php endif; ?>
        </div>

        <button id="mobileMenuBtn" class="md:hidden p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-300">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" /></svg>
        </button>
      </div>
    </div>

    <div id="mobileMenu" class="hidden md:hidden pb-4 space-y-2">
      <form action="<?php echo isset($isAdmin) ? '../index.php' : 'index.php'; ?>" method="get" class="relative">
        <input type="text" name="search" placeholder="Search products..." class="w-full rounded-full border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 py-2 pl-4 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
      </form>
      <?php if ($currentUser): ?>
        <a href="<?php echo isset($isAdmin) ? '../dashboard.php' : 'dashboard.php'; ?>" class="block px-3 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 text-sm">Dashboard</a>
        <a href="<?php echo isset($isAdmin) ? '../cart.php' : 'cart.php'; ?>" class="block px-3 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 text-sm">Shopping Cart</a>
        <a href="<?php echo isset($isAdmin) ? '../tickets.php' : 'tickets.php'; ?>" class="block px-3 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 text-sm">Support Tickets</a>
        <?php if ($currentRole === 'admin'): ?>
          <a href="<?php echo isset($isAdmin) ? 'index.php' : 'admin/index.php'; ?>" class="block px-3 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 text-sm">Admin Panel</a>
        <?php endif; ?>
        <button id="logoutBtnMobile" class="w-full text-left px-3 py-2 rounded-md text-red-600 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm">Logout</button>
      <?php else: ?>
        <a href="<?php echo isset($isAdmin) ? '../login.php' : 'login.php'; ?>" class="block px-3 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 text-sm">Login</a>
        <a href="<?php echo isset($isAdmin) ? '../register.php' : 'register.php'; ?>" class="block px-3 py-2 rounded-md bg-primary-600 text-white text-sm">Register</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<!-- Cart Sidebar Overlay -->
<div id="cartOverlay" class="hidden fixed inset-0 bg-black/40 z-40 transition-opacity"></div>

<!-- Cart Sidebar -->
<div id="cartSidebar" class="fixed top-0 right-0 h-full w-full max-w-md bg-white dark:bg-gray-800 shadow-xl z-50 transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col">
  <div class="flex items-center justify-between p-4 border-b border-gray-100 dark:border-gray-700">
    <h2 class="text-lg font-bold flex items-center gap-2">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
      </svg>
      Cart (<span id="cartSidebarCount">0</span>)
    </h2>
    <button data-toggle-cart class="p-1 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
    </button>
  </div>

  <div id="cartSidebarItems" class="flex-1 overflow-y-auto p-4 space-y-3">
    <div class="flex flex-col items-center justify-center py-12 text-gray-400">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
      </svg>
      <p class="text-sm font-medium">Your cart is empty</p>
    </div>
  </div>

  <div class="border-t border-gray-100 dark:border-gray-700 p-4 space-y-3">
    <div class="flex items-center justify-between">
      <span class="text-sm text-gray-500 dark:text-gray-400">Total</span>
      <span id="cartSidebarTotal" class="text-lg font-bold text-primary-600 dark:text-primary-400">$0.00</span>
    </div>
    <a href="<?php echo isset($isAdmin) ? '../cart.php' : 'cart.php'; ?>" class="block w-full text-center bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition-colors">View Cart</a>
  </div>
</div>

<!-- Notifications Panel -->
<div id="notifPanel" class="hidden fixed top-16 right-4 w-80 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 z-50 max-h-96 overflow-y-auto transition-colors duration-300">
  <div class="p-3 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
    <h3 class="font-bold text-sm">Notifications</h3>
    <button id="markNotifRead" class="text-xs text-primary-600 hover:underline">Mark all as read</button>
  </div>
  <div id="notifList" class="divide-y divide-gray-100 dark:divide-gray-700">
    <div class="p-4 text-center text-sm text-gray-400">No notifications.</div>
  </div>
</div>

<div id="toastContainer" class="fixed top-20 right-4 z-50 space-y-2 w-72"></div>

<main class="flex-1">
