<?php

namespace App\Http\Controllers;

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
        return view('tables',  ['title' => 'Tables']);
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
        if ($data = ($this->getData()[$table] ?? [])) {
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
        ]);
    }

    public function row(string $table, string $id, Request $request)
    {
        $isNew = 'new' === $id;

        view()->share('title', $isNew ? $table . ' new' : $table . ' update '. $id);

        $fieldsData = \DB::select('show fields from ' . $table);

        $typeToHTML = [
            'int'       => 'number',
            'tinyint'   => 'radio',
            'varchar'   => 'text',
            'text'      => 'textarea',
            'date'      => 'text',
            'datetime'  => 'text',
            'timestamp' => 'text',
        ];
        $fields = [];

        if (!$isNew) {
            $row = (array)\DB::table($table)->where('id', $id)->first();
        }

        foreach ($fieldsData as $i => $field) {
            if ('id' === $field->Field) {
                $fields[$i] = [
                    'name'     => 'id',
                    'html'     => 'hidden',
                    'required' => false,
                    'max'      => -1,
                    'value'    => $isNew ? null : $id,
                ];
                continue;
            }
            preg_match('/^([^(]+)\s*(?:\((\d+)\))?\s*(.+)?/ui', $field->Type, $typeData);
            $fields[$i] = [
                'name'     => $field->Field,
                'html'     => $typeToHTML[$typeData[1]],
                'max'      => $typeData[2] ?? -1,
                'required' => 'No' === $field->Null && null === $field->Default &&
                    !(false === strpos($field->Extra, 'auto_increment')),
                'value'    => $isNew ? $field->Default : $row[$field->Field],
            ];

            $additionalData = $this->getData()[$table][$field->Field] ?? null;
            if (null === $additionalData) {
                continue;
            }

            $nameColumns = implode('`,`', $additionalData['nameColumns']);
            $select = \DB::table($additionalData['table'])
                ->select('id', \DB::raw("CONCAT(`{$nameColumns}`) as `name`"))
                ->get();

            $fields[$i]['options'] = array_combine(
                $select->pluck('id')->toArray(),
                $select->pluck('name')->toArray()
            );
            $fields[$i]['html'] = 'select';
        }

        return view('row', [
            'active_table' => $table,
            'fields'       => $fields,
        ]);
    }

    public function saveRow(string $table, string $id, Request $request)
    {
        $all = $request->all();
        dump($all);die();
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

    protected function getData(): array
    {
        return json_decode(@file_get_contents(base_path('data.json')), true);
    }
}
