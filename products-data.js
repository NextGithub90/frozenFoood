// ========== PRODUCTS DATABASE ==========
const PRODUCTS = [
  // === SOSIS ===
  {
    id: 1, name: "Sosis Kanzler Bratwurst 500g", category: "Sosis", brand: "Kanzler",
    desc: "Sosis bratwurst premium dari Kanzler dengan cita rasa autentik Jerman, cocok untuk BBQ dan masakan sehari-hari.",
    weight: "500g", storage: "Frozen (-18°C)", halal: true, badge: "Laris",
    img: "img/product-sosis.png", isNew: false, isBestSeller: true
  },
  {
    id: 2, name: "Sosis Fiesta 1kg", category: "Sosis", brand: "Fiesta",
    desc: "Sosis ayam Fiesta ukuran ekonomis, ideal untuk keluarga dan usaha kuliner.",
    weight: "1kg (isi ±15 pcs)", storage: "Frozen (-18°C)", halal: true, badge: "Laris",
    img: "img/product-sosis.png", isNew: false, isBestSeller: true
  },
  {
    id: 3, name: "Sosis So Nice 700g", category: "Sosis", brand: "So Nice",
    desc: "Sosis ayam So Nice yang digemari anak-anak, praktis dimasak dan bergizi.",
    weight: "700g", storage: "Frozen (-18°C)", halal: true, badge: "",
    img: "img/product-sosis.png", isNew: true, isBestSeller: false
  },
  {
    id: 4, name: "Sosis Champ 500g", category: "Sosis", brand: "Champ",
    desc: "Sosis ayam Champ rasa original, pas untuk camilan dan bekal sekolah.",
    weight: "500g", storage: "Frozen (-18°C)", halal: true, badge: "",
    img: "img/product-sosis.png", isNew: false, isBestSeller: false
  },
  {
    id: 5, name: "Sosis Kanzler Frankfurter 360g", category: "Sosis", brand: "Kanzler",
    desc: "Sosis frankfurter classic dari Kanzler, lembut dan juicy, cocok untuk hotdog.",
    weight: "360g", storage: "Frozen (-18°C)", halal: true, badge: "",
    img: "img/product-sosis.png", isNew: true, isBestSeller: false
  },
  {
    id: 6, name: "Sosis Bernardi 1kg", category: "Sosis", brand: "Bernardi",
    desc: "Sosis sapi premium Bernardi, tekstur lembut dan rasa gurih khas daging sapi.",
    weight: "1kg", storage: "Frozen (-18°C)", halal: true, badge: "",
    img: "img/product-sosis.png", isNew: false, isBestSeller: false
  },

  // === NUGGET ===
  {
    id: 7, name: "Nugget Fiesta 500g", category: "Nugget", brand: "Fiesta",
    desc: "Nugget ayam Fiesta renyah di luar, lembut di dalam. Favorit keluarga Indonesia.",
    weight: "500g (isi ±20 pcs)", storage: "Frozen (-18°C)", halal: true, badge: "Laris",
    img: "img/product-nugget.png", isNew: false, isBestSeller: true
  },
  {
    id: 8, name: "Nugget Champ 250g", category: "Nugget", brand: "Champ",
    desc: "Nugget ayam Champ bentuk lucu, disukai anak-anak, praktis digoreng.",
    weight: "250g", storage: "Frozen (-18°C)", halal: true, badge: "",
    img: "img/product-nugget.png", isNew: false, isBestSeller: false
  },
  {
    id: 9, name: "Nugget So Good Spicy 400g", category: "Nugget", brand: "So Good",
    desc: "Nugget ayam pedas So Good, sensasi renyah dengan bumbu pedas yang pas.",
    weight: "400g", storage: "Frozen (-18°C)", halal: true, badge: "Baru",
    img: "img/product-nugget.png", isNew: true, isBestSeller: false
  },
  {
    id: 10, name: "Nugget Golden Fiesta 1kg", category: "Nugget", brand: "Fiesta",
    desc: "Nugget ayam premium ukuran besar, cocok untuk usaha dan keluarga besar.",
    weight: "1kg (isi ±40 pcs)", storage: "Frozen (-18°C)", halal: true, badge: "Laris",
    img: "img/product-nugget.png", isNew: false, isBestSeller: true
  },

  // === BAKSO ===
  {
    id: 11, name: "Bakso Sapi Bernardi 500g", category: "Bakso", brand: "Bernardi",
    desc: "Bakso sapi asli dari Bernardi, kenyal dan gurih, siap untuk kuah atau goreng.",
    weight: "500g (isi ±25 pcs)", storage: "Frozen (-18°C)", halal: true, badge: "Laris",
    img: "img/product-bakso.png", isNew: false, isBestSeller: true
  },
  {
    id: 12, name: "Bakso Ikan 300g", category: "Bakso", brand: "Cedea",
    desc: "Bakso ikan premium, lembut dan bergizi, cocok untuk sup dan hotpot.",
    weight: "300g", storage: "Frozen (-18°C)", halal: true, badge: "",
    img: "img/product-bakso.png", isNew: true, isBestSeller: false
  },
  {
    id: 13, name: "Bakso Keju So Good 400g", category: "Bakso", brand: "So Good",
    desc: "Bakso sapi isi keju leleh dari So Good, sensasi unik saat digigit.",
    weight: "400g", storage: "Frozen (-18°C)", halal: true, badge: "Baru",
    img: "img/product-bakso.png", isNew: true, isBestSeller: false
  },

  // === FROZEN LAINNYA ===
  {
    id: 14, name: "Lumpia Udang 300g", category: "Frozen Lainnya", brand: "Cedea",
    desc: "Lumpia isi udang renyah, cocok sebagai camilan atau lauk pendamping.",
    weight: "300g (isi ±10 pcs)", storage: "Frozen (-18°C)", halal: true, badge: "Baru",
    img: "img/product-frozen.png", isNew: true, isBestSeller: false
  },
  {
    id: 15, name: "Dimsum Ayam 500g", category: "Frozen Lainnya", brand: "Fiesta",
    desc: "Dimsum ayam kukus premium, lembut dan juicy, sajikan dengan saus pedas.",
    weight: "500g (isi ±15 pcs)", storage: "Frozen (-18°C)", halal: true, badge: "",
    img: "img/product-frozen.png", isNew: false, isBestSeller: true
  },
  {
    id: 16, name: "Kentang Goreng Aviko 1kg", category: "Frozen Lainnya", brand: "Aviko",
    desc: "Kentang goreng impor premium, renyah sempurna untuk pendamping makan.",
    weight: "1kg", storage: "Frozen (-18°C)", halal: true, badge: "",
    img: "img/product-frozen.png", isNew: false, isBestSeller: false
  },
  {
    id: 17, name: "Otak-Otak Bandeng 250g", category: "Frozen Lainnya", brand: "Cedea",
    desc: "Otak-otak ikan bandeng khas, gurih dan nikmat dibakar atau dikukus.",
    weight: "250g (isi ±8 pcs)", storage: "Frozen (-18°C)", halal: true, badge: "",
    img: "img/product-frozen.png", isNew: true, isBestSeller: false
  },
  {
    id: 18, name: "Siomay Ikan 400g", category: "Frozen Lainnya", brand: "Bernardi",
    desc: "Siomay ikan lembut dan gurih, tinggal kukus dan sajikan dengan saus kacang.",
    weight: "400g (isi ±20 pcs)", storage: "Frozen (-18°C)", halal: true, badge: "",
    img: "img/product-frozen.png", isNew: false, isBestSeller: false
  }
];

// Helper to generate WA message
function getWaLink(productName) {
  const msg = encodeURIComponent(`Halo Nada Agen Sosis, saya ingin memesan *${productName}*. Apakah stok tersedia?`);
  return `https://wa.me/6287815991605?text=${msg}`;
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
