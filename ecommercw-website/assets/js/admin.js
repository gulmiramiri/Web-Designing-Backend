document.addEventListener('DOMContentLoaded', () => {
  const statsGrid = document.getElementById('statsGrid');
  const stockStatsGrid = document.getElementById('stockStatsGrid');
  const ticketStatsGrid = document.getElementById('ticketStatsGrid');
  if (statsGrid) loadStats();

  async function loadStats() {
    const data = await apiFetch(window.APP_BASE + 'api/stats.php');
    if (!data.success) {
      statsGrid.innerHTML = `<p class="col-span-full text-center text-gray-400">Unable to load statistics.</p>`;
      return;
    }
    const s = data.stats;

    if (statsGrid) {
      statsGrid.innerHTML = [
        { label: 'Total Users', value: s.total_users, color: 'from-blue-500 to-blue-600', icon: 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4zm6 0a4 4 0 10-4-4' },
        { label: 'Total Products', value: s.total_products, color: 'from-emerald-500 to-emerald-600', icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4' },
        { label: 'Total Categories', value: s.total_categories, color: 'from-purple-500 to-purple-600', icon: 'M4 6h16M4 12h16M4 18h7' },
      ].map((item) => `
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-6 flex items-center gap-4 transition-colors duration-300">
          <div class="w-12 h-12 rounded-xl bg-gradient-to-br ${item.color} flex items-center justify-center text-white shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="${item.icon}" /></svg>
          </div>
          <div>
            <p class="text-2xl font-bold">${item.value}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">${item.label}</p>
          </div>
        </div>`).join('');
    }

    if (stockStatsGrid) {
      stockStatsGrid.innerHTML = [
        { label: 'Total Products', value: s.total_products, color: 'from-emerald-500 to-emerald-600', icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4' },
        { label: 'Out of Stock', value: s.products_out_of_stock, color: 'from-red-500 to-red-600', icon: 'M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
        { label: 'Low Stock (≤5)', value: s.low_stock_products, color: 'from-orange-500 to-orange-600', icon: 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
      ].map((item) => `
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-6 flex items-center gap-4 transition-colors duration-300">
          <div class="w-12 h-12 rounded-xl bg-gradient-to-br ${item.color} flex items-center justify-center text-white shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="${item.icon}" /></svg>
          </div>
          <div>
            <p class="text-2xl font-bold">${item.value}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">${item.label}</p>
          </div>
        </div>`).join('');
    }

    if (ticketStatsGrid) {
      ticketStatsGrid.innerHTML = [
        { label: 'Total Tickets', value: s.total_tickets, color: 'from-blue-500 to-blue-600', icon: 'M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z' },
        { label: 'Open Tickets', value: s.open_tickets, color: 'from-yellow-500 to-yellow-600', icon: 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
        { label: 'Closed Tickets', value: s.closed_tickets, color: 'from-green-500 to-green-600', icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' },
      ].map((item) => `
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-6 flex items-center gap-4 transition-colors duration-300">
          <div class="w-12 h-12 rounded-xl bg-gradient-to-br ${item.color} flex items-center justify-center text-white shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="${item.icon}" /></svg>
          </div>
          <div>
            <p class="text-2xl font-bold">${item.value}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">${item.label}</p>
          </div>
        </div>`).join('');
    }
  }
});

/** Shared modal open/close helpers used by admin CRUD pages. */
function openModal(id) {
  document.getElementById(id).classList.remove('hidden');
}
function closeModal(id) {
  document.getElementById(id).classList.add('hidden');
}
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('[data-close-modal]').forEach((el) => {
    el.addEventListener('click', () => closeModal(el.dataset.closeModal));
  });
});
