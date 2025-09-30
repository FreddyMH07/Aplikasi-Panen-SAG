<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KpiController extends Controller
{
    public function index(Request $request)
    {
        return view('kpi.index');
    }

    // Rekonsiliasi Kebun ↔ PKS
    public function rekonsiliasi(Request $request)
    {
        $filters = $this->filters($request);
        $data = DB::table('panen_harians')
            ->select('tanggal_panen','kebun','divisi',
                DB::raw('COALESCE(timbang_pks_harian,0) AS timbang_pks_harian'),
                DB::raw('COALESCE(timbang_kebun_harian,0) AS timbang_kebun_harian'),
                DB::raw('(COALESCE(timbang_pks_harian,0) - COALESCE(timbang_kebun_harian,0)) AS selisih_kg'),
                DB::raw("CASE WHEN COALESCE(timbang_kebun_harian,0)=0 THEN 0 ELSE (COALESCE(timbang_pks_harian,0) - COALESCE(timbang_kebun_harian,0)) / NULLIF(timbang_kebun_harian,0) * 100 END AS loss_pct")
            )
            ->when($filters['kebun'], fn($q)=>$q->where('kebun',$filters['kebun']))
            ->when($filters['divisi'], fn($q)=>$q->where('divisi',$filters['divisi']))
            ->when($filters['start'], fn($q)=>$q->whereDate('tanggal_panen','>=',$filters['start']))
            ->when($filters['end'], fn($q)=>$q->whereDate('tanggal_panen','<=',$filters['end']))
            ->orderBy('tanggal_panen')
            ->get();
        return view('kpi.rekonsiliasi', compact('data','filters'));
    }

    // Restan Tracker
    public function restan(Request $request)
    {
        $filters = $this->filters($request);
        $ranking = DB::table('panen_harians')
            ->select('kebun','divisi',
                DB::raw('SUM(COALESCE(restant_jjg,0)) as restant_jjg'),
                DB::raw('SUM(COALESCE(jjg_panen_jjg,0)) as jjg_panen_jjg'),
                DB::raw("CASE WHEN SUM(COALESCE(jjg_panen_jjg,0))=0 THEN 0 ELSE SUM(COALESCE(restant_jjg,0))::float / NULLIF(SUM(COALESCE(jjg_panen_jjg,0)),0) * 100 END as restan_rate")
            )
            ->when($filters['kebun'], fn($q)=>$q->where('kebun',$filters['kebun']))
            ->when($filters['divisi'], fn($q)=>$q->where('divisi',$filters['divisi']))
            ->when($filters['start'], fn($q)=>$q->whereDate('tanggal_panen','>=',$filters['start']))
            ->when($filters['end'], fn($q)=>$q->whereDate('tanggal_panen','<=',$filters['end']))
            ->groupBy('kebun','divisi')
            ->orderByDesc('restan_rate')
            ->get();
        return view('kpi.restan', compact('ranking','filters'));
    }

    // Budget Harian & Bulanan
    public function budget(Request $request)
    {
        $filters = $this->filters($request);
        $harian = DB::table('panen_harians')
            ->select('tanggal_panen','kebun','divisi',
                DB::raw('COALESCE(tonase_panen_kg,0) as tonase_panen_kg'),
                DB::raw('COALESCE(budget_harian,0) as budget_harian'),
                DB::raw('COALESCE(tonase_panen_kg,0)-COALESCE(budget_harian,0) as var_budget_harian_kg')
            )
            ->when($filters['kebun'], fn($q)=>$q->where('kebun',$filters['kebun']))
            ->when($filters['divisi'], fn($q)=>$q->where('divisi',$filters['divisi']))
            ->when($filters['start'], fn($q)=>$q->whereDate('tanggal_panen','>=',$filters['start']))
            ->when($filters['end'], fn($q)=>$q->whereDate('tanggal_panen','<=',$filters['end']))
            ->orderBy('tanggal_panen')
            ->get();

        $bulanan = DB::table('panen_harians as ph')
            ->join('master_data as md', function($j){
                $j->on('md.kebun','=','ph.kebun')
                  ->on('md.divisi','=','ph.divisi')
                  ->on('md.tahun','=','ph.tahun')
                  ->on('md.bulan','=','ph.bulan');
            })
            ->select('ph.kebun','ph.divisi','ph.bulan','ph.tahun',
                DB::raw('EXTRACT(MONTH FROM MIN(ph.tanggal_panen))::int as month_num'),
                DB::raw('SUM(COALESCE(ph.tonase_panen_kg,0)) as actual_kg'),
                DB::raw('COALESCE(SUM(md.budget_alokasi),0) as budget_kg'),
                DB::raw('SUM(COALESCE(ph.tonase_panen_kg,0)) - COALESCE(SUM(md.budget_alokasi),0) as variance_kg')
            )
            ->when($filters['kebun'], fn($q)=>$q->where('ph.kebun',$filters['kebun']))
            ->when($filters['divisi'], fn($q)=>$q->where('ph.divisi',$filters['divisi']))
            ->when($filters['year'], fn($q)=>$q->where('ph.tahun',$filters['year']))
            ->when($filters['month'], fn($q)=>$q->whereRaw("EXTRACT(MONTH FROM ph.tanggal_panen)::int = ?", [$this->monthToNumber($filters['month'])]))
            ->groupBy('ph.kebun','ph.divisi','ph.bulan','ph.tahun')
            ->orderBy('ph.tahun')
            ->orderBy('month_num')
            ->get();

        return view('kpi.budget', compact('harian','bulanan','filters'));
    }

    // Produktivitas Tenaga Kerja
    public function produktifitas(Request $request)
    {
        $filters = $this->filters($request);
        $data = DB::table('panen_harians')
            ->select('kebun','divisi',
                DB::raw('AVG(COALESCE(output_kg_hk,0)) as avg_output_kg_hk'),
                DB::raw('AVG(COALESCE(output_ha_hk,0)) as avg_output_ha_hk')
            )
            ->when($filters['kebun'], fn($q)=>$q->where('kebun',$filters['kebun']))
            ->when($filters['divisi'], fn($q)=>$q->where('divisi',$filters['divisi']))
            ->when($filters['start'], fn($q)=>$q->whereDate('tanggal_panen','>=',$filters['start']))
            ->when($filters['end'], fn($q)=>$q->whereDate('tanggal_panen','<=',$filters['end']))
            ->groupBy('kebun','divisi')
            ->orderBy('kebun')->orderBy('divisi')
            ->get();
        return view('kpi.produktifitas', compact('data','filters'));
    }

    // Quality Bias AKP & BJR
    public function quality(Request $request)
    {
        $filters = $this->filters($request);
        $data = DB::table('panen_harians as ph')
            ->leftJoin('master_data as md', function($j){
                $j->on('md.kebun','=','ph.kebun')
                  ->on('md.divisi','=','ph.divisi')
                  ->on('md.tahun','=','ph.tahun')
                  ->on('md.bulan','=','ph.bulan');
            })
            ->select('ph.tanggal_panen','ph.kebun','ph.divisi',
                DB::raw('COALESCE(ph.akp_panen,0) as akp_panen'),
                DB::raw('CASE WHEN COALESCE(ph.luas_panen_ha,0)*COALESCE(md.sph_panen,0)=0 THEN 0 ELSE COALESCE(ph.jjg_panen_jjg,0) / NULLIF(COALESCE(ph.luas_panen_ha,0)*COALESCE(md.sph_panen,0),0) END as akp_calc'),
                DB::raw('COALESCE(ph.bjr_hari_ini,0) as bjr_hari_ini'),
                DB::raw('CASE WHEN COALESCE(ph.jjg_panen_jjg,0)=0 THEN 0 ELSE COALESCE(ph.timbang_kebun_harian,0) / NULLIF(COALESCE(ph.jjg_panen_jjg,0),0) END as bjr_calc'),
                DB::raw('(COALESCE(ph.akp_panen,0) - (CASE WHEN COALESCE(ph.luas_panen_ha,0)*COALESCE(md.sph_panen,0)=0 THEN 0 ELSE COALESCE(ph.jjg_panen_jjg,0) / NULLIF(COALESCE(ph.luas_panen_ha,0)*COALESCE(md.sph_panen,0),0) END)) as akp_bias'),
                DB::raw('(COALESCE(ph.bjr_hari_ini,0) - (CASE WHEN COALESCE(ph.jjg_panen_jjg,0)=0 THEN 0 ELSE COALESCE(ph.timbang_kebun_harian,0) / NULLIF(COALESCE(ph.jjg_panen_jjg,0),0) END)) as bjr_bias')
            )
            ->when($filters['kebun'], fn($q)=>$q->where('ph.kebun',$filters['kebun']))
            ->when($filters['divisi'], fn($q)=>$q->where('ph.divisi',$filters['divisi']))
            ->when($filters['start'], fn($q)=>$q->whereDate('ph.tanggal_panen','>=',$filters['start']))
            ->when($filters['end'], fn($q)=>$q->whereDate('ph.tanggal_panen','<=',$filters['end']))
            ->orderBy('ph.tanggal_panen')
            ->get();
        return view('kpi.quality', compact('data','filters'));
    }

    // Anomali 3-sigma
    public function anomali(Request $request)
    {
        $filters = $this->filters($request);
        $base = DB::table('panen_harians as ph')
            ->leftJoin('master_data as md', function($j){
                $j->on('md.kebun','=','ph.kebun')
                  ->on('md.divisi','=','ph.divisi')
                  ->on('md.tahun','=','ph.tahun')
                  ->on('md.bulan','=','ph.bulan');
            })
            ->select('ph.tanggal_panen','ph.kebun','ph.divisi',
                DB::raw('COALESCE(ph.refraksi_persen,0) as refraksi_persen'),
                DB::raw('COALESCE(ph.ketrek,0) as ketrek'),
                DB::raw("CASE WHEN COALESCE(ph.timbang_kebun_harian,0)=0 THEN 0 ELSE (COALESCE(ph.timbang_pks_harian,0)-COALESCE(ph.timbang_kebun_harian,0))/NULLIF(ph.timbang_kebun_harian,0)*100 END as loss_pct"),
                DB::raw('COALESCE(ph.restant_jjg,0) as restant_jjg'),
                DB::raw('COALESCE(ph.jjg_panen_jjg,0) as jjg_panen_jjg'),
                DB::raw('COALESCE(ph.akp_panen,0) as akp_panen'),
                DB::raw('CASE WHEN COALESCE(ph.luas_panen_ha,0)*COALESCE(md.sph_panen,0)=0 THEN 0 ELSE COALESCE(ph.jjg_panen_jjg,0) / NULLIF(COALESCE(ph.luas_panen_ha,0)*COALESCE(md.sph_panen,0),0) END as akp_calc'),
                DB::raw('COALESCE(ph.bjr_hari_ini,0) as bjr_hari_ini'),
                DB::raw('CASE WHEN COALESCE(ph.jjg_panen_jjg,0)=0 THEN 0 ELSE COALESCE(ph.timbang_kebun_harian,0) / NULLIF(COALESCE(ph.jjg_panen_jjg,0),0) END as bjr_calc'),
                DB::raw('(COALESCE(ph.akp_panen,0) - (CASE WHEN COALESCE(ph.luas_panen_ha,0)*COALESCE(md.sph_panen,0)=0 THEN 0 ELSE COALESCE(ph.jjg_panen_jjg,0) / NULLIF(COALESCE(ph.luas_panen_ha,0)*COALESCE(md.sph_panen,0),0) END)) as akp_bias'),
                DB::raw('(COALESCE(ph.bjr_hari_ini,0) - (CASE WHEN COALESCE(ph.jjg_panen_jjg,0)=0 THEN 0 ELSE COALESCE(ph.timbang_kebun_harian,0) / NULLIF(COALESCE(ph.jjg_panen_jjg,0),0) END)) as bjr_bias')
            )
            ->when($filters['kebun'], fn($q)=>$q->where('ph.kebun',$filters['kebun']))
            ->when($filters['divisi'], fn($q)=>$q->where('ph.divisi',$filters['divisi']))
            ->when($filters['start'], fn($q)=>$q->whereDate('ph.tanggal_panen','>=',$filters['start']))
            ->when($filters['end'], fn($q)=>$q->whereDate('ph.tanggal_panen','<=',$filters['end']))
            ->orderBy('ph.tanggal_panen')
            ->get();

        // Compute 3-sigma thresholds per metric
        $metrics = ['refraksi_persen','ketrek','loss_pct','akp_bias','bjr_bias'];
        $stats = [];
        foreach ($metrics as $m) {
            $vals = $base->pluck($m)->filter(fn($v)=>$v !== null)->values();
            $n = $vals->count();
            if ($n > 1) {
                $mean = $vals->avg();
                $std = sqrt($vals->reduce(fn($c,$v)=>$c + pow($v - $mean, 2), 0) / ($n - 1));
                $stats[$m] = ['mean'=>$mean,'std'=>$std,'upper'=>$mean + 3*$std,'lower'=>$mean - 3*$std];
            } else {
                $stats[$m] = ['mean'=>0,'std'=>0,'upper'=>0,'lower'=>0];
            }
        }

        // Flag anomalies
        $flagged = $base->map(function($row) use ($stats, $metrics){
            $row = (array)$row;
            $row['flags'] = [];
            foreach ($metrics as $m) {
                $v = (float)$row[$m];
                if (($stats[$m]['std'] ?? 0) > 0 && ($v > $stats[$m]['upper'] || $v < $stats[$m]['lower'])) {
                    $row['flags'][] = $m;
                }
            }
            return $row;
        });

        return view('kpi.anomali', [
            'data' => $flagged,
            'stats' => $stats,
            'filters' => $filters,
        ]);
    }

    // Summary KPI
    public function summary(Request $request)
    {
        $filters = $this->filters($request);
        $agg = DB::table('panen_harians')
            ->when($filters['kebun'], fn($q)=>$q->where('kebun',$filters['kebun']))
            ->when($filters['divisi'], fn($q)=>$q->where('divisi',$filters['divisi']))
            ->when($filters['start'], fn($q)=>$q->whereDate('tanggal_panen','>=',$filters['start']))
            ->when($filters['end'], fn($q)=>$q->whereDate('tanggal_panen','<=',$filters['end']))
            // Apply month/year filters when provided
            ->when($filters['year'], fn($q)=>$q->whereRaw('EXTRACT(YEAR FROM tanggal_panen)::int = ?', [$filters['year']]))
            ->when($filters['month'], fn($q)=>$q->whereRaw('EXTRACT(MONTH FROM tanggal_panen)::int = ?', [$this->monthToNumber($filters['month'])]))
            ->select(
                DB::raw("CASE WHEN SUM(COALESCE(timbang_kebun_harian,0))=0 THEN 0 ELSE (SUM(COALESCE(timbang_pks_harian,0))-SUM(COALESCE(timbang_kebun_harian,0)))/NULLIF(SUM(COALESCE(timbang_kebun_harian,0)),0)*100 END as avg_loss_pct"),
                DB::raw("CASE WHEN SUM(COALESCE(jjg_panen_jjg,0))=0 THEN 0 ELSE SUM(COALESCE(restant_jjg,0))::float/NULLIF(SUM(COALESCE(jjg_panen_jjg,0)),0)*100 END as restan_rate_pct"),
                DB::raw('AVG(COALESCE(output_kg_hk,0)) as avg_output_kg_hk'),
                DB::raw('SUM(COALESCE(tonase_panen_kg,0)-COALESCE(budget_harian,0)) as total_var_budget_harian_kg')
            )
            ->first();
        return view('kpi.summary', compact('agg','filters'));
    }

    private function filters(Request $request): array
    {
        return [
            'kebun' => $request->get('kebun'),
            'divisi' => $request->get('divisi'),
            'start' => $request->get('start_date'),
            'end' => $request->get('end_date'),
            'year' => $request->get('tahun'),
            'month' => $request->get('bulan'),
        ];
    }

    private function monthToNumber(?string $bulan): ?int
    {
        if (!$bulan) return null;
        $map = [
            'JANUARI'=>1,'FEBRUARI'=>2,'MARET'=>3,'APRIL'=>4,'MEI'=>5,'JUNI'=>6,
            'JULI'=>7,'AGUSTUS'=>8,'SEPTEMBER'=>9,'OKTOBER'=>10,'NOVEMBER'=>11,'DESEMBER'=>12,
            'JANUARY'=>1,'FEBRUARY'=>2,'MARCH'=>3,'APRIL'=>4,'MAY'=>5,'JUNE'=>6,
            'JULY'=>7,'AUGUST'=>8,'SEPTEMBER'=>9,'OCTOBER'=>10,'NOVEMBER'=>11,'DECEMBER'=>12,
            'JAN'=>1,'FEB'=>2,'MAR'=>3,'APR'=>4,'MEI'=>5,'JUN'=>6,'JUL'=>7,'AGU'=>8,'SEP'=>9,'OKT'=>10,'NOV'=>11,'DES'=>12,
        ];
        $key = strtoupper(trim($bulan));
        return $map[$key] ?? (ctype_digit($key) ? (int)$key : null);
    }
}
