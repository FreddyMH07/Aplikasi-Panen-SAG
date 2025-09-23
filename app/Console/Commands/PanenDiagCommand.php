<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PanenDiagCommand extends Command
{
    protected $signature = 'panen:diag {--limit=3 : Sample row limit tiap tabel}';
    protected $description = 'Diagnostik ringkas tabel panen (counts & sample)';

    public function handle(): int
    {
        $limit = (int)$this->option('limit');
        $tables = [ 'master_data','panen_harians' ];
        $rows = [];
        foreach ($tables as $t) {
            try {
                $count = DB::table($t)->count();
                $sample = DB::table($t)->orderByDesc('id')->limit($limit)->get();
                $rows[] = [ 'table' => $t, 'count' => $count, 'recent_ids' => $sample->pluck('id')->implode(',') ];
                $this->line("\n=== $t (count: $count) ===");
                foreach ($sample as $s) { $this->line(json_encode($s)); }
            } catch (\Throwable $e) {
                $rows[] = [ 'table'=>$t, 'count'=>'ERR', 'recent_ids'=>'' ];
                $this->error("Error akses $t: ".$e->getMessage());
            }
        }
        $this->table(['Table','Count','Recent IDs'],$rows);
        return self::SUCCESS;
    }
}
