// Animasi masuk
window.addEventListener('load', ()=>{
  document.querySelectorAll('.drop-in,.reveal-up').forEach(el=>{
    // beri sedikit stagger
    setTimeout(()=>el.classList.add('show'), 80);
  });
});

// Tampilkan email tujuan (opsional)
const email = localStorage.getItem('lastBookingEmail') || '';
if (email) {
  const el = document.getElementById('descText');
  if (el) el.textContent = `Notifikasi sukses dikirim ke email: ${email}`;
}
