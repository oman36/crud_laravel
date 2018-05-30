<?php

namespace App\FormGenerator;

use App\DataBaseSettings\DataBaseSettings;

/**
 * Class FormGenerator.
 */
class FormGenerator
{
    public const FIELD_TYPE_TO_HTML = [
        'int'       => 'number',
        'tinyint'   => 'radio',
        'varchar'   => 'text',
        'text'      => 'textarea',
        'date'      => 'text',
        'datetime'  => 'text',
        'timestamp' => 'text',
    ];

    public function generateForTable(string $table): array
    {
        $fields = [];
        $dataBaseSettings = new DataBaseSettings();
        $relations = $dataBaseSettings->getRelationsForTable($table);
        foreach ($this->getColumnsInfo($table) as $i => $field) {
            if ('id' === $field->Field) {
                $fields[$i] = [
                    'name'     => 'id',
                    'html'     => 'hidden',
                    'required' => false,
                    'max'      => -1,
                    'value'    => null,
                ];
                continue;
            }

            $fields[$i] = [
                'name'     => $field->Field,
                'html'     => self::FIELD_TYPE_TO_HTML[$field->Type->type],
                'max'      => $field->Type->length ?? -1,
                'min'      => -1,
                'required' => 'No' === $field->Null && null === $field->Default &&
                    !(false === strpos($field->Extra, 'auto_increment')),
                'value'    => $field->Default,
            ];

            if ('int' === $field->Type->type) {
                $fields[$i]['max'] = 2147483647;
                if (false !== strpos($field->Type->params ?? '', 'unsigned')) {
                    $fields[$i]['max'] *= 2;
                    $fields[$i]['min'] = 0;
                } else {
                    $fields[$i]['min'] = -$fields[$i]['max'];
                }
            }

            if (!($relations[$field->Field] ?? null)) {
                continue;
            }

            $nameColumns = implode('`,`', $relations[$field->Field]['nameColumns']);
            $select = \DB::table($relations[$field->Field]['table'])
                ->select('id', \DB::raw("CONCAT(`{$nameColumns}`) as `name`"))
                ->get();

            $fields[$i]['options'] = array_combine(
                $select->pluck('id')->toArray(),
                $select->pluck('name')->toArray()
            );
            $fields[$i]['html'] = 'select';
        }

        return $fields;
    }

    public function getColumnsInfo(string $table): array
    {
        $fieldsData = \DB::select('show fields from ' . $table);

        foreach ($fieldsData as $field) {
            preg_match('/^([^(]+)\s*(?:\((\d+)\))?\s*(.+)?/ui', $field->Type, $typeData);
            $field->Type = (object) [
                'type'   => $typeData[1] ?? $field->Type,
                'length' => $typeData[2] ?? null,
                'params' => $typeData[3] ?? '',
            ];
        }

        return $fieldsData;
    }
}
