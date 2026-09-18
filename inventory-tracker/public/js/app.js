document.addEventListener('DOMContentLoaded', () => {
    // Sidebar Mobile Toggle
    const toggleBtn = document.getElementById('toggleSidebarBtn');
    const sidebar = document.getElementById('sidebar');
    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
            sidebar.classList.toggle('absolute');
        });
    }

    // Live Global Search Bar with Debounce
    const searchInput = document.getElementById('globalSearchInput');
    const resultsBox = document.getElementById('searchResultsDropdown');
    let searchTimer = null;

    if (searchInput && resultsBox) {
        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimer);
            const val = e.target.value.trim();
            if (val.length < 2) {
                resultsBox.classList.add('hidden');
                resultsBox.innerHTML = '';
                return;
            }

            searchTimer = setTimeout(async () => {
                try {
                    const res = await fetch('/api/products/search?q=' + encodeURIComponent(val));
                    const products = await res.json();

                    if (!products || !products.length) {
                        resultsBox.innerHTML = '<div class="p-3 text-xs text-slate-400 text-center">No products matching "' + val + '"</div>';
                        resultsBox.classList.remove('hidden');
                        return;
                    }

                    let html = '';
                    products.forEach(p => {
                        html += `
                            <a href="/products/${p.id}" class="flex items-center justify-between p-3 hover:bg-slate-50 transition-colors block">
                                <div>
                                    <h5 class="text-xs font-bold text-slate-900">${p.name}</h5>
                                    <span class="text-[10px] text-slate-400 font-mono">SKU: ${p.sku} | Barcode: ${p.barcode || 'N/A'}</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs font-bold text-sky-600">${p.total_stock} ${p.unit}</span>
                                </div>
                            </a>
                        `;
                    });

                    resultsBox.innerHTML = html;
                    resultsBox.classList.remove('hidden');
                } catch(err) {
                    console.error('Search error:', err);
                }
            }, 250);
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!searchInput.contains(e.target) && !resultsBox.contains(e.target)) {
                resultsBox.classList.add('hidden');
            }
        });
    }
});
