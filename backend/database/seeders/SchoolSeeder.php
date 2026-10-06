<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\DepEdSchoolImporter;

class SchoolSeeder extends Seeder
{
    public function run(): void
    {
        $file = database_path(
            'data/deped_schools.csv'
        );

        if (!file_exists($file)) {

            $this->command->error(
                "DepEd CSV file not found:"
            );

            $this->command->error(
                $file
            );

            $this->command->newLine();

            $this->command->info(
                'Place the DepEd CSV file here:'
            );

            $this->command->info(
                'database/data/deped_schools.csv'
            );

            return;
        }

        $this->command->info(
            'Importing DepEd schools...'
        );

        $importer = app(
            DepEdSchoolImporter::class
        );

        $stats = $importer->import($file);

        $this->command->newLine();

        $this->command->table(
            [
                'Total Rows',
                'Created',
                'Updated',
                'Skipped',
            ],
            [[
                $stats['total'],
                $stats['created'],
                $stats['updated'],
                $stats['skipped'],
            ]]
        );
    }
}