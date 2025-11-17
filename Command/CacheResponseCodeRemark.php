<?php

namespace Rezky\LaravelResponseFormatter\Command;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Rezky\LaravelResponseFormatter\Models\ResponseRemark;
use Rezky\LaravelResponseFormatter\Http\Response;

class CacheResponseCodeRemark extends Command
{
    /**
     * Nama dan signature dari console command.
     *
     * @var string
     */
    protected $signature = 'code:cache';

    /**
     * Deskripsi console command.
     *
     * @var string
     */
    protected $description = 'Cache all response codes and groups from database and config';

    protected $prefix = 'CODE';
    protected function generateConstName($codeName)
    {
        //check is has prefix
        $codeName = strtoupper($codeName);
        if (!str_starts_with($codeName, $this->prefix . '_')) {
            $codeName = $this->prefix . '_' . $codeName;
        }
        return $codeName;
    }
    /**
     * Jalankan console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Caching all response codes and groups...');

        // --- 1. Cache Codes ---
        $this->line('Processing codes...');

        // Ambil kode dari DB
        $dbCodes = [];
        try {
            $dbCodes = ResponseRemark::whereNotNull('const_name')
                ->pluck('resp_code', 'const_name')
                ->mapWithKeys(fn($resp_code, $const_name) => [$this->generateConstName($const_name) => $resp_code])
                ->all();
            $this->info('Loaded '. count($dbCodes) .' codes from database.');
        } catch (\Exception $e) {
            $this->warn('Could not load codes from database. Maybe migration has not been run? Error: ' . $e->getMessage());
        }

        // Gabungkan: 1. Default, 2. Config, 3. DB
        $codes = array_merge(
            Response::getDefaultCode(),
//            config('code.code', []), // Ambil dari config
            $dbCodes // Kode DB menimpa default/config
        );
        $finalCodes = array_change_key_case($codes, CASE_UPPER);

        Cache::forever('response_formatter.codes', $finalCodes);
        $this->info('Codes cached successfully.');

        // --- 2. Cache Groups ---
        $this->line('Processing groups...');

        // Ambil grup dari DB
        $dbGroups = [];
        try {
            $dbGroups = ResponseRemark::all()
                ->map(function (ResponseRemark $remark){
                    $remark->const_name = $this->generateConstName($remark->const_name);
                    return $remark;
                })
                ->groupBy('http_code')
                ->map(fn($remarks) => $remarks->pluck('const_name')->all())
                ->all();
            $this->info('Loaded '. count($dbGroups) .' HTTP code groups from database.');
        } catch (\Exception $e) {
            $this->warn('Could not load groups from database. Maybe migration has not been run? Error: ' . $e->getMessage());
        }


        // Mulai dengan grup default
        $finalGroups = Response::getDefaultGroup();

        // Gabungkan grup dari config
//        $configGroups = config("code.group", []);
//        foreach ($configGroups as $httpCode => $values) {
//            if (!is_array($values)) continue;
//            $finalGroups[$httpCode] = array_unique(array_merge($finalGroups[$httpCode] ?? [], $values));
//        }

        // Gabungkan grup dari DB
        foreach ($dbGroups as $httpCode => $values) {
            if (!is_array($values)) continue;
            $finalGroups[$httpCode] = array_unique(array_merge($finalGroups[$httpCode] ?? [], $values));
        }

        Cache::forever('response_formatter.groups', $finalGroups);
        $this->info('Groups cached successfully.');

        $this->info('Response codes and groups cached successfully.');
        return self::SUCCESS;
    }
}
