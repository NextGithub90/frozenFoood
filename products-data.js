// ========== PRODUCTS DATABASE ==========
// Loads from PHP API (MySQL) with static fallback

let PRODUCTS = [];
let CATEGORIES = [];
let BRANDS = [];
let productsLoaded = false;
let categoriesLoaded = false;
let brandsLoaded = false;

// Static fallback categories
const CATEGORIES_FALLBACK = [
  { id: 1, name: "Sosis", slug: "sosis", icon: "🌭", description: "Beragam varian sosis premium" },
  { id: 2, name: "Nugget", slug: "nugget", icon: "🍗", description: "Nugget ayam renyah & lezat" },
  { id: 3, name: "Bakso", slug: "bakso", icon: "🥩", description: "Bakso daging segar & kenyal" },
  { id: 4, name: "Frozen Lainnya", slug: "lainnya", icon: "❄️", description: "Lumpia, dimsum, & lainnya" }
];

// Static fallback brands
const BRANDS_FALLBACK = [
  { id: 1, name: "Kanzler", description: "Brand sosis premium asal Jerman" },
  { id: 2, name: "Fiesta", description: "Produk frozen food populer Indonesia" },
  { id: 3, name: "So Nice", description: "Sosis dan olahan ayam praktis" },
  { id: 4, name: "Champ", description: "Frozen food untuk anak-anak" },
  { id: 5, name: "Bernardi", description: "Produk daging olahan premium" },
  { id: 6, name: "Cedea", description: "Produk seafood frozen berkualitas" },
  { id: 7, name: "So Good", description: "Olahan ayam dan daging terpercaya" },
  { id: 8, name: "Aviko", description: "Kentang goreng impor premium" }
];

// Static fallback data (original products)
const PRODUCTS_FALLBACK = [
  { id: 1, name: "Sosis Kanzler Bratwurst 500g", category: "Sosis", brand: "Kanzler", desc: "Sosis bratwurst premium dari Kanzler dengan cita rasa autentik Jerman, cocok untuk BBQ dan masakan sehari-hari.", weight: "500g", storage: "Disimpan beku (-18°C)", halal: true, badge: "Laris", img: "img/product-sosis.png", isNew: false, isBestSeller: true, price: "Rp 65.000" },
  { id: 2, name: "Sosis Fiesta 1kg", category: "Sosis", brand: "Fiesta", desc: "Sosis ayam Fiesta ukuran ekonomis, ideal untuk keluarga dan usaha kuliner.", weight: "1kg (isi ±15 pcs)", storage: "Disimpan beku (-18°C)", halal: true, badge: "Laris", img: "img/product-sosis.png", isNew: false, isBestSeller: true, price: "Rp 55.000" },
  { id: 3, name: "Sosis So Nice 700g", category: "Sosis", brand: "So Nice", desc: "Sosis ayam So Nice yang digemari anak-anak, praktis dimasak dan bergizi.", weight: "700g", storage: "Disimpan beku (-18°C)", halal: true, badge: "", img: "img/product-sosis.png", isNew: true, isBestSeller: false, price: "Rp 42.000" },
  { id: 4, name: "Sosis Champ 500g", category: "Sosis", brand: "Champ", desc: "Sosis ayam Champ rasa original, pas untuk camilan dan bekal sekolah.", weight: "500g", storage: "Disimpan beku (-18°C)", halal: true, badge: "", img: "img/product-sosis.png", isNew: false, isBestSeller: false, price: "Rp 38.000" },
  { id: 5, name: "Sosis Kanzler Frankfurter 360g", category: "Sosis", brand: "Kanzler", desc: "Sosis frankfurter classic dari Kanzler, lembut dan juicy, cocok untuk hotdog.", weight: "360g", storage: "Disimpan beku (-18°C)", halal: true, badge: "", img: "img/product-sosis.png", isNew: true, isBestSeller: false, price: "Rp 58.000" },
  { id: 6, name: "Sosis Bernardi 1kg", category: "Sosis", brand: "Bernardi", desc: "Sosis sapi premium Bernardi, tekstur lembut dan rasa gurih khas daging sapi.", weight: "1kg", storage: "Disimpan beku (-18°C)", halal: true, badge: "", img: "img/product-sosis.png", isNew: false, isBestSeller: false, price: "Rp 72.000" },
  { id: 7, name: "Nugget Fiesta 500g", category: "Nugget", brand: "Fiesta", desc: "Nugget ayam Fiesta renyah di luar, lembut di dalam. Favorit keluarga Indonesia.", weight: "500g (isi ±20 pcs)", storage: "Disimpan beku (-18°C)", halal: true, badge: "Laris", img: "img/product-nugget.png", isNew: false, isBestSeller: true, price: "Rp 48.000" },
  { id: 8, name: "Nugget Champ 250g", category: "Nugget", brand: "Champ", desc: "Nugget ayam Champ bentuk lucu, disukai anak-anak, praktis digoreng.", weight: "250g", storage: "Disimpan beku (-18°C)", halal: true, badge: "", img: "img/product-nugget.png", isNew: false, isBestSeller: false, price: "Rp 25.000" },
  { id: 9, name: "Nugget So Good Spicy 400g", category: "Nugget", brand: "So Good", desc: "Nugget ayam pedas So Good, sensasi renyah dengan bumbu pedas yang pas.", weight: "400g", storage: "Disimpan beku (-18°C)", halal: true, badge: "Baru", img: "img/product-nugget.png", isNew: true, isBestSeller: false, price: "Rp 40.000" },
  { id: 10, name: "Nugget Golden Fiesta 1kg", category: "Nugget", brand: "Fiesta", desc: "Nugget ayam premium ukuran besar, cocok untuk usaha dan keluarga besar.", weight: "1kg (isi ±40 pcs)", storage: "Disimpan beku (-18°C)", halal: true, badge: "Laris", img: "img/product-nugget.png", isNew: false, isBestSeller: true, price: "Rp 85.000" },
  { id: 11, name: "Bakso Sapi Bernardi 500g", category: "Bakso", brand: "Bernardi", desc: "Bakso sapi asli dari Bernardi, kenyal dan gurih, siap untuk kuah atau goreng.", weight: "500g (isi ±25 pcs)", storage: "Disimpan beku (-18°C)", halal: true, badge: "Laris", img: "img/product-bakso.png", isNew: false, isBestSeller: true, price: "Rp 52.000" },
  { id: 12, name: "Bakso Ikan 300g", category: "Bakso", brand: "Cedea", desc: "Bakso ikan premium, lembut dan bergizi, cocok untuk sup dan hotpot.", weight: "300g", storage: "Disimpan beku (-18°C)", halal: true, badge: "", img: "img/product-bakso.png", isNew: true, isBestSeller: false, price: "Rp 35.000" },
  { id: 13, name: "Bakso Keju So Good 400g", category: "Bakso", brand: "So Good", desc: "Bakso sapi isi keju leleh dari So Good, sensasi unik saat digigit.", weight: "400g", storage: "Disimpan beku (-18°C)", halal: true, badge: "Baru", img: "img/product-bakso.png", isNew: true, isBestSeller: false, price: "Rp 45.000" },
  { id: 14, name: "Lumpia Udang 300g", category: "Frozen Lainnya", brand: "Cedea", desc: "Lumpia isi udang renyah, cocok sebagai camilan atau lauk pendamping.", weight: "300g (isi ±10 pcs)", storage: "Disimpan beku (-18°C)", halal: true, badge: "Baru", img: "img/product-frozen.png", isNew: true, isBestSeller: false, price: "Rp 38.000" },
  { id: 15, name: "Dimsum Ayam 500g", category: "Frozen Lainnya", brand: "Fiesta", desc: "Dimsum ayam kukus premium, lembut dan juicy, sajikan dengan saus pedas.", weight: "500g (isi ±15 pcs)", storage: "Disimpan beku (-18°C)", halal: true, badge: "", img: "img/product-frozen.png", isNew: false, isBestSeller: true, price: "Rp 46.000" },
  { id: 16, name: "Kentang Goreng Aviko 1kg", category: "Frozen Lainnya", brand: "Aviko", desc: "Kentang goreng impor premium, renyah sempurna untuk pendamping makan.", weight: "1kg", storage: "Disimpan beku (-18°C)", halal: true, badge: "", img: "img/product-frozen.png", isNew: false, isBestSeller: false, price: "Rp 62.000" },
  { id: 17, name: "Otak-Otak Bandeng 250g", category: "Frozen Lainnya", brand: "Cedea", desc: "Otak-otak ikan bandeng khas, gurih dan nikmat dibakar atau dikukus.", weight: "250g (isi ±8 pcs)", storage: "Disimpan beku (-18°C)", halal: true, badge: "", img: "img/product-frozen.png", isNew: true, isBestSeller: false, price: "Rp 30.000" },
  { id: 18, name: "Siomay Ikan 400g", category: "Frozen Lainnya", brand: "Bernardi", desc: "Siomay ikan lembut dan gurih, tinggal kukus dan sajikan dengan saus kacang.", weight: "400g (isi ±20 pcs)", storage: "Disimpan beku (-18°C)", halal: true, badge: "", img: "img/product-frozen.png", isNew: false, isBestSeller: false, price: "Rp 42.000" }
];

// Load products from API
async function loadProducts() {
  try {
    const response = await fetch('api/products.php');
    if (!response.ok) throw new Error('API error');
    PRODUCTS = await response.json();
    productsLoaded = true;
  } catch (e) {
    console.warn('API not available, using fallback data:', e.message);
    PRODUCTS = PRODUCTS_FALLBACK;
    productsLoaded = true;
  }
}

// Load categories from API
async function loadCategories() {
  try {
    const response = await fetch('api/categories.php');
    if (!response.ok) throw new Error('API error');
    CATEGORIES = await response.json();
    categoriesLoaded = true;
  } catch (e) {
    console.warn('Categories API not available, using fallback:', e.message);
    CATEGORIES = CATEGORIES_FALLBACK;
    categoriesLoaded = true;
  }
}

// Load brands from API
async function loadBrands() {
  try {
    const response = await fetch('api/brands.php');
    if (!response.ok) throw new Error('API error');
    BRANDS = await response.json();
    brandsLoaded = true;
  } catch (e) {
    console.warn('Brands API not available, using fallback:', e.message);
    BRANDS = BRANDS_FALLBACK;
    brandsLoaded = true;
  }
}

// Create category card HTML for homepage
function createCategoryCard(cat, delay) {
  return `
    <a href="produk.html#${cat.slug}" class="category-card" data-aos="fade-up" data-aos-delay="${delay}">
      <div class="category-icon">${cat.icon}</div>
      <h3>${cat.name}</h3>
      <p>${cat.description}</p>
    </a>
  `;
}

// Helper to generate WA message
function getWaLink(productName) {
  const msg = encodeURIComponent(`Halo Nada Agen Sosis, saya ingin memesan *${productName}*. Apakah stok tersedia?`);
  return `https://wa.me/6287769554433?text=${msg}`;
}

// Create product card HTML
function createProductCard(product, aosDelay) {
  const badges = [];
  if (product.badge) {
    badges.push(`<span class="product-badge">${product.badge}</span>`);
  }
  badges.push('<span class="product-badge halal"><i class="bi bi-patch-check-fill"></i> Halal</span>');

  return `
    <div class="product-card" data-aos="fade-up" data-aos-delay="${aosDelay || 0}" data-category="${product.category}" data-brand="${product.brand}">
      ${badges.join('')}
      <div class="product-img-wrap">
        <img src="${product.img}" alt="${product.name}" class="product-img" loading="lazy" width="300" height="300">
      </div>
      <div class="product-info">
        <div class="product-category">${product.category}</div>
        <h3>${product.name}</h3>
        <p class="product-desc">${product.desc}</p>
        <div class="product-price">${product.price}</div>
        <div class="product-meta">
          <span><i class="bi bi-box-fill"></i> ${product.weight}</span>
          <span><i class="bi bi-snow"></i> ${product.storage}</span>
        </div>
        <div class="product-actions">
          <a href="${getWaLink(product.name)}" class="btn btn-wa" target="_blank" rel="noopener">
            <i class="bi bi-whatsapp"></i> Pesan
          </a>
        </div>
      </div>
    </div>
  `;
}
