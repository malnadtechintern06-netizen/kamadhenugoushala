/**
 * Kamadhenu Goushala - Interactive JavaScript Engine
 */

document.addEventListener('DOMContentLoaded', () => {
  initMobileMenu();
  initCartActions();
  initDonationPresets();
  initGalleryLightbox();
  initCardFilters();
  initQuantityControls();
});

/**
 * Mobile Navigation Menu Drawer Toggle
 */
function initMobileMenu() {
  const hamburgerBtn = document.querySelector('.hamburger-btn');
  const navMenu = document.querySelector('.nav-menu');

  if (hamburgerBtn && navMenu) {
    hamburgerBtn.addEventListener('click', () => {
      navMenu.classList.toggle('active');
      hamburgerBtn.innerHTML = navMenu.classList.contains('active') ? '✕' : '☰';
    });

    // Close when clicking outside
    document.addEventListener('click', (e) => {
      if (!navMenu.contains(e.target) && !hamburgerBtn.contains(e.target)) {
        navMenu.classList.remove('active');
        hamburgerBtn.innerHTML = '☰';
      }
    });
  }
}

/**
 * Toast Notification Popup System
 */
function showToast(message, type = 'success') {
  let container = document.querySelector('.toast-container');
  if (!container) {
    container = document.createElement('div');
    container.className = 'toast-container';
    document.body.appendChild(container);
  }

  const toast = document.createElement('div');
  toast.className = `toast ${type}`;
  
  const iconMap = {
    success: '✓',
    error: '✕',
    warning: '⚠'
  };

  toast.innerHTML = `<span>${iconMap[type] || 'ℹ'}</span><span>${message}</span>`;
  container.appendChild(toast);

  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateX(100%)';
    toast.style.transition = 'all 0.3s ease';
    setTimeout(() => toast.remove(), 300);
  }, 4000);
}

/**
 * AJAX Cart Operations
 */
function initCartActions() {
  // Add to Cart buttons
  document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
    btn.addEventListener('click', async (e) => {
      e.preventDefault();
      const productId = btn.dataset.productId;
      const qtyInput = document.querySelector(`.qty-input[data-product-id="${productId}"]`);
      const quantity = qtyInput ? parseInt(qtyInput.value, 10) : 1;

      btn.disabled = true;
      btn.innerHTML = 'Adding...';

      try {
        const response = await fetch(getApiUrl('cart.php'), {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: new URLSearchParams({
            action: 'add',
            product_id: productId,
            quantity: quantity
          })
        });

        const data = await response.json();
        if (data.success) {
          showToast(data.message, 'success');
          updateCartBadge(data.cart_count);
        } else {
          showToast(data.message || 'Error adding to cart', 'error');
        }
      } catch (err) {
        showToast('Network connection error', 'error');
      } finally {
        btn.disabled = false;
        btn.innerHTML = 'Add to Cart';
      }
    });
  });
}

function updateCartBadge(count) {
  const badge = document.querySelector('.cart-badge');
  if (badge) {
    badge.textContent = count;
    badge.style.display = count > 0 ? 'flex' : 'none';
  }
}

/**
 * Donation Amount Presets Sync
 */
function initDonationPresets() {
  const presetBtns = document.querySelectorAll('.preset-btn');
  const customInput = document.getElementById('custom-amount');

  if (presetBtns.length > 0 && customInput) {
    presetBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        presetBtns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        customInput.value = btn.dataset.amount;
      });
    });

    customInput.addEventListener('input', () => {
      presetBtns.forEach(b => b.classList.remove('active'));
    });
  }
}

/**
 * Gallery Filter & Lightbox Popup
 */
function initGalleryLightbox() {
  const galleryItems = document.querySelectorAll('.gallery-item');
  const modal = document.querySelector('.lightbox-modal');

  if (!modal || galleryItems.length === 0) return;

  const modalImg = modal.querySelector('.lightbox-img');
  const modalCaption = modal.querySelector('.lightbox-caption');
  const closeBtn = modal.querySelector('.lightbox-close');
  const prevBtn = modal.querySelector('.lightbox-prev');
  const nextBtn = modal.querySelector('.lightbox-next');

  let currentIndex = 0;
  const itemsArray = Array.from(galleryItems);

  function openLightbox(index) {
    currentIndex = index;
    const item = itemsArray[currentIndex];
    modalImg.src = item.dataset.image || item.querySelector('img').src;
    modalCaption.textContent = item.dataset.title || '';
    modal.classList.add('active');
  }

  itemsArray.forEach((item, idx) => {
    item.addEventListener('click', () => openLightbox(idx));
  });

  if (closeBtn) closeBtn.addEventListener('click', () => modal.classList.remove('active'));
  if (prevBtn) prevBtn.addEventListener('click', () => openLightbox((currentIndex - 1 + itemsArray.length) % itemsArray.length));
  if (nextBtn) nextBtn.addEventListener('click', () => openLightbox((currentIndex + 1) % itemsArray.length));

  document.addEventListener('keydown', (e) => {
    if (!modal.classList.contains('active')) return;
    if (e.key === 'Escape') modal.classList.remove('active');
    if (e.key === 'ArrowLeft') prevBtn.click();
    if (e.key === 'ArrowRight') nextBtn.click();
  });
}

/**
 * Card Filtering Handler (Cows, Products & Gallery)
 */
function initCardFilters() {
  const filterBtns = document.querySelectorAll('.filter-btn[data-category]');
  filterBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      filterBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');

      const cat = btn.dataset.category.toLowerCase();
      const filterableItems = document.querySelectorAll('[data-category-item]');

      filterableItems.forEach(item => {
        const itemCat = item.dataset.categoryItem.toLowerCase();
        if (cat === 'all' || itemCat === cat) {
          item.style.display = 'flex';
        } else {
          item.style.display = 'none';
        }
      });
    });
  });
}

/**
 * Quantity Controller (+ / -)
 */
function initQuantityControls() {
  document.querySelectorAll('.quantity-control').forEach(ctrl => {
    const minusBtn = ctrl.querySelector('.qty-minus');
    const plusBtn = ctrl.querySelector('.qty-plus');
    const input = ctrl.querySelector('.qty-input');

    if (minusBtn && plusBtn && input) {
      minusBtn.addEventListener('click', () => {
        let val = parseInt(input.value, 10) || 1;
        if (val > 1) {
          input.value = val - 1;
          input.dispatchEvent(new Event('change'));
        }
      });

      plusBtn.addEventListener('click', () => {
        let val = parseInt(input.value, 10) || 1;
        input.value = val + 1;
        input.dispatchEvent(new Event('change'));
      });
    }
  });
}

/**
 * Helper to construct relative API path
 */
function getApiUrl(endpoint) {
  const scriptName = window.location.pathname;
  if (scriptName.includes('/kamadhenugoushala/')) {
    return '/kamadhenugoushala/api/' + endpoint;
  }
  return '/api/' + endpoint;
}
