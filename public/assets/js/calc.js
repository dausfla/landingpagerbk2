/**
 * RBK STUDIO × RBK KONSTRUKSI — COST ESTIMATOR CALCULATOR
 * Dual-Card Realtime Calculation Engine & Form Synchronization
 */

document.addEventListener('DOMContentLoaded', () => {
  initCalculator();
});

function initCalculator() {
  const scriptData = document.getElementById('pricing-data');
  if (!scriptData) return;

  let pricing = {};
  try {
    pricing = JSON.parse(scriptData.textContent);
  } catch (e) {
    console.error('Failed to parse pricing data:', e);
    return;
  }

  // Card 1: Desain Elements
  const desainSlider = document.getElementById('calc-desain-slider');
  const desainNum = document.getElementById('calc-desain-num');
  const selectDesain = document.getElementById('calc-select-desain');
  const desainResult = document.getElementById('calc-desain-result');
  const desainFormula = document.getElementById('calc-desain-formula');
  const btnDesain = document.getElementById('btn-calc-desain');

  // Card 2: Bangun Elements
  const bangunSlider = document.getElementById('calc-bangun-slider');
  const bangunNum = document.getElementById('calc-bangun-num');
  const selectBangun = document.getElementById('calc-select-bangun');
  const bangunResult = document.getElementById('calc-bangun-result');
  const bangunFormula = document.getElementById('calc-bangun-formula');
  const btnBangun = document.getElementById('btn-calc-bangun');

  // Helper Format Rupiah
  function formatRupiahDisplay(min, max) {
    if (min === max) {
      if (min >= 1000000000) {
        return 'Rp' + (min / 1000000000).toFixed(2).replace('.', ',') + ' M';
      }
      if (min >= 1000000) {
        const jt = (min / 1000000).toFixed(1).replace('.0', '').replace('.', ',');
        return 'Rp' + jt + ' jt';
      }
      return 'Rp' + min.toLocaleString('id-ID');
    }

    if (max >= 1000000000) {
      const minM = (min / 1000000000).toFixed(2).replace('.', ',');
      const maxM = (max / 1000000000).toFixed(2).replace('.', ',');
      return `Rp${minM}–${maxM} M`;
    }

    if (max >= 1000000) {
      const minJt = (min / 1000000).toFixed(1).replace('.0', '').replace('.', ',');
      const maxJt = (max / 1000000).toFixed(1).replace('.0', '').replace('.', ',');
      return `Rp${minJt}–${maxJt} jt`;
    }

    return 'Rp' + min.toLocaleString('id-ID') + ' – Rp' + max.toLocaleString('id-ID');
  }

  // 1. Calculate Desain
  function calculateDesain() {
    if (!desainSlider || !desainResult) return;
    const area = parseInt(desainSlider.value) || 120;
    const pkgName = selectDesain ? selectDesain.value : 'Standard';
    const p = pricing.desain?.[pkgName] || { price_min: 80000, price_max: 80000 };

    const minVal = area * p.price_min;
    const maxVal = area * (p.price_max || p.price_min);

    const resultStr = formatRupiahDisplay(minVal, maxVal);
    desainResult.innerText = resultStr;

    const rateKb = (p.price_min >= 1000) ? (p.price_min / 1000).toLocaleString('id-ID') + 'rb' : p.price_min;
    if (desainFormula) {
      desainFormula.innerText = `${area} m² × Rp${rateKb}/m² (${pkgName})`;
    }

    if (btnDesain) {
      btnDesain.dataset.snapshot = JSON.stringify({
        mode: 'desain',
        area: area,
        pkgDesain: pkgName,
        totalMin: minVal,
        totalMax: maxVal,
        displayStr: resultStr
      });
    }
  }

  // 2. Calculate Bangun
  function calculateBangun() {
    if (!bangunSlider || !bangunResult) return;
    const area = parseInt(bangunSlider.value) || 120;
    const pkgName = selectBangun ? selectBangun.value : 'Standard';
    const p = pricing.bangun?.[pkgName] || { price_min: 4500000, price_max: 5000000 };

    const minVal = area * p.price_min;
    const maxVal = area * (p.price_max || p.price_min);

    const resultStr = formatRupiahDisplay(minVal, maxVal);
    bangunResult.innerText = resultStr;

    const minJt = (p.price_min / 1000000).toString().replace('.', ',');
    const maxJt = (p.price_max / 1000000).toString().replace('.', ',');
    if (bangunFormula) {
      bangunFormula.innerText = `${area} m² × Rp${minJt}–${maxJt} jt/m² (${pkgName})`;
    }

    if (btnBangun) {
      btnBangun.dataset.snapshot = JSON.stringify({
        mode: 'bangun',
        area: area,
        pkgBangun: pkgName,
        totalMin: minVal,
        totalMax: maxVal,
        displayStr: resultStr
      });
    }
  }

  // Event Listeners - Desain Card
  if (desainSlider && desainNum) {
    desainSlider.addEventListener('input', () => {
      desainNum.value = desainSlider.value;
      calculateDesain();
    });
    desainNum.addEventListener('input', () => {
      let val = parseInt(desainNum.value) || 30;
      if (val < 30) val = 30;
      if (val > 1000) val = 1000;
      desainSlider.value = val;
      calculateDesain();
    });
    if (selectDesain) selectDesain.addEventListener('change', calculateDesain);
    calculateDesain();
  }

  // Event Listeners - Bangun Card
  if (bangunSlider && bangunNum) {
    bangunSlider.addEventListener('input', () => {
      bangunNum.value = bangunSlider.value;
      calculateBangun();
    });
    bangunNum.addEventListener('input', () => {
      let val = parseInt(bangunNum.value) || 30;
      if (val < 30) val = 30;
      if (val > 1000) val = 1000;
      bangunSlider.value = val;
      calculateBangun();
    });
    if (selectBangun) selectBangun.addEventListener('change', calculateBangun);
    calculateBangun();
  }

  // CTA Click Handlers - Autofill Form & Scroll
  function handleCtaClick(btn) {
    if (!btn) return;
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const snapshotRaw = btn.dataset.snapshot;
      if (!snapshotRaw) return;

      const snap = JSON.parse(snapshotRaw);
      const calcSnapshotField = document.getElementById('calc_snapshot');
      if (calcSnapshotField) {
        calcSnapshotField.value = JSON.stringify(snap);
      }

      // Pre-select need dropdown in form
      const needSelect = document.getElementById('form-need');
      if (needSelect) {
        if (snap.mode === 'desain') needSelect.value = 'Desain rumah/bangunan (RBK Studio)';
        else if (snap.mode === 'bangun') needSelect.value = 'Bangun rumah (RBK Konstruksi)';
      }

      const formBuildingArea = document.getElementById('building_size_m2');
      if (formBuildingArea) {
        formBuildingArea.value = snap.area;
      }

      // Smooth scroll to form
      const formEl = document.querySelector('#konsultasi');
      if (formEl) {
        formEl.scrollIntoView({ behavior: 'smooth' });
      }

      if (typeof window.trackEvent === 'function') {
        window.trackEvent('calculator_cta', snap);
      }
    });
  }

  handleCtaClick(btnDesain);
  handleCtaClick(btnBangun);
}
