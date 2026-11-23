<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeFeature extends Command
{
    protected $signature = 'make:feature
                            {name : Feature name, e.g. Category or BlogPost}
                            {--plural= : Optional plural class name, e.g. Categories}
                            {--table= : Optional database table name, e.g. categories}
                            {--force : Overwrite files that already exist}';

    protected $description = 'Generate a feature scaffold using the project custom stubs';

    public function handle(): int
    {
        $rawName = trim((string) $this->argument('name'));

        if (! preg_match('/^[A-Za-z][A-Za-z0-9 _-]*$/', $rawName)) {
            $this->error('Feature name must start with a Latin letter and contain only Latin letters, numbers, spaces, _ or -.');

            return self::FAILURE;
        }

        $feature = Str::studly($rawName);

        if (! preg_match('/^[A-Z][A-Za-z0-9]*$/', $feature)) {
            $this->error('The normalized feature name is not a valid PHP class name.');

            return self::FAILURE;
        }

        $rawPlural = trim((string) ($this->option('plural') ?: Str::plural($feature)));

        if (! preg_match('/^[A-Za-z][A-Za-z0-9 _-]*$/', $rawPlural)) {
            $this->error('Plural name must start with a Latin letter and contain only Latin letters, numbers, spaces, _ or -.');

            return self::FAILURE;
        }

        $plural = Str::studly($rawPlural);
        $table = trim((string) ($this->option('table') ?: Str::snake($plural)));

        if (! preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $table)) {
            $this->error('Table name must be a valid SQL identifier.');

            return self::FAILURE;
        }

        $replacements = [
            '{{Features}}' => $plural,
            '{{features}}' => Str::camel($plural),
            '{{features_snake}}' => Str::snake($plural),
            '{{Feature}}' => $feature,
            '{{feature}}' => Str::camel($feature),
            '{{feature_snake}}' => Str::snake($feature),
            '{{table}}' => $table,
        ];

        $files = [
            'controller.stub' => app_path("Http/Controllers/V1/{$feature}/{$feature}Controller.php"),
            'service-interface.stub' => app_path("Services/V1/{$feature}/Interfaces/{$feature}ServiceInterface.php"),
            'service.stub' => app_path("Services/V1/{$feature}/{$feature}Service.php"),
            'model.stub' => app_path("Models/V1/{$feature}/{$feature}.php"),
            'filter.stub' => app_path("ModelFilters/V1/{$feature}/{$plural}Filter.php"),
            'store-request.stub' => app_path("Http/Requests/V1/{$feature}/{$feature}StoreRequest.php"),
            'update-request.stub' => app_path("Http/Requests/V1/{$feature}/{$feature}UpdateRequest.php"),
            'admin-collection.stub' => app_path("Http/Resources/V1/{$feature}/Admin/{$feature}Collection.php"),
            'admin-resource.stub' => app_path("Http/Resources/V1/{$feature}/Admin/{$feature}Resource.php"),
            'admin-summary-resource.stub' => app_path("Http/Resources/V1/{$feature}/Admin/{$feature}SummaryResource.php"),
            'public-collection.stub' => app_path("Http/Resources/V1/{$feature}/Public/Public{$feature}Collection.php"),
            'public-resource.stub' => app_path("Http/Resources/V1/{$feature}/Public/Public{$feature}Resource.php"),
            'public-summary-resource.stub' => app_path("Http/Resources/V1/{$feature}/Public/Public{$feature}SummaryResource.php"),
            'policy.stub' => app_path("Policies/V1/{$feature}/{$feature}Policy.php"),
        ];

        // Check the templates before writing anything, to avoid a half-generated feature.
        foreach (array_keys($files) as $stubName) {
            $stubPath = base_path("stubs/features/{$stubName}");

            if (! File::exists($stubPath)) {
                $this->error("Stub not found: {$stubPath}");

                return self::FAILURE;
            }
        }

        $created = 0;
        $skipped = 0;

        foreach ($files as $stubName => $destination) {
            if (File::exists($destination) && ! $this->option('force')) {
                $this->warn("Skipped existing file: {$destination}");
                $skipped++;

                continue;
            }

            $stubPath = base_path("stubs/features/{$stubName}");
            $content = str_replace(
                array_keys($replacements),
                array_values($replacements),
                File::get($stubPath)
            );

            File::ensureDirectoryExists(dirname($destination));
            File::put($destination, $content);

            $this->info("Created: {$destination}");
            $created++;
        }

        $this->newLine();
        $this->info("Feature [{$feature}] scaffold completed. Created: {$created}; skipped: {$skipped}.");

        if ($skipped > 0 && ! $this->option('force')) {
            $this->comment('Use --force to overwrite existing files. Review those files before doing so.');
        }

        return self::SUCCESS;
    }
}
