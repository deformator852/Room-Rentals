<?php

namespace App\Console\Commands;

use App\Models\Property;
use App\Models\Settlement;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ImportSettlementsCommand extends Command
{
    protected $signature = 'settlements:import
        {--url=https://raw.githubusercontent.com/gontsa/ua-osm-names-of-places/main/ua-name-places.csv : CSV URL}
        {--truncate : Clear settlements table before import}';

    protected $description = 'Import Ukrainian settlements from public CSV dataset';

    public function handle(): int
    {
        $url = (string) $this->option('url');

        $this->info("Downloading settlements from: {$url}");

        $response = Http::timeout(120)->get($url);

        if (! $response->ok()) {
            $this->error('Failed to download dataset. HTTP status: ' . $response->status());

            return self::FAILURE;
        }

        if ($this->option('truncate')) {
            Property::query()->update(['settlement_id' => null]);
            Settlement::query()->delete();
            $this->warn('Settlements table cleared.');
        }

        $rows = preg_split("/\r\n|\n|\r/", trim($response->body()));

        if (! $rows || count($rows) < 2) {
            $this->error('Dataset is empty or malformed.');

            return self::FAILURE;
        }

        $headers = array_map(static fn ($h) => trim(mb_strtolower($h)), str_getcsv(array_shift($rows)));

        if (isset($headers[0])) {
            $headers[0] = preg_replace('/^\xEF\xBB\xBF/u', '', $headers[0]);
        }

        $map = array_flip($headers);

        $chunk = [];
        $processed = 0;

        foreach ($rows as $row) {
            if ($row === '') {
                continue;
            }

            $data = str_getcsv($row);
            if (count($data) < 2) {
                continue;
            }

            $name = $this->value($data, $map, ['name', 'name:uk']);
            if (! $name) {
                continue;
            }

            $katottgCode = $this->value($data, $map, ['katotth', 'katottg']);

            $chunk[] = [
                'name' => $name,
                'type' => $this->value($data, $map, ['place']),
                'region' => $this->value($data, $map, ['region']),
                'district' => $this->value($data, $map, ['rayon', 'district']),
                'community' => $this->value($data, $map, ['hromada', 'community']),
                'katottg_code' => $katottgCode ?: null,
                'lat' => null,
                'lon' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($chunk) >= 1000) {
                $this->flushChunk($chunk);
                $processed += count($chunk);
                $this->line("Processed: {$processed}");
                $chunk = [];
            }
        }

        if ($chunk !== []) {
            $this->flushChunk($chunk);
            $processed += count($chunk);
        }

        $this->info("Import completed. Rows processed: {$processed}");

        return self::SUCCESS;
    }

    private function flushChunk(array $chunk): void
    {
        Settlement::query()->upsert(
            $chunk,
            ['katottg_code'],
            ['name', 'type', 'region', 'district', 'community', 'updated_at']
        );
    }

    private function value(array $data, array $map, array $keys): ?string
    {
        foreach ($keys as $key) {
            if (! array_key_exists($key, $map)) {
                continue;
            }

            $value = trim((string) ($data[$map[$key]] ?? ''));

            if ($value !== '') {
                return $value;
            }
        }

        return null;
    }
}
