/**
 * RBK STUDIO × RBK KONSTRUKSI — 2-STEP LEAD FORM AJAX ENGINE
 */

document.addEventListener('DOMContentLoaded', () => {
  initLeadForm();
});

function initLeadForm() {
  const form = document.getElementById('lead-form-element');
  const step1Container = document.getElementById('step-1-container');
  const step2Container = document.getElementById('step-2-container');
  const btnStep1 = document.getElementById('btn-submit-step1');
  const btnStep2 = document.getElementById('btn-submit-step2');
  const btnSkipStep2 = document.getElementById('btn-skip-step2');

  if (!form || !btnStep1) return;

  let activeLeadId = 0;
  let activeCode = '';

  // STEP 1 AJAX SUBMISSION
  btnStep1.addEventListener('click', async (e) => {
    e.preventDefault();

    const nameInput = document.getElementById('form-name');
    const phoneInput = document.getElementById('form-phone');
    const needInput = document.getElementById('form-need');

    if (!nameInput.value.trim() || !phoneInput.value.trim()) {
      alert('Nama dan Nomor WhatsApp wajib diisi.');
      return;
    }

    btnStep1.innerText = 'Menyimpan...';
    btnStep1.disabled = true;

    try {
      const formData = new FormData(form);
      const res = await fetch('/api/lead/step1', {
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      });

      const data = await res.json();

      if (data.success) {
        activeLeadId = data.lead_id;
        activeCode = data.code;

        // Transition to Step 2
        step1Container.style.display = 'none';
        step2Container.style.display = 'block';

        trackEvent('lead_step1', { need: needInput.value });
      } else {
        alert(data.message || 'Terjadi kesalahan. Silakan periksa kembali data Anda.');
        btnStep1.innerText = 'Lanjut ke Langkah 2 →';
        btnStep1.disabled = false;
      }
    } catch (err) {
      console.error(err);
      alert('Gagal terhubung ke server.');
      btnStep1.innerText = 'Lanjut ke Langkah 2 →';
      btnStep1.disabled = false;
    }
  });

  // STEP 2 AJAX SUBMISSION
  if (form) {
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      if (!activeLeadId) return;

      btnStep2.innerText = 'Mengirim...';
      btnStep2.disabled = true;

      const formData = new FormData(form);
      formData.append('lead_id', activeLeadId);

      try {
        const res = await fetch('/api/lead/step2', {
          method: 'POST',
          body: formData,
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          }
        });

        const data = await res.json();
        if (data.success) {
          window.location.href = data.redirect;
        } else {
          alert(data.message || 'Gagal menyimpan data.');
          btnStep2.innerText = 'Kirim Informasi & Konsultasi';
          btnStep2.disabled = false;
        }
      } catch (err) {
        console.error(err);
        alert('Gagal terhubung ke server.');
        btnStep2.innerText = 'Kirim Informasi & Konsultasi';
        btnStep2.disabled = false;
      }
    });
  }

  // SKIP STEP 2 ACTION
  if (btnSkipStep2) {
    btnSkipStep2.addEventListener('click', async () => {
      if (!activeLeadId) return;

      const formData = new FormData(form);
      formData.append('lead_id', activeLeadId);

      await fetch('/api/lead/step2', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });

      window.location.href = `/terima-kasih?kode=${activeCode}`;
    });
  }
}
