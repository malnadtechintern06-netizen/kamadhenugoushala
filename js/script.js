/**
 * Kamadhenu Goushala - Interactive JavaScript Engine
 */

document.addEventListener('DOMContentLoaded', () => {
  initSitePreloader();
  initMobileMenu();
  initCartActions();
  initDonationPresets();
  initGalleryLightbox();
  initCardFilters();
  initQuantityControls();
  initThemeToggle();
  initHeroBg3D();
  initPaymentMethodSelector();
});

/**
 * Website Opening & Refresh Loader (Index Page)
 */
function initSitePreloader() {
  const preloader = document.getElementById('site-preloader');
  if (!preloader) return;

  const progressBar = preloader.querySelector('.preloader-bar');
  const statusText = document.getElementById('preloader-status-text');

  if (progressBar) {
    requestAnimationFrame(() => {
      setTimeout(() => {
        progressBar.style.width = '100%';
      }, 80);
    });
  }

  // Update status messages gracefully during loading
  if (statusText) {
    setTimeout(() => {
      statusText.textContent = 'Preparing Gau Seva...';
    }, 700);
    setTimeout(() => {
      statusText.textContent = 'Welcome to Gau Sanctuary ✨';
    }, 1400);
  }

  const finishLoading = () => {
    if (preloader.classList.contains('preloader-done')) return;
    preloader.classList.add('preloader-done');
    setTimeout(() => {
      preloader.style.display = 'none';
      if (preloader.parentNode) preloader.parentNode.removeChild(preloader);
    }, 600);
  };

  // Allow comfortable ~1.8s load display time so user experiences the animation nicely
  setTimeout(finishLoading, 1800);
}



/**
 * Theme Toggle (Dark / Light Mode)
 */
function initThemeToggle() {
  const themeBtn = document.getElementById('theme-toggle');
  if (!themeBtn) return;

  // Check saved theme or system preference
  const savedTheme = localStorage.getItem('theme');
  if (savedTheme === 'dark') {
    document.documentElement.setAttribute('data-theme', 'dark');
    themeBtn.textContent = '☀️';
  } else if (savedTheme === 'light') {
    document.documentElement.removeAttribute('data-theme');
    themeBtn.textContent = '🌙';
  } else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
    document.documentElement.setAttribute('data-theme', 'dark');
    themeBtn.textContent = '☀️';
  }

  themeBtn.addEventListener('click', () => {
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    if (isDark) {
      document.documentElement.removeAttribute('data-theme');
      localStorage.setItem('theme', 'light');
      themeBtn.textContent = '🌙';
    } else {
      document.documentElement.setAttribute('data-theme', 'dark');
      localStorage.setItem('theme', 'dark');
      themeBtn.textContent = '☀️';
    }
  });
}

/**
 * Mobile Navigation Menu Drawer Toggle
 */
function initMobileMenu() {
  const hamburgerBtn = document.querySelector('.hamburger-btn');
  const navMenu = document.querySelector('.nav-menu');
  const backdrop = document.querySelector('.nav-backdrop');

  function toggleMenu(show) {
    const isActive = show !== undefined ? show : !navMenu.classList.contains('active');
    if (isActive) {
      navMenu.classList.add('active');
      if (backdrop) backdrop.classList.add('active');
      hamburgerBtn.innerHTML = '✕';
      document.body.style.overflow = 'hidden';
    } else {
      navMenu.classList.remove('active');
      if (backdrop) backdrop.classList.remove('active');
      hamburgerBtn.innerHTML = '☰';
      document.body.style.overflow = '';
    }
  }

  if (hamburgerBtn && navMenu) {
    hamburgerBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      toggleMenu();
    });

    if (backdrop) {
      backdrop.addEventListener('click', () => toggleMenu(false));
    }

    // Close when clicking nav links inside mobile drawer
    navMenu.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', () => toggleMenu(false));
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

/**
 * Subtle 3D Mouse Parallax on Hero Background Photo
 */
function initHeroBg3D() {
  const hero = document.querySelector('.hero-section');
  const heroBg = document.querySelector('.hero-bg-photo');
  if (!hero || !heroBg) return;

  hero.addEventListener('mousemove', (e) => {
    const rect = hero.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;
    const centerX = rect.width / 2;
    const centerY = rect.height / 2;
    const moveX = (x - centerX) / centerX;
    const moveY = (y - centerY) / centerY;

    const rotateX = -moveY * 3;
    const rotateY = moveX * 3.5;
    const transX = -moveX * 14;
    const transY = -moveY * 14;

    heroBg.style.transform = `scale(1.06) translate3d(${transX}px, ${transY}px, 25px) rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
    heroBg.style.transition = 'transform 0.15s ease-out';
  });

  hero.addEventListener('mouseleave', () => {
    heroBg.style.transform = '';
    heroBg.style.transition = 'transform 1s ease-out';
  });
}

/**
 * Interactive Payment Options Selector
 */
function initPaymentMethodSelector() {
  const cards = document.querySelectorAll('.payment-option-card');
  if (!cards.length) return;

  cards.forEach(card => {
    const radio = card.querySelector('input[type="radio"]');
    if (!radio) return;

    const handleSelect = () => {
      const container = card.closest('.payment-options-grid');
      if (container) {
        container.querySelectorAll('.payment-option-card').forEach(c => c.classList.remove('active'));
      }
      card.classList.add('active');
      radio.checked = true;

      const val = radio.value.toLowerCase();
      const parentForm = card.closest('form');
      if (!parentForm) return;

      const subfields = parentForm.querySelectorAll('.payment-subfield');
      subfields.forEach(sub => sub.style.display = 'none');

      if (val.includes('upi')) {
        const field = parentForm.querySelector('#payment-field-upi');
        if (field) field.style.display = 'block';
      } else if (val.includes('card')) {
        const field = parentForm.querySelector('#payment-field-card');
        if (field) field.style.display = 'block';
      } else if (val.includes('banking') || val.includes('net')) {
        const field = parentForm.querySelector('#payment-field-netbanking');
        if (field) field.style.display = 'block';
      } else if (val.includes('bank') || val.includes('neft')) {
        const field = parentForm.querySelector('#payment-field-bank');
        if (field) field.style.display = 'block';
      }
    };

    card.addEventListener('click', handleSelect);
    radio.addEventListener('change', handleSelect);
  });
}

/**
 * Copy UPI VPA ID to Clipboard with Toast Notification
 */
function copyUpiId(idText = 'kamadhenugoushala@sbi') {
  if (navigator.clipboard && navigator.clipboard.writeText) {
    navigator.clipboard.writeText(idText).then(() => {
      showToast('✓ UPI ID copied: ' + idText, 'success');
    }).catch(() => {
      fallbackCopy(idText);
    });
  } else {
    fallbackCopy(idText);
  }
}

function fallbackCopy(idText) {
  const textarea = document.createElement('textarea');
  textarea.value = idText;
  document.body.appendChild(textarea);
  textarea.select();
  try {
    document.execCommand('copy');
    showToast('✓ UPI ID copied: ' + idText, 'success');
  } catch (e) {
    showToast('UPI VPA: ' + idText, 'info');
  }
  document.body.removeChild(textarea);
}

/**
 * Day & Night Theme Toggle Handler (Light / Dark Mode)
 */
function initThemeToggle() {
  const toggleBtn = document.getElementById('theme-toggle');
  
  // Read saved theme preference or default to light
  const currentTheme = localStorage.getItem('theme') || 'light';
  document.documentElement.setAttribute('data-theme', currentTheme);

  const updateToggleUI = (theme) => {
    if (!toggleBtn) return;
    if (theme === 'dark') {
      toggleBtn.innerHTML = '☀️';
      toggleBtn.title = 'Switch to Day Mode (Light)';
      toggleBtn.setAttribute('aria-label', 'Switch to Day Mode');
    } else {
      toggleBtn.innerHTML = '🌙';
      toggleBtn.title = 'Switch to Night Mode (Dark)';
      toggleBtn.setAttribute('aria-label', 'Switch to Night Mode');
    }
  };

  updateToggleUI(currentTheme);

  if (toggleBtn) {
    toggleBtn.addEventListener('click', (e) => {
      e.preventDefault();
      const activeTheme = document.documentElement.getAttribute('data-theme');
      const newTheme = activeTheme === 'dark' ? 'light' : 'dark';
      
      document.documentElement.setAttribute('data-theme', newTheme);
      localStorage.setItem('theme', newTheme);
      
      updateToggleUI(newTheme);

      if (typeof showToast === 'function') {
        showToast(newTheme === 'dark' ? '🌙 Switched to Night Mode' : '☀️ Switched to Day Mode', 'info');
      }
    });
  }
}




