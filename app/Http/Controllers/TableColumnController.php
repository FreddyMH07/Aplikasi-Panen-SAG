<?php

namespace App\Http\Controllers;

use App\Models\TableColumn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class TableColumnController extends Controller
{
    public function index()
    {
        // Table list for filtering (distinct table_name values)
        $tables = TableColumn::select('table_name')->distinct()->orderBy('table_name')->pluck('table_name');
        return view('table-columns.index', compact('tables'));
    }

    public function getData(Request $request)
    {
        $query = TableColumn::query();
        if ($request->filled('table_name')) {
            $query->where('table_name', $request->get('table_name'));
        }
        // Simple server-side DataTables (no heavy search yet)
        $columns = ['table_name','column_name','column_label','column_type','is_visible','is_required','sort_order'];
        $draw = (int)$request->get('draw',1);
        $start = (int)$request->get('start',0);
        $length = (int)$request->get('length',25);
        $searchValue = $request->input('search.value');
        if ($searchValue) {
            $query->where(function($q) use ($searchValue){
                $q->where('column_name','like',"%$searchValue%")
                  ->orWhere('column_label','like',"%$searchValue%")
                  ->orWhere('table_name','like',"%$searchValue%");
            });
        }
        $recordsTotal = TableColumn::count();
        $recordsFiltered = (clone $query)->count();
        $orderIndex = $request->input('order.0.column');
        $orderDir = $request->input('order.0.dir','asc');
        if ($orderIndex !== null && isset($columns[$orderIndex])) {
            $query->orderBy($columns[$orderIndex], $orderDir === 'desc' ? 'desc' : 'asc');
        } else {
            $query->orderBy('table_name')->orderBy('sort_order');
        }
        $rows = $query->skip($start)->take($length)->get()->map(function($r){
            return [
                'id' => $r->id,
                'table_name' => $r->table_name,
                'column_name' => $r->column_name,
                'column_label' => $r->column_label,
                'column_type' => $r->column_type,
                'is_visible' => $r->is_visible,
                'is_required' => $r->is_required,
                'sort_order' => $r->sort_order,
                'actions' => '<div class="flex space-x-2">'
                    .'<button onclick="editColumn(' . $r->id . ')" class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit"></i></button>'
                    .'<button onclick="deleteColumn(' . $r->id . ')" class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>'
                    .'</div>'
            ];
        });
        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $rows
        ]);
    }

    public function create()
    {
        $existingTables = TableColumn::select('table_name')->distinct()->orderBy('table_name')->pluck('table_name')->toArray();
        // Also try to include actual DB tables (optional)
        try {
            $dbTables = [];
            foreach (Schema::getAllTables() as $t) { // For pgsql not standard; fallback skip
                $dbTables[] = is_array($t) ? reset($t) : (property_exists($t,'Tables_in_public') ? $t->Tables_in_public : null);
            }
            $dbTables = array_filter($dbTables);
        } catch (\Throwable $e) { $dbTables = []; }
        $tableSuggestions = array_unique(array_merge($existingTables, $dbTables));
        sort($tableSuggestions);
        return view('table-columns.create', compact('tableSuggestions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'table_name' => 'required|string|max:64',
            'column_name' => 'required|string|max:64',
            'column_label' => 'required|string|max:128',
            'column_type' => 'required|string|max:32',
            'is_visible' => 'sometimes|boolean',
            'is_required' => 'sometimes|boolean',
            'sort_order' => 'nullable|integer|min:0'
        ]);
        $data['is_visible'] = $request->boolean('is_visible');
        $data['is_required'] = $request->boolean('is_required');
        TableColumn::updateOrCreate([
            'table_name' => $data['table_name'],
            'column_name' => $data['column_name']
        ], $data);
        return redirect()->route('table-columns.index')->with('success','Kolom berhasil disimpan');
    }

    public function edit($id)
    {
        $column = TableColumn::findOrFail($id);
        return view('table-columns.edit', compact('column'));
    }

    public function update(Request $request, $id)
    {
        $column = TableColumn::findOrFail($id);
        $data = $request->validate([
            'column_label' => 'required|string|max:128',
            'column_type' => 'required|string|max:32',
            'is_visible' => 'sometimes|boolean',
            'is_required' => 'sometimes|boolean',
            'sort_order' => 'nullable|integer|min:0'
        ]);
        $data['is_visible'] = $request->boolean('is_visible');
        $data['is_required'] = $request->boolean('is_required');
        $column->update($data);
        return redirect()->route('table-columns.index')->with('success','Kolom berhasil diperbarui');
    }

    public function destroy($id)
    {
        $column = TableColumn::findOrFail($id);
        $column->delete();
        return response()->json(['success'=>true]);
    }
}
