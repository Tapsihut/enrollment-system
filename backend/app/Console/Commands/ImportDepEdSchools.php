<?php

namespace App\Console\Commands;

use App\Models\School;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportDepEdSchools extends Command
{
    protected $signature = 'schools:import
                            {file=database/data/caraga_schools.csv : CSV file path}
                            {--fresh : Delete existing DepEd schools before importing}';

    protected $description = 'Import DepEd schools from a CSV file';

    public function handle(): int
    {
        $file = base_path($this->argument('file'));

        if (!file_exists($file)) {
            $this->error('CSV file not found:');
            $this->line($file);

            return self::FAILURE;
        }

        $this->info('==========================================');
        $this->info('      DEPED SCHOOL CSV IMPORTER');
        $this->info('==========================================');
        $this->line('File: ' . $file);
        $this->newLine();

        /*
        |--------------------------------------------------------------------------
        | Fresh import
        |--------------------------------------------------------------------------
        */

        if ($this->option('fresh')) {
            if (!$this->confirm(
                'This will delete all existing DepEd schools. Continue?',
                false
            )) {
                $this->warn('Import cancelled.');

                return self::SUCCESS;
            }

            $deleted = School::where('source', 'DepEd')->delete();

            $this->warn("Deleted {$deleted} existing DepEd schools.");
            $this->newLine();
        }

        /*
        |--------------------------------------------------------------------------
        | Open CSV
        |--------------------------------------------------------------------------
        */

        $handle = fopen($file, 'r');

        if (!$handle) {
            $this->error('Unable to open CSV file.');

            return self::FAILURE;
        }

        /*
        |--------------------------------------------------------------------------
        | Read headers
        |--------------------------------------------------------------------------
        */

        $headers = fgetcsv($handle);

        if (!$headers) {
            fclose($handle);

            $this->error('CSV file is empty.');

            return self::FAILURE;
        }

        $headers = array_map(
            fn ($header) => $this->normalizeHeader($header),
            $headers
        );

        $this->info('Detected columns:');

        foreach ($headers as $header) {
            $this->line("  - {$header}");
        }

        $this->newLine();

        /*
        |--------------------------------------------------------------------------
        | Required columns
        |--------------------------------------------------------------------------
        */

        $required = [
            'region',
            'division',
            'district',
            'school_id',
            'school_name',
            'street_address',
            'municipality',
            'legislative_district',
            'barangay',
            'sector',
            'urban_rural_classification',
            'school_subclassification',
            'curricular_offering',
        ];

        $missing = array_diff($required, $headers);

        if (!empty($missing)) {
            fclose($handle);

            $this->error('Missing required CSV columns:');

            foreach ($missing as $column) {
                $this->line("  - {$column}");
            }

            $this->newLine();
            $this->error('Import stopped.');

            return self::FAILURE;
        }

        /*
        |--------------------------------------------------------------------------
        | Counters
        |--------------------------------------------------------------------------
        */

        $newSchools = 0;
        $updatedSchools = 0;
        $skippedRows = 0;
        $duplicateIds = 0;
        $rowNumber = 1;

        DB::beginTransaction();

        try {
            while (($row = fgetcsv($handle)) !== false) {
                $rowNumber++;

                /*
                |--------------------------------------------------------------------------
                | Skip completely empty rows
                |--------------------------------------------------------------------------
                */

                if ($this->isEmptyRow($row)) {
                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | Column mismatch
                |--------------------------------------------------------------------------
                */

                if (count($row) !== count($headers)) {
                    $skippedRows++;

                    $this->warn(
                        "Row {$rowNumber} skipped: column count mismatch."
                    );

                    continue;
                }

                $data = array_combine($headers, $row);

                if (!$data) {
                    $skippedRows++;
                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | Clean values
                |--------------------------------------------------------------------------
                */

                $region = $this->clean($data['region'] ?? null);
                $division = $this->clean($data['division'] ?? null);
                $district = $this->clean($data['district'] ?? null);
                $schoolId = $this->clean($data['school_id'] ?? null);
                $schoolName = $this->clean($data['school_name'] ?? null);

                /*
                |--------------------------------------------------------------------------
                | Only CARAGA
                |--------------------------------------------------------------------------
                */

                if (
                    !$region ||
                    strtoupper($region) !== 'CARAGA'
                ) {
                    $skippedRows++;
                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | School name required
                |--------------------------------------------------------------------------
                */

                if (!$schoolName) {
                    $skippedRows++;

                    $this->warn(
                        "Row {$rowNumber} skipped: school name is empty."
                    );

                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | Province
                |--------------------------------------------------------------------------
                */

                $province = $this->determineProvince($division);

                /*
                |--------------------------------------------------------------------------
                | Prepare school data
                |--------------------------------------------------------------------------
                */

                $schoolData = [
                    'school_name' => $schoolName,

                    'school_type' => $this->determineSchoolType(
                        $data['curricular_offering'] ?? null
                    ),

                    'region' => $region,

                    'division' => $division,

                    'district' => $district,

                    'province' => $province,

                    'municipality' => $this->clean(
                        $data['municipality'] ?? null
                    ),

                    'barangay' => $this->clean(
                        $data['barangay'] ?? null
                    ),

                    'address' => $this->clean(
                        $data['street_address'] ?? null
                    ),

                    'sector' => $this->clean(
                        $data['sector'] ?? null
                    ),

                    'school_subclassification' => $this->clean(
                        $data['school_subclassification'] ?? null
                    ),

                    'curricular_offering' => $this->clean(
                        $data['curricular_offering'] ?? null
                    ),

                    'active' => true,

                    'source' => 'DepEd',
                ];

                /*
                |--------------------------------------------------------------------------
                | Existing school by BEIS ID
                |--------------------------------------------------------------------------
                */

                if ($schoolId) {
                    $existing = School::where(
                        'school_id',
                        $schoolId
                    )->first();

                    if ($existing) {
                        $existing->update($schoolData);

                        $updatedSchools++;
                        $duplicateIds++;

                        continue;
                    }

                    $schoolData['school_id'] = $schoolId;

                    School::create($schoolData);

                    $newSchools++;

                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | No BEIS ID
                |--------------------------------------------------------------------------
                */

                $municipality = $schoolData['municipality'];

                $existing = School::whereRaw(
                    'LOWER(school_name) = ?',
                    [strtolower($schoolName)]
                )
                    ->where('region', $region)
                    ->where('municipality', $municipality)
                    ->first();

                if ($existing) {
                    $existing->update($schoolData);

                    $updatedSchools++;
                } else {
                    School::create($schoolData);

                    $newSchools++;
                }
            }

            DB::commit();

            fclose($handle);
        } catch (\Throwable $e) {
            DB::rollBack();

            fclose($handle);

            $this->error('==========================================');
            $this->error('IMPORT FAILED');
            $this->error('==========================================');
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        /*
        |--------------------------------------------------------------------------
        | Summary
        |--------------------------------------------------------------------------
        */

        $totalCaraga = School::where('region', 'CARAGA')
            ->where('source', 'DepEd')
            ->count();

        $this->newLine();

        $this->info('==========================================');
        $this->info('          IMPORT COMPLETED');
        $this->info('==========================================');

        $this->line(
            "New schools imported     : {$newSchools}"
        );

        $this->line(
            "Existing schools updated : {$updatedSchools}"
        );

        $this->line(
            "Duplicate BEIS IDs       : {$duplicateIds}"
        );

        $this->line(
            "Rows skipped             : {$skippedRows}"
        );

        $this->newLine();

        $this->info(
            "CARAGA schools in database: {$totalCaraga}"
        );

        $this->newLine();

        return self::SUCCESS;
    }

    /*
    |--------------------------------------------------------------------------
    | Normalize CSV headers
    |--------------------------------------------------------------------------
    */

    private function normalizeHeader(?string $header): string
    {
        $header = trim((string) $header);

        // Remove UTF-8 BOM
        $header = preg_replace('/^\xEF\xBB\xBF/', '', $header);

        $header = strtolower($header);

        $header = str_replace(
            [
                '?',
                '-',
                '/',
                '.',
                '(',
                ')',
            ],
            ' ',
            $header
        );

        $header = preg_replace('/\s+/', ' ', $header);

        $header = trim($header);

        return match ($header) {

            'region'
                => 'region',

            'division'
                => 'division',

            'district'
                => 'district',

            'beis school id'
                => 'school_id',

            'school id'
                => 'school_id',

            'school name'
                => 'school_name',

            'street address'
                => 'street_address',

            'municipality'
                => 'municipality',

            'legislative district'
                => 'legislative_district',

            'barangay'
                => 'barangay',

            'sector'
                => 'sector',

            'urban rural classification'
                => 'urban_rural_classification',

            'school subclassification'
                => 'school_subclassification',

            'modified curricural offering classification'
                => 'curricular_offering',

            'modified curricular offering classification'
                => 'curricular_offering',

            default
                => str_replace(' ', '_', $header),
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Clean CSV value
    |--------------------------------------------------------------------------
    */

    private function clean($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        return $value;
    }

    /*
    |--------------------------------------------------------------------------
    | Empty row
    |--------------------------------------------------------------------------
    */

    private function isEmptyRow(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Determine broad school type
    |--------------------------------------------------------------------------
    */

    private function determineSchoolType(?string $offering): string
    {
        $offering = strtoupper(
            trim((string) $offering)
        );

        if ($offering === '') {
            return 'Other';
        }

        if (
            str_contains($offering, 'PURELY ES')
        ) {
            return 'Elementary';
        }

        if (
            str_contains($offering, 'PURELY JHS')
        ) {
            return 'Junior High School';
        }

        if (
            str_contains($offering, 'PURELY SHS')
        ) {
            return 'Senior High School';
        }

        if (
            str_contains($offering, 'JHS WITH SHS')
        ) {
            return 'Junior/Senior High School';
        }

        if (
            str_contains($offering, 'ALL OFFERING') ||
            str_contains($offering, 'K TO 12')
        ) {
            return 'K-12';
        }

        if (
            str_contains($offering, 'ELEMENTARY')
        ) {
            return 'Elementary';
        }

        if (
            str_contains($offering, 'JHS')
        ) {
            return 'Junior High School';
        }

        if (
            str_contains($offering, 'SHS')
        ) {
            return 'Senior High School';
        }

        return 'Other';
    }

    /*
    |--------------------------------------------------------------------------
    | CARAGA province mapping
    |--------------------------------------------------------------------------
    */

    private function determineProvince(?string $division): ?string
    {
        $division = strtoupper(
            trim((string) $division)
        );

        return match (true) {

            str_contains($division, 'AGUSAN DEL NORTE')
                => 'Agusan del Norte',

            str_contains($division, 'CABADBARAN')
                => 'Agusan del Norte',

            str_contains($division, 'BUTUAN')
                => 'Agusan del Norte',

            str_contains($division, 'AGUSAN DEL SUR')
                => 'Agusan del Sur',

            str_contains($division, 'BAYUGAN')
                => 'Agusan del Sur',

            str_contains($division, 'DINAGAT')
                => 'Dinagat Islands',

            str_contains($division, 'SURIGAO DEL NORTE')
                => 'Surigao del Norte',

            str_contains($division, 'SURIGAO CITY')
                => 'Surigao del Norte',

            str_contains($division, 'SIARGAO')
                => 'Surigao del Norte',

            str_contains($division, 'SURIGAO DEL SUR')
                => 'Surigao del Sur',

            str_contains($division, 'BISLIG')
                => 'Surigao del Sur',

            str_contains($division, 'TANDAG')
                => 'Surigao del Sur',

            default => null,
        };
    }
}