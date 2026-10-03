/**
 * RBK STUDIO × RBK KONSTRUKSI — MAIN JAVASCRIPT
 * ES Module compatible, lightweight vanilla JS (<60KB gzip)
 */

document.addEventListener('DOMContentLoaded', () => {
  initMobileMenu();
  initScrollReveal();
  initPortfolioFilters();
  initBeforeAfterSliders();
  initMobileCtaBar();
});

/* 1. Mobile Hamburger Menu */
function initMobileMenu() {
  const toggle = document.querySelector('.hamburger-toggle');
  const menu = document.querySelector('.navbar-menu');
  if (toggle && menu) {
    toggle.addEventListener('click', () => {
      menu.classList.toggle('active');
    });
  }
}

/* 2. Scroll Reveal Animations */
function initScrollReveal() {
  const reveals = document.querySelectorAll('.reveal');
  if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('active');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.1 });

    reveals.forEach(el => observer.observe(el));
  } else {
    reveals.forEach(el => el.classList.add('active'));
  }
}

/* 3. Portfolio Category Filtering */
function initPortfolioFilters() {
  const chips = document.querySelectorAll('.portfolio-chip');
  const cards = document.querySelectorAll('.portfolio-card');

  if (!chips.length || !cards.length) return;

  chips.forEach(chip => {
    chip.addEventListener('click', () => {
      if (chip.classList.contains('active')) return;

      chips.forEach(c => c.classList.remove('active'));
      chip.classList.add('active');

      const cat = chip.dataset.category;

      // Stage 1: Add hiding class for fade out transition
      cards.forEach(card => card.classList.add('is-hiding'));

      setTimeout(() => {
        // Stage 2: Toggle hidden state
        cards.forEach(card => {
          const isMatch = (cat === 'all' || card.dataset.category === cat);
          if (isMatch) {
            card.classList.remove('is-hidden');
          } else {
            card.classList.add('is-hidden');
          }
        });

        // Stage 3: Fade back in matching cards on next frame
        requestAnimationFrame(() => {
          requestAnimationFrame(() => {
            cards.forEach(card => card.classList.remove('is-hiding'));
          });
        });
      }, 200);

      if (window.trackEvent) {
        window.trackEvent('portfolio_filter', { category: cat });
      }
    });
  });
}

/* 4. Interactive Before/After Image Slider */
function initBeforeAfterSliders() {
  const sliders = document.querySelectorAll('.ba-container');
  sliders.forEach(container => {
    const input = container.querySelector('.ba-slider-input');
    if (!input) return;

    let ticking = false;
    let latestValue = input.value || 50;

    const updatePosition = () => {
      container.style.setProperty('--pos', `${latestValue}%`);
      ticking = false;
    };

    const requestUpdate = (val) => {
      latestValue = val;
      if (!ticking) {
        requestAnimationFrame(updatePosition);
        ticking = true;
      }
    };

    input.addEventListener('input', (e) => requestUpdate(e.target.value));
    input.addEventListener('change', (e) => requestUpdate(e.target.value));

    // Initialize position
    updatePosition();
  });
}

/* 5. Mobile Sticky CTA Bar Visibility */
function initMobileCtaBar() {
  const mbar = document.querySelector('.mbar');
  const hero = document.querySelector('#top');
  const form = document.querySelector('#konsultasi');

  if (!mbar || !hero) return;

  window.addEventListener('scroll', () => {
    const heroBottom = hero.getBoundingClientRect().bottom;
    const formTop = form ? form.getBoundingClientRect().top : 99999;
    const formBottom = form ? form.getBoundingClientRect().bottom : -99999;

    // Show after hero, hide when form is visible in viewport
    if (heroBottom < 0 && (formTop > window.innerHeight || formBottom < 0)) {
      mbar.style.display = 'flex';
    } else {
      mbar.style.display = 'none';
    }
  });
}

/* Global Event Tracking Wrapper (Section 10.3) */
window.trackEvent = function(eventName, params = {}) {
  if (window.dataLayer) {
    window.dataLayer.push({ event: eventName, ...params });
  }
  if (window.gtag) {
    window.gtag('event', eventName, params);
  }
  if (window.fbq) {
    window.fbq('trackCustom', eventName, params);
  }
  if (window.ttq) {
    window.ttq.track(eventName, params);
  }
};
