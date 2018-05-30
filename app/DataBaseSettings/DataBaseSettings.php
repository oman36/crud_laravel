<?php

namespace App\DataBaseSettings;

/**
 * Class DataBaseSettings.
 */
class DataBaseSettings
{
    /** @var string */
    private $source;

    public function __construct()
    {
        $this->source = base_path('data.json');
    }

    private function getData(): array
    {
        return json_decode(file_get_contents($this->source), true);
    }

    public function getRelations(): array
    {
        return $this->getData();
    }

    public function getRelationsForTable(string $table): ?array
    {
        return $this->getRelations()[$table] ?? null;
    }
}
