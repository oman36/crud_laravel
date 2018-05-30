<?php

namespace App\Http\Controllers;

use App\DataBaseSettings\DataBaseSettings;
use App\FormGenerator\FormGenerator;
use Illuminate\Database\Schema\MySqlBuilder;
use Illuminate\Http\Request;

/**
 * Class DataBaseController.
 */
class DataBaseController
{
    protected $tables = [];

    public function __construct()
    {
        view()->share('breadcrumbs', []);
        /** @var  MySqlBuilder $connection */
        foreach (\DB::select('SHOW TABLES') as $table) {
            foreach ($table as $value) {
                $this->tables[] = $value;
                break;
            }
        }
        view()->share('tables', $this->tables);
        view()->share('active_table', '');
    }

    public function tables()
    {
        return view('tables', ['title' => 'Tables']);
    }

    public function rows(string $table, Request $request)
    {
        view()->share('breadcrumbs', [
            ['name' => $table, 'link' => '/' . $table, 'active' => false],
        ]);
        view()->share('title', 'Table ' . $table);

        $cols = array_map(
            function ($item) use ($table) {
                return "{$table}.{$item}";
            },
            \Schema::getColumnListing($table)
        );

        /** @var \Illuminate\Database\Query\Builder $query */
        $query = \DB::table($table);
        if ($data = (new DataBaseSettings())->getRelationsForTable($table)) {
            foreach ($data as $column => $relation) {
                unset($cols[array_search($table . '.' . $column, $cols, true)]);
                $query->join(
                    $relation['table'],
                    $table . '.' . $column,
                    '=',
                    $relation['table'] . '.' . $relation['dataColumn'],
                    'left'
                );
                $cols[] = sprintf(
                    '%s.%s as %s',
                    $relation['table'],
                    reset($relation['nameColumns']),
                    $relation['table'] . '_' . reset($relation['nameColumns'])
                );
            }
        }
        $query->select($cols);

        foreach ($request->get('filters', []) as $field => $filter) {
            $query->where(
                $field,
                'like',
                '%' . str_replace(['%', '_'], ['\%', '\_'], $filter) . '%'
            );
        }

        $pagination = $query
            ->orderBy($table . '.id', 'desc')
            ->paginate($request->get('per-page', 15));
        $rows = $pagination->items();

        return view('rows', [
            'active_table' => $table,
            'rows'         => $rows,
            'fields'       => array_map(function ($col) use ($table) {
                return preg_replace('/.+ as (.+)/i', '$1',
                    preg_replace("/{$table}\.(.+)/i", '$1', $col)
                );
            }, $cols),
            'pagination'   => $pagination,
            'formFields' => (new FormGenerator())->generateForTable($table)
        ]);
    }

    public function row(string $table, string $id, Request $request)
    {
        $isNew = 'new' === $id;

        view()->share('title', $isNew ? $table . ' new' : $table . ' update ' . $id);

        if (!$isNew) {
            $row = (array)\DB::table($table)->where('id', $id)->first();
        }

        $fieldsData = (new FormGenerator())->generateForTable($table);
        foreach ($fieldsData as &$field) {
            if ('id' === $field['name']) {
                $field['value'] = $isNew ? null : $id;
                continue;
            }
            if ($isNew) {
                continue;
            }
            $field['value'] = $row[$field['name']];
        }

        return view('row', [
            'active_table' => $table,
            'fields'       => $fieldsData,
        ]);
    }

    public function saveRow(string $table, string $id, Request $request)
    {
        $all = $request->all();
        $isNew = ($id !== ($all['id'] ?? null));
        unset($all['id']);

        if ($isNew) {
            \DB::table($table)->insert($all);
        } else {
            \DB::table($table)->where('id', $id)->update($all);
        }

        return redirect("/{$table}");
    }

    public function deleteRow(string $table, int $id)
    {
        \DB::table($table)->where('id', $id)->delete();

        return redirect("/{$table}");
    }
}
