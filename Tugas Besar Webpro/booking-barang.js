/* ---------- Animasi ---------- */
function markRevealTargets(){
  document.querySelectorAll(`
    .navbar-masjid,
    .page-title,
    .summary-card,
    .form-card,
    .btn-book,
    .badge
  `).forEach(el => el.classList.add('reveal-up'));
}
function initRevealObserver(){
  const io = new IntersectionObserver((entries,obs)=>{
    entries.forEach(e=>{
      if(e.isIntersecting){ e.target.classList.add('show'); obs.unobserve(e.target); }
    });
  }, {threshold:.12, rootMargin:"0px 0px -40px 0px"});
  document.querySelectorAll('.reveal-up').forEach(el=>io.observe(el));
}
window.addEventListener('load', ()=> document.querySelector('.drop-in')?.classList.add('show'));
window.addEventListener('DOMContentLoaded', ()=>{ markRevealTargets(); initRevealObserver(); });

/* ---------- Qty & Sisa ---------- */
const sisaEl = document.getElementById('itemSisa');
const qtyDisplay = document.getElementById('qtyDisplay');
const btnPlus = document.getElementById('btnQtyPlus');
const btnMinus = document.getElementById('btnQtyMinus');

function getMax(){ return Number(sisaEl.dataset.max || '0'); }
function getQty(){ return Number(qtyDisplay.textContent.trim() || '0'); }

function updateQty(newQty){
  const max = getMax();
  const sisaBaru = Math.max(0, max - newQty);
  sisaEl.textContent = String(sisaBaru);
  qtyDisplay.textContent = String(newQty);
  btnMinus.disabled = (newQty <= 0);
  btnPlus.disabled  = (newQty >= max);
  validateForm();
}
btnPlus?.addEventListener('click', ()=> updateQty(Math.min(getQty()+1, getMax())));
btnMinus?.addEventListener('click', ()=> updateQty(Math.max(getQty()-1, 0)));

/* ---------- Kalender mini + tanggal terbooking ---------- */
const calTitle = document.getElementById('calTitle');
const calGrid  = document.getElementById('calGrid');
let calRef = new Date();

/* contoh 3 tanggal terbooking (5,12,19 setiap bulan) */
function getBookedDays(y,m){
  return [5,12,19]; // bisa diganti dengan data dari server
}
function isToday(y,m,d){
  const t=new Date();
  return y===t.getFullYear() && m===t.getMonth() && d===t.getDate();
}
function renderCalendar(ref){
  const y = ref.getFullYear(), m = ref.getMonth();
  const first = new Date(y, m, 1);
  const startDay = (first.getDay() + 6) % 7; // Senin=0
  const daysInMonth = new Date(y, m+1, 0).getDate();
  const prevDays = new Date(y, m, 0).getDate();
  const booked = new Set(getBookedDays(y,m));

  calTitle.textContent = ref.toLocaleDateString('id-ID', {month:'long', year:'numeric'});

  let html = '';
  'S N S R K J S'.split(' ').forEach(h=> html += `<span class="muted">${h}</span>`);
  for(let i=startDay;i>0;i--) html += `<span class="muted">${prevDays - i + 1}</span>`;

  for(let d=1; d<=daysInMonth; d++){
    const cls = [
      isToday(y,m,d)?'today':'',
      booked.has(d)?'booked':''
    ].join(' ').trim();
    html += `<span class="${cls}">${d}</span>`;
  }
  calGrid.innerHTML = html;
}
renderCalendar(calRef);
document.getElementById('calPrev')?.addEventListener('click', ()=>{ calRef.setMonth(calRef.getMonth()-1); renderCalendar(calRef); syncDateBlock(); });
document.getElementById('calNext')?.addEventListener('click', ()=>{ calRef.setMonth(calRef.getMonth()+1); renderCalendar(calRef); syncDateBlock(); });

/* ---------- Input tanggal: blokir tanggal merah ---------- */
const loanDate = document.getElementById('loanDate');
function syncDateBlock(){
  if(!loanDate) return;
  // set minimal hari ini
  const today = new Date(); today.setHours(0,0,0,0);
  loanDate.min = today.toISOString().split('T')[0];

  // jika user memilih tanggal booked -> batal & beri info
  loanDate.addEventListener('change', ()=>{
    if(!loanDate.value) return;
    const d = new Date(loanDate.value);
    const booked = new Set(getBookedDays(d.getFullYear(), d.getMonth()));
    if (booked.has(d.getDate())){
      alert('Tanggal yang dipilih sudah terbooking/habis. Silakan pilih tanggal lainnya.');
      loanDate.value = '';
    }
    validateForm();
  });
}
syncDateBlock();

/* ---------- Validasi Form + Enable submit ---------- */
const form = document.getElementById('bookingForm');
const btnSubmit = document.getElementById('btnSubmit');
const reqInput = document.getElementById('requirements');

function validateForm(){
  const requiredValid = [...form.querySelectorAll('[required]')].every(el => el.value && el.checkValidity());
  const qtyOK = getQty() > 0;
  const fileOK = !!reqInput?.files?.length;

  btnSubmit.disabled = !(requiredValid && qtyOK && fileOK);
}
form?.addEventListener('input', validateForm);
form?.addEventListener('change', validateForm);

reqInput?.addEventListener('change', ()=>{
  const f = reqInput.files?.[0];
  if (!f) return validateForm();
  const max = 10 * 1024 * 1024; // 10MB
  if (f.size > max){
    alert('Ukuran maksimum file 10 MB.');
    reqInput.value = '';
  }
  validateForm();
});

/* ---------- Submit ---------- */
form?.addEventListener('submit', (e)=>{
  e.preventDefault();
  e.stopPropagation();

  if (!form.checkValidity() || getQty() === 0){
    form.classList.add('was-validated');
    validateForm();
    return;
  }

  const data = {
    barang: document.getElementById('itemName')?.textContent?.trim(),
    qty: getQty(),
    tanggal: loanDate?.value,
    mulai: document.getElementById('startTime')?.value,
    durasi: document.getElementById('duration')?.value + ' jam',
    pj: document.getElementById('pjName')?.value,
    nim: document.getElementById('idNumber')?.value,
    email: document.getElementById('email')?.value,
    prodi: document.getElementById('studyProgram')?.value,
    keperluan: document.getElementById('purpose')?.value,
    detail: document.getElementById('longPurpose')?.value,
    dokumen: reqInput?.files?.[0]?.name || '(tidak ada)'
  };

  alert(
    `Booking berhasil!\n\nBarang: ${data.barang}\nQty: ${data.qty}\nTanggal: ${data.tanggal}\nMulai: ${data.mulai} (${data.durasi})\nPJ: ${data.pj} (${data.nim})\nEmail: ${data.email}\nProdi/Unit: ${data.prodi}\nKeperluan: ${data.keperluan}\nDokumen: ${data.dokumen}\n\nDetail:\n${data.detail}`
  );

  form.reset();
  updateQty(0);
  form.classList.remove('was-validated');
  validateForm();
});

/* ---------- Batalkan ---------- */
document.getElementById('btnCancel')?.addEventListener('click', ()=>{
  if (confirm('Batalkan pengisian booking? Perubahan akan direset.')){
    form.reset(); updateQty(0); form.classList.remove('was-validated'); validateForm();
  }
});

/* ---------- Prefill default ---------- */
(function prefill(){
  const st = document.getElementById('startTime');
  if (st && !st.value) st.value = '10:00';
  if (loanDate && !loanDate.value){
    const today = new Date();
    loanDate.valueAsDate = today;
    // jika hari ini kebetulan merah, kosongkan
    const booked = new Set(getBookedDays(today.getFullYear(), today.getMonth()));
    if (booked.has(today.getDate())) loanDate.value = '';
  }
  validateForm();
})();
