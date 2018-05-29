<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Import extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $data = json_decode(file_get_contents(base_path('folders.json')), true);

        $folders = $this->getFolders($data['folders']['lisa']['var']['www']);
        foreach ($folders as &$folder) {
            $folder['path'] = '/var/www/' . $folder['path'];
        }
        unset($folder);
//        var_dump($folders);

        $repositories = [];
        foreach (array_unique(array_column($folders, 'repository')) as $link) {
            if ($row = \DB::table('repositories')->where(compact('link'))->first()) {
                $repositories[$link] = $row->id;
                continue;
            }
            $repositories[$link] =
                \DB::table('repositories')->insertGetId(compact('link'));
        }

        $ports = [];
        $server_id = 1;
        foreach (array_unique(array_column($folders, 'port')) as $port) {

            if ($row = \DB::table('ports')->where(compact('port', 'server_id'))->first()) {
                $ports[$port] = $row->id;
                continue;
            }

            $ports[$port] = \DB::table('ports')->insertGetId(compact('port', 'server_id'));
        }

        foreach ($folders as $folder) {
            $row = [
                'path'          => $folder['path'],
                'delete'        => $folder['delete'] ?? 0,
                'port_id'       => empty($folder['port']) ? null : $ports[$folder['port']],
                'repository_id' => empty($folder['repository']) ? null : $repositories[$folder['repository']],
                'server_id'     => $server_id,
            ];

            if (\DB::table('folders')->where($row)->first()) {
                continue;
            }

            \DB::table('folders')->insertGetId($row);
        }

        return;
    }

    public function getFolders(array $array): array
    {
        $folders = [];
        foreach ($array as $folder => $subFolders) {
            if ('.' === $folder) {
                $folderData = $this->parseData($subFolders);
                $folders[] = $folderData;
                continue;
            }
            foreach ($this->getFolders($subFolders) as $subFolder) {
                if (isset($subFolder['path'])) {
                    $subFolder['path'] = $folder . '/' . $subFolder['path'];
                } else {
                    $subFolder['path'] = $folder;
                }
                $folders[] = $subFolder;
            }
        }

        return $folders;
    }

    public function parseData(string $comment): array
    {
        $data = [];

        $parts = explode(' ', $comment);
        foreach ($parts as $part) {
            if ('[MAY_BE_DELETED]' === $part) {
                $data['delete'] = true;
                continue;
            }
            if (preg_match('/\[port:(\d+)\]/ui', $part, $match)) {
                $data['port'] = $match[1];
                continue;
            }
            if (in_array(substr($part, 0, 14), ['https://bitbuc', 'https://gitlab'])) {
                $data['repository'] = $part;
                continue;
            }

            if (isset($data['repository'])) {
                $data['comment'] = ($data['comment'] ?? '') . ' ' . $part;
            } else {
                $data['name'] = ($data['name'] ?? '') . ' ' . $part;
            }
        }

        return $data;
    }
}
