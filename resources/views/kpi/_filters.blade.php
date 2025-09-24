<form method="GET" class="mb-4 grid grid-cols-2 md:grid-cols-6 gap-2 items-end">
  <div>
    <label class="text-sm">Kebun</label>
    <input type="text" name="kebun" value="{{ request('kebun') }}" class="w-full border rounded px-2 py-1 bg-white dark:bg-gray-900" placeholder="All" />
  </div>
  <div>
    <label class="text-sm">Divisi</label>
    <input type="text" name="divisi" value="{{ request('divisi') }}" class="w-full border rounded px-2 py-1 bg-white dark:bg-gray-900" placeholder="All" />
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
