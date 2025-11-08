// aktifkan nav "Laporan" otomatis
    (function(){
      const current=(location.pathname.split('/').pop()||'laporan.html').toLowerCase();
      document.querySelectorAll('.navbar-nav .nav-link').forEach(a=>{
        const href=(a.getAttribute('href')||'').toLowerCase();
        if(href && current && href.endsWith(current)) a.classList.add('active');
      });
    })();

    // bangun chart "Top Dipinjam" dari tabel (jumlah dijumlahkan per nama)
    const rows=[...document.querySelectorAll('#tbodyLaporan tr')];
    const popularMap=new Map();
    rows.forEach(tr=>{
      const name=tr.children[1].textContent.trim();
      const qty=parseInt(tr.children[7].textContent.trim(),10)||0;
      popularMap.set(name,(popularMap.get(name)||0)+qty);
    });
    const top=[...popularMap.entries()].sort((a,b)=>b[1]-a[1]).slice(0,10);
    const labels=top.map(x=>x[0]);
    const data=top.map(x=>x[1]);

    const ctx=document.getElementById('chartTop');
    new Chart(ctx,{
      type:'bar',
      data:{labels, datasets:[{label:'Jumlah Dipinjam', data}]},
      options:{
        responsive:true,
        plugins:{legend:{display:false}},
        scales:{y:{beginAtZero:true}}
      }
    });