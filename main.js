// ========== MAIN.JS ==========
document.addEventListener('DOMContentLoaded', async () => {

  // === AOS Init ===
  if (typeof AOS !== 'undefined') {
    AOS.init({ duration: 700, once: true, offset: 60 });
  }

  // === Navbar Scroll ===
  const navbar = document.getElementById('navbar');
  const backToTop = document.getElementById('backToTop');
  window.addEventListener('scroll', () => {
    if (window.scrollY > 50) {
      navbar && navbar.classList.add('scrolled');
      backToTop && backToTop.classList.add('show');
    } else {
      navbar && navbar.classList.remove('scrolled');
      backToTop && backToTop.classList.remove('show');
    }
  });

  // === Hamburger ===
  const hamburger = document.getElementById('hamburger');
  const mobileMenu = document.getElementById('mobileMenu');
  if (hamburger && mobileMenu) {
    hamburger.addEventListener('click', () => {
      mobileMenu.classList.toggle('active');
      hamburger.classList.toggle('active');
      document.body.style.overflow = mobileMenu.classList.contains('active') ? 'hidden' : '';
    });
    mobileMenu.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', () => {
        mobileMenu.classList.remove('active');
        hamburger.classList.remove('active');
        document.body.style.overflow = '';
      });
    });
  }

  // === LOAD PRODUCTS & CATEGORIES FROM API ===
  if (typeof loadProducts === 'function') {
    await loadProducts();
  }
  if (typeof loadCategories === 'function') {
    await loadCategories();
  }
  if (typeof loadBrands === 'function') {
    await loadBrands();
  }

  // === HOME PAGE: Populate categories grid dynamically ===
  const categoriesGrid = document.getElementById('categoriesGrid');
  if (categoriesGrid && CATEGORIES.length > 0) {
    categoriesGrid.innerHTML = CATEGORIES.map((cat, i) => createCategoryCard(cat, i * 100)).join('');
    if (typeof AOS !== 'undefined') AOS.refresh();
  }

  // === PRODUK PAGE: Populate category filter checkboxes dynamically ===
  const categoryFilters = document.getElementById('categoryFilters');
  if (categoryFilters && CATEGORIES.length > 0) {
    let html = '<h4>Kategori</h4>';
    CATEGORIES.forEach(cat => {
      html += `<label><input type="checkbox" class="filter-checkbox filter-category" value="${cat.name}"> ${cat.name}</label>`;
    });
    categoryFilters.innerHTML = html;

    // Re-bind filter events
    categoryFilters.querySelectorAll('.filter-checkbox').forEach(cb => {
      cb.addEventListener('change', () => {
        if (typeof renderCatalog === 'function') renderCatalog();
      });
    });
  }

  // === PRODUK PAGE: Populate brand filter checkboxes dynamically ===
  const brandFilters = document.getElementById('brandFilters');
  if (brandFilters && BRANDS.length > 0) {
    let html = '<h4>Brand</h4>';
    BRANDS.forEach(br => {
      html += `<label><input type="checkbox" class="filter-checkbox filter-brand" value="${br.name}"> ${br.name}</label>`;
    });
    brandFilters.innerHTML = html;

    // Re-bind filter events
    brandFilters.querySelectorAll('.filter-checkbox').forEach(cb => {
      cb.addEventListener('change', () => {
        if (typeof renderCatalog === 'function') renderCatalog();
      });
    });
  }

  // === HOME PAGE: Populate product grids ===
  const bestSellerGrid = document.getElementById('bestSellerGrid');
  const newProductGrid = document.getElementById('newProductGrid');

  if (bestSellerGrid && PRODUCTS.length > 0) {
    const bestSellers = PRODUCTS.filter(p => p.isBestSeller);
    bestSellerGrid.innerHTML = bestSellers.map((p, i) => createProductCard(p, i * 80)).join('');
  }

  if (newProductGrid && PRODUCTS.length > 0) {
    const newProducts = PRODUCTS.filter(p => p.isNew);
    newProductGrid.innerHTML = newProducts.map((p, i) => createProductCard(p, i * 80)).join('');
  }

  // === PRODUK PAGE: Catalog ===
  const catalogGrid = document.getElementById('catalogGrid');
  const searchInput = document.getElementById('searchInput');
  const sortSelect = document.getElementById('sortSelect');
  const filterCheckboxes = document.querySelectorAll('.filter-checkbox');
  const productCount = document.getElementById('productCount');
  const filterToggle = document.getElementById('filterToggle');
  const catalogSidebar = document.getElementById('catalogSidebar');

  // Mobile filter toggle
  if (filterToggle && catalogSidebar) {
    filterToggle.addEventListener('click', () => {
      catalogSidebar.classList.toggle('mobile-open');
    });
  }

  function renderCatalog() {
    if (!catalogGrid || PRODUCTS.length === 0) return;

    let filtered = [...PRODUCTS];

    // Search
    if (searchInput && searchInput.value.trim()) {
      const q = searchInput.value.toLowerCase().trim();
      filtered = filtered.filter(p =>
        p.name.toLowerCase().includes(q) ||
        p.category.toLowerCase().includes(q) ||
        p.brand.toLowerCase().includes(q) ||
        p.desc.toLowerCase().includes(q)
      );
    }

    // Category filter
    const checkedCats = [...document.querySelectorAll('.filter-category:checked')].map(c => c.value);
    if (checkedCats.length > 0) {
      filtered = filtered.filter(p => checkedCats.includes(p.category));
    }

    // Brand filter
    const checkedBrands = [...document.querySelectorAll('.filter-brand:checked')].map(c => c.value);
    if (checkedBrands.length > 0) {
      filtered = filtered.filter(p => checkedBrands.includes(p.brand));
    }

    // Sort
    if (sortSelect) {
      const sortVal = sortSelect.value;
      if (sortVal === 'newest') {
        filtered.sort((a, b) => (b.isNew ? 1 : 0) - (a.isNew ? 1 : 0));
      } else if (sortVal === 'name-asc') {
        filtered.sort((a, b) => a.name.localeCompare(b.name));
      } else if (sortVal === 'name-desc') {
        filtered.sort((a, b) => b.name.localeCompare(a.name));
      }
    }

    // Render
    if (filtered.length === 0) {
      catalogGrid.innerHTML = `<div style="grid-column:1/-1;text-align:center;padding:60px 20px">
        <i class="bi bi-search" style="font-size:3rem;color:var(--dark-300)"></i>
        <h3 style="margin-top:16px;color:var(--dark-500)">Produk tidak ditemukan</h3>
        <p style="color:var(--dark-400)">Coba kata kunci lain atau hapus filter</p>
      </div>`;
    } else {
      catalogGrid.innerHTML = filtered.map((p, i) => createProductCard(p, i * 50)).join('');
    }

    // Update count
    if (productCount) {
      productCount.textContent = `Menampilkan ${filtered.length} produk`;
    }

    // Re-init AOS
    if (typeof AOS !== 'undefined') AOS.refresh();
  }

  if (catalogGrid) {
    renderCatalog();
    if (searchInput) {
      searchInput.addEventListener('input', renderCatalog);
    }
    if (sortSelect) {
      sortSelect.addEventListener('change', renderCatalog);
    }
    filterCheckboxes.forEach(cb => {
      cb.addEventListener('change', renderCatalog);
    });
  }

  // === CONTACT PAGE: Form ===
  const contactForm = document.getElementById('contactForm');
  if (contactForm) {
    contactForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const name = document.getElementById('formName').value.trim();
      const wa = document.getElementById('formWa').value.trim();
      const email = document.getElementById('formEmail').value.trim();
      const msg = document.getElementById('formMessage').value.trim();

      if (!name || !wa || !msg) {
        showToast('Mohon lengkapi field yang wajib diisi.', 'warning');
        return;
      }

      // Build WA message
      let waMsg = `Halo Nada Agen Sosis 👋\n\n`;
      waMsg += `Nama: ${name}\n`;
      waMsg += `No WA: ${wa}\n`;
      waMsg += `Email: ${email || '-'}\n`;
      waMsg += `\nPesan:\n${msg}\n\n`;
      waMsg += `Dikirim dari halaman Kontak Website`;

      const waUrl = `https://wa.me/62877333999?text=${encodeURIComponent(waMsg)}`;
      window.open(waUrl, '_blank');

      showToast('Pesan Anda sedang diarahkan ke WhatsApp!', 'success');
      contactForm.reset();
    });
  }

  // === Toast notification ===
  window.showToast = function(message, type = 'success') {
    const existing = document.querySelector('.toast-notification');
    if (existing) existing.remove();

    const toast = document.createElement('div');
    toast.className = 'toast-notification';
    toast.style.cssText = `
      position:fixed; bottom:30px; left:50%; transform:translateX(-50%);
      background:${type === 'success' ? '#10B981' : type === 'warning' ? '#F59E0B' : '#EF4444'};
      color:white; padding:14px 28px; border-radius:12px; font-size:.92rem; font-weight:600;
      box-shadow:0 8px 30px rgba(0,0,0,.15); z-index:9999;
      animation:slideUpToast .4s ease;
    `;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => {
      toast.style.opacity = '0';
      toast.style.transition = 'opacity .3s';
      setTimeout(() => toast.remove(), 300);
    }, 3000);
  };

  // Add toast animation
  const style = document.createElement('style');
  style.textContent = `
    @keyframes slideUpToast {
      from { opacity:0; transform:translateX(-50%) translateY(20px); }
      to { opacity:1; transform:translateX(-50%) translateY(0); }
    }
    .catalog-sidebar.mobile-open { display:block !important; }
    @media(max-width:1024px) {
      .filter-toggle-btn { display:inline-flex !important; }
    }
  `;
  document.head.appendChild(style);

  // Handle hash for category scroll
  if (window.location.hash && catalogGrid) {
    const hash = window.location.hash.replace('#', '').toLowerCase();
    // Build category map dynamically from loaded CATEGORIES data
    const categoryMap = {};
    if (CATEGORIES && CATEGORIES.length > 0) {
      CATEGORIES.forEach(cat => {
        categoryMap[cat.slug.toLowerCase()] = cat.name;
      });
    }
    if (categoryMap[hash]) {
      const checkbox = document.querySelector(`.filter-category[value="${categoryMap[hash]}"]`);
      if (checkbox) {
        checkbox.checked = true;
        renderCatalog();
      }
    }
  }

});
