<form method="GET" class="mb-4 grid grid-cols-2 md:grid-cols-6 gap-2 items-end">
  <div>
    <label class="text-sm">Kebun</label>
    <select id="filterKebun" name="kebun" class="w-full border rounded px-2 py-1 bg-white">
      <option value="">Semua</option>
    </select>
  </div>
  <div>
    <label class="text-sm">Divisi</label>
    <select id="filterDivisi" name="divisi" class="w-full border rounded px-2 py-1 bg-white">
      <option value="">Semua</option>
    </select>
  </div>
  <div>
    <label class="text-sm">Start</label>
    <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full border rounded px-2 py-1 bg-white" />
  </div>
  <div>
    <label class="text-sm">End</label>
    <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full border rounded px-2 py-1 bg-white" />
  </div>
  <div>
    <label class="text-sm">Bulan</label>
    <select id="filterBulan" name="bulan" class="w-full border rounded px-2 py-1 bg-white">
      <option value="">Semua</option>
      @php($bulanList = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'])
      @foreach($bulanList as $b)
        <option value="{{ strtoupper($b) }}" {{ strtoupper(request('bulan')) === strtoupper($b) ? 'selected' : '' }}>{{ $b }}</option>
      @endforeach
    </select>
  </div>
  <div>
    <label class="text-sm">Tahun</label>
    @php($yearNow = (int)date('Y'))
  <select id="filterTahun" name="tahun" class="w-full border rounded px-2 py-1 bg-white">
      <option value="">Semua</option>
      @for($y = $yearNow+1; $y >= $yearNow-5; $y--)
        <option value="{{ $y }}" {{ (string)request('tahun') === (string)$y ? 'selected' : '' }}>{{ $y }}</option>
      @endfor
    </select>
  </div>
  <div class="md:col-span-6">
    <button class="px-3 py-2 rounded bg-blue-600 text-white">Filter</button>
  </div>
</form>
@push('scripts')
<script>
  (function(){
    const kebunSel = document.getElementById('filterKebun');
    const divisiSel = document.getElementById('filterDivisi');
  const bulanSel = document.getElementById('filterBulan');
  const tahunSel = document.getElementById('filterTahun');
    const selectedKebun = @json(request('kebun'));
    const selectedDivisi = @json(request('divisi'));

    async function fetchJSON(url){
      const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      if(!res.ok) return [];
      return res.json();
    }

    function fillOptions(select, items, selected){
      // clear except first
      while(select.options.length > 1) select.remove(1);
      items.forEach(v => {
        const opt = document.createElement('option');
        opt.value = v; opt.textContent = v;
        if (selected && String(selected).toUpperCase() === String(v).toUpperCase()) opt.selected = true;
        select.appendChild(opt);
      });
    }

    async function loadKebun(){
      const list = await fetchJSON('{{ route('api.kebun-list', [], false) }}');
      fillOptions(kebunSel, list, selectedKebun);
    }

    async function loadDivisi(kebun){
      let url = kebun 
        ? '{{ route('api.divisi-list', ['kebun' => '___'], false) }}'.replace('___', encodeURIComponent(kebun))
        : '{{ route('api.divisi-list', [], false) }}';
      const list = await fetchJSON(url);
      fillOptions(divisiSel, list, selectedDivisi);
    }

    kebunSel.addEventListener('change', () => {
      // Clear divisi when kebun changed to avoid stale selection
      fillOptions(divisiSel, [], null);
      loadDivisi(kebunSel.value || null);
    });

    // init
    (async () => {
      await loadKebun();
      await loadDivisi(selectedKebun || null);
  // Ensure dropdowns retain selected values (already handled by blade); no extra JS needed
    })();
  })();
</script>
@endpush
