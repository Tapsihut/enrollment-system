<?php

namespace App\Services;

use App\Models\School;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DepEdSchoolImporter
{
    /**
     * Import DepEd schools from CSV.
     *
     * @return array{
     *     total:int,
     *     created:int,
     *     updated:int,
     *     skipped:int
     * }
     */
    public function import(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new RuntimeException(
                "CSV file not found: {$filePath}"
            );
        }

        $handle = fopen($filePath, 'r');

        if ($handle === false) {
            throw new RuntimeException(
                "Unable to open CSV file: {$filePath}"
            );
        }

        /*
         * Detect UTF-8 BOM.
         */
        $firstBytes = fread($handle, 3);

        if ($firstBytes !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $headers = fgetcsv($handle);

        if (!$headers) {
            fclose($handle);

            throw new RuntimeException(
                'The CSV file does not contain a header row.'
            );
        }

        $headers = array_map(function ($header) {
            return $this->normalizeHeader($header);
        }, $headers);

        $stats = [
            'total' => 0,
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
        ];

        DB::beginTransaction();

        try {

            while (($row = fgetcsv($handle)) !== false) {

                $stats['total']++;

                /*
                 * Skip completely empty rows.
                 */
                if ($this->emptyRow($row)) {
                    $stats['skipped']++;
                    continue;
                }

                /*
                 * Match CSV values to normalized headers.
                 */
                $data = [];

                foreach ($headers as $index => $header) {
                    $data[$header] = isset($row[$index])
                        ? trim($row[$index])
                        : null;
                }

                /*
                 * Extract fields.
                 */
                $schoolId = $this->value(
                    $data,
                    [
                        'school_id',
                        'schoolid',
                        'school_code',
                        'schoolcode',
                        'beis_school_id',
                        'beis_schoolid',
                    ]
                );

                $schoolName = $this->value(
                    $data,
                    [
                        'school_name',
                        'schoolname',
                        'name_of_school',
                        'nameofschool',
                        'school',
                    ]
                );

                /*
                 * School name is required.
                 */
                if (!$schoolName) {
                    $stats['skipped']++;
                    continue;
                }

                $schoolType = $this->value(
                    $data,
                    [
                        'school_type',
                        'schooltype',
                        'school_classification',
                        'classification',
                        'school_general_classification',
                        'schoolgeneralclassification',
                    ]
                );

                $region = $this->value(
                    $data,
                    [
                        'region',
                        'region_name',
                        'regionname',
                        'region_code',
                        'regioncode',
                    ]
                );

                $division = $this->value(
                    $data,
                    [
                        'division',
                        'division_name',
                        'divisionname',
                        'schools_division',
                        'schoolsdivision',
                    ]
                );

                $province = $this->value(
                    $data,
                    [
                        'province',
                        'province_name',
                        'provincename',
                    ]
                );

                $municipality = $this->value(
                    $data,
                    [
                        'municipality',
                        'municipality_name',
                        'municipalityname',
                        'city_municipality',
                        'citymunicipality',
                        'city',
                    ]
                );

                $address = $this->value(
                    $data,
                    [
                        'address',
                        'school_address',
                        'schooladdress',
                        'complete_address',
                        'completeaddress',
                    ]
                );

                /*
                 * Normalize School ID.
                 */
                $schoolId = $this->cleanSchoolId($schoolId);

                /*
                 * Duplicate handling.
                 *
                 * Primary key:
                 *     DepEd School ID
                 *
                 * Fallback:
                 *     school name + municipality
                 */
                if ($schoolId) {

                    $school = School::where(
                        'school_id',
                        $schoolId
                    )->first();

                } else {

                    $school = School::whereRaw(
                        'LOWER(TRIM(school_name)) = ?',
                        [mb_strtolower(trim($schoolName))]
                    )
                    ->where(function ($query) use ($municipality) {
                        if ($municipality) {
                            $query->whereRaw(
                                'LOWER(TRIM(municipality)) = ?',
                                [mb_strtolower(trim($municipality))]
                            );
                        } else {
                            $query->whereNull('municipality');
                        }
                    })
                    ->first();
                }

                $attributes = [
                    'school_id' => $schoolId,
                    'school_name' => $schoolName,
                    'school_type' => $schoolType,
                    'region' => $region,
                    'division' => $division,
                    'province' => $province,
                    'municipality' => $municipality,
                    'address' => $address,
                    'active' => true,
                ];

                if ($school) {

                    $school->update($attributes);

                    $stats['updated']++;

                } else {

                    School::create($attributes);

                    $stats['created']++;
                }
            }

            DB::commit();

        } catch (\Throwable $e) {

            DB::rollBack();

            fclose($handle);

            throw $e;
        }

        fclose($handle);

        return $stats;
    }

    /**
     * Normalize CSV header names.
     */
    private function normalizeHeader(?string $header): string
    {
        $header = trim((string) $header);

        $header = preg_replace(
            '/^\xEF\xBB\xBF/',
            '',
            $header
        );

        $header = mb_strtolower($header);

        $header = preg_replace(
            '/[^a-z0-9]+/',
            '_',
            $header
        );

        return trim($header, '_');
    }

    /**
     * Find first available value from aliases.
     */
    private function value(
        array $data,
        array $aliases
    ): ?string {
        foreach ($aliases as $alias) {

            if (
                array_key_exists($alias, $data) &&
                trim((string) $data[$alias]) !== ''
            ) {
                return trim($data[$alias]);
            }
        }

        return null;
    }

    /**
     * Normalize School ID.
     */
    private function cleanSchoolId(?string $schoolId): ?string
    {
        if (!$schoolId) {
            return null;
        }

        $schoolId = trim($schoolId);

        /*
         * Excel sometimes converts IDs such as:
         *
         * 101234
         *
         * into:
         *
         * 101234.0
         */
        if (preg_match('/^(\d+)\.0$/', $schoolId, $matches)) {
            $schoolId = $matches[1];
        }

        return $schoolId !== ''
            ? $schoolId
            : null;
    }

    /**
     * Check if CSV row is completely empty.
     */
    private function emptyRow(array $row): bool
    {
        foreach ($row as $value) {

            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }
}