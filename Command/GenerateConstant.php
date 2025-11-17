<?php

namespace Rezky\LaravelResponseFormatter\Command;

use Illuminate\Console\Command;
use Rezky\LaravelResponseFormatter\Models\ResponseRemark;
use Illuminate\Support\Facades\Schema; // Import Schema facade

class GenerateConstant extends Command
{
    /**
     * @var string
     */
    protected string $prefix = "CONST";

    protected string $filePath = "";

    /**
     * Mode generasi: 'const' (menyisipkan ke file) atau 'enum' (membuat file enum baru).
     * @var string
     */
    protected string $generationMode = 'enum';

    /**
     * Namespace untuk enum yang akan digenerate (hanya digunakan jika $generationMode = 'enum').
     * @var string
     */
    protected string $targetNamespace = '';

    /**
     * Nama kelas Enum yang akan digenerate (hanya digunakan jika $generationMode = 'enum').
     * @var string
     */
    protected string $targetEnumName = '';


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // Tambahkan flag --use-database ke parent
    protected $signature = 'const:generate {--use-database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'generate const list';

    /**
     * @var array
     * Hasil akhir dari konstanta yang akan digenerate
     */
    protected array $consts = [];

    /**
     * @var array
     * Data dari config file (di-set oleh child class)
     */
    protected array $configData = [];

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
     * Membuat string definisi const.
     * @param $codeName
     * @param $codeValue
     * @return string
     */
    private function createConst(&$codeName, $codeValue)
    {
        //check is has prefix
        $codeName = strtoupper($codeName);
        if (!str_starts_with($codeName, $this->prefix . '_')) {
            $codeName = $this->prefix . '_' . $codeName;
        }

        return "\tconst {$codeName} = '{$codeValue}';\n";
    }

    /**
     * Membuat string definisi case enum.
     * @param $caseName
     * @param $caseValue
     * @return string
     */
    private function createEnumCase(&$caseName, $caseValue)
    {
        // Logika yang sama dengan createConst untuk konsistensi
        $caseName = strtoupper($caseName);
        if (!str_starts_with($caseName, $this->prefix . '_')) {
            $caseName = $this->prefix . '_' . $caseName;
        }

        // Pastikan nama kasus valid (meskipun logika di atas seharusnya sudah menanganinya)
        $caseName = preg_replace('/[^A-Za-z0-9_]/', '_', $caseName);

        return "\tcase {$caseName} = '{$caseValue}';\n";
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // 1. Tentukan sumber data
        // $this->option('use-database') akan bernilai true jika flag --use-database ada
        if ($this->option('use-database')) {
            $this->loadConfigFromDB();
        } else {
            $this->loadConfigFromChild();
        }

        // 2. Cek jika $this->consts kosong
        if (empty($this->consts)) {
            $this->warn("No constants to generate. Aborting.");
            return false;
        }

        // 3. Lanjutkan ke logika generasi
        if ($this->generationMode === 'enum') {
            return $this->handleEnumGeneration();
        }

        return $this->handleConstInjection();
    }

    /**
     * Menangani logika lama: menyisipkan const ke dalam file yang ada.
     * @return bool
     */
    private function handleConstInjection()
    {
        $constStartRemark = "/** {$this->prefix} LIST HERE */";
        $constEndRemark = "/** END {$this->prefix} LIST HERE */";
        $this->info("find start line " . $constStartRemark);
        $this->info("find end line " . $constEndRemark);

        $scriptFilePath = $this->filePath;
        if (!file_exists($scriptFilePath)) {
            $this->error("File not found: {$scriptFilePath}");
            return false;
        }

        $scriptFile = file($scriptFilePath);

        // get code put location
        $lineConstStart = -1;
        $lineConstEnd = -1;
        foreach ($scriptFile as $line => $lineContent) {
            if (strpos($lineContent, $constStartRemark) > -1) {
                $lineConstStart = $line;
            } else if (strpos($lineContent, $constEndRemark) > -1) {
                $lineConstEnd = $line;
            }

            if ($lineConstStart != -1 && $lineConstEnd != -1) {
                break;
            }
        }

        if ($lineConstStart === -1 || $lineConstEnd === -1) {
            $this->error("Could not find start/end remarks in file: {$scriptFilePath}");
            return false;
        }

        $scriptFileHead = array_slice($scriptFile, 0, $lineConstStart + 1);
        $scriptFileFooter = array_slice($scriptFile, $lineConstEnd);

        $scriptFileCodeConsts = [];
        $scriptFileCodeConsts[] = "\n";

        $outputTable = [];
        foreach ($this->consts as $constName => $constValue) {
            $scriptFileCodeConsts[] = $this->createConst($constName, $constValue);
            $outputTable[] = [$constName, $constValue];
        }

        $this->table(["Constant Name", "Value"], $outputTable);

        $scriptFile = array_merge(array_values($scriptFileHead), array_values($scriptFileCodeConsts), array_values($scriptFileFooter));

        $fp = fopen($scriptFilePath, 'w');
        fwrite($fp, implode("", $scriptFile));
        fclose($fp);
        $this->info("Successfully updated constants in: {$scriptFilePath}");
        return true;
    }

    /**
     * Menangani logika baru: men-generate file enum string.
     * @return bool
     */
    private function handleEnumGeneration()
    {
        if (empty($this->targetNamespace)) {
            $this->error('Properti targetNamespace belum diatur di kelas turunan.');
            return false;
        }
        if (empty($this->targetEnumName)) {
            $this->error('Properti targetEnumName belum diatur di kelas turunan.');
            return false;
        }

        $this->info("Generating enum {$this->targetEnumName} in namespace {$this->targetNamespace}");

        $fileContent = "<?php\n\n";
        $fileContent .= "namespace {$this->targetNamespace};\n\n";
        $fileContent .= "/**\n";
        $fileContent .= " * Enum ini digenerate secara otomatis oleh " . static::class . "\n";
        $fileContent .= " * Jangan diedit secara manual.\n";
        $fileContent .= " */\n";
        $fileContent .= "enum {$this->targetEnumName}: string\n";
        $fileContent .= "{\n";

        $outputTable = [];
        foreach ($this->consts as $constName => $constValue) {
            $fileContent .= $this->createEnumCase($constName, $constValue);
            $outputTable[] = [$constName, $constValue];
        }

        $fileContent .= "}\n";

        $this->table(["Enum Case", "Value"], $outputTable);

        try {
            // Tiru 'make:enum' dan gagal jika file sudah ada
            if (file_exists($this->filePath)) {
                $this->error("Enum already exists: " . $this->filePath);
                return false;
            }

            // Membuat direktori jika belum ada
            $directory = dirname($this->filePath);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            file_put_contents($this->filePath, $fileContent);
            $this->info("Successfully generated enum: {$this->filePath}");
            return true;
        } catch (\Exception $e) {
            $this->error("Failed to write enum file: " . $e->getMessage());
            return false;
        }
    }


    /**
     * Memuat data dari properti $configData (di-set oleh child class)
     */
    protected function loadConfigFromChild()
    {
        if (empty($this->configData)) {
            $this->error("Config data was not provided by the child class (config('code.code') is empty?).");
            $this->consts = [];
        } else {
            $this->info("Loading configuration from child class (config file)...");
            $this->consts = $this->configData;
        }
    }

    /**
     * Memuat data dari database 'response_remarks'
     * Ini adalah implementasi baru berdasarkan file migrasi Anda.
     */
    protected function loadConfigFromDB()
    {
        $this->info("Loading configuration from database (response_remarks table)...");
        $tableName = 'response_remarks';

        try {
            // Cek jika tabel ada sebelum query
            if (!Schema::hasTable($tableName)) {
                $this->error("Database table '{$tableName}' not found. Run migrations first.");
                $this->consts = [];
                return;
            }

            // Ambil semua remark di mana 'const_name' tidak null dan tidak kosong
            // Ini sesuai dengan migrasi Anda (const_name -> nama Enum Case, resp_code -> nilai Enum Case)
            $remarks = ResponseRemark::whereNotNull('const_name')
                                     ->where('const_name', '!=', '')
                                     ->get();

            if ($remarks->isEmpty()) {
                $this->warn("No constants found in database (table '{$tableName}' has no rows with a valid 'const_name').");
                $this->consts = [];
                return;
            }

            // Ubah collection menjadi format [const_name => resp_code]
            // 'pluck' adalah cara Laravel yang efisien untuk ini.
            $this->consts = $remarks->pluck('resp_code', 'const_name')->toArray();

            $this->info("Successfully loaded " . $remarks->count() . " constants from database.");

        } catch (\Exception $e) {
            $this->error("Failed to load configuration from database: " . $e->getMessage());
            $this->consts = []; // Pastikan kosong jika gagal
        }
    }
}
