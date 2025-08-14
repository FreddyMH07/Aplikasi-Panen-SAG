<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TableColumn extends Model
{
    use HasFactory;

    protected $fillable = [
        'table_name',
        'column_name',
        'column_label',
        'column_type',
        'is_visible',
        'is_required',
        'sort_order',
        'validation_rules'
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'is_required' => 'boolean',
        'sort_order' => 'integer',
        'validation_rules' => 'array'
    ];

    public static function getVisibleColumns($tableName)
    {
        return self::where('table_name', $tableName)
                   ->where('is_visible', true)
                   ->orderBy('sort_order')
                   ->get();
    }
}
