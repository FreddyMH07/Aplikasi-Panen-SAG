<form method="GET" class="mb-4 grid grid-cols-2 md:grid-cols-6 gap-2 items-end">
  <div>
    <label class="text-sm">Kebun</label>
    <select id="filterKebun" name="kebun" class="w-full border rounded px-2 py-1 bg-white dark:bg-gray-900">
      <option value="">Semua</option>
    </select>
  </div>
  <div>
    <label class="text-sm">Divisi</label>
    <select id="filterDivisi" name="divisi" class="w-full border rounded px-2 py-1 bg-white dark:bg-gray-900">
      <option value="">Semua</option>
    </select>
  </div>
  <div>
    <label class="text-sm">Start</label>
    <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full border rounded px-2 py-1 bg-white dark:bg-gray-900" />
  </div>
  <div>
    <label class="text-sm">End</label>
    <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full border rounded px-2 py-1 bg-white dark:bg-gray-900" />
  </div>
  <div>
    <label class="text-sm">Bulan</label>
    <input type="text" name="bulan" value="{{ request('bulan') }}" class="w-full border rounded px-2 py-1 bg-white dark:bg-gray-900" placeholder="e.g. JANUARI" />
  </div>
  <div>
    <label class="text-sm">Tahun</label>
    <input type="number" name="tahun" value="{{ request('tahun') }}" class="w-full border rounded px-2 py-1 bg-white dark:bg-gray-900" />
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
      let url = kebun ? '{{ url('/api/divisi-list') }}/' + encodeURIComponent(kebun) : '{{ route('api.divisi-list', [], false) }}';
      const list = await fetchJSON(url);
      fillOptions(divisiSel, list, selectedDivisi);
    }

    kebunSel.addEventListener('change', () => {
      loadDivisi(kebunSel.value || null);
    });

    // init
    (async () => {
      await loadKebun();
      await loadDivisi(selectedKebun || null);
    })();
  })();
</script>
@endpush
