<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeModule extends Command
{
    protected $signature = 'make:module {name : O nome do módulo (ex: Order)}';
    protected $description = 'Cria uma estrutura de módulo Enterprise em app/Modules';

    public function handle()
    {
        $name = Str::studly($this->argument('name'));
        $basePath = app_path("Modules/{$name}");

        if (File::exists($basePath)) {
            $this->error("Módulo {$name} já existe!");
            return;
        }

        $directories = [
            "Http/Controllers",
            "Http/Requests",
            "Http/Resources",
            "Console",
            "Models",
            "Services",
            "Actions",
            "DataTransferObjects",
            "Repositories",
            "Persistence/Migrations",
            "Providers",
            "Tests/Feature",
            "Tests/Unit",
        ];

        foreach ($directories as $directory) {
            File::makeDirectory("{$basePath}/{$directory}", 0755, true);
        }

        $this->createServiceProvider($name, $basePath);

        $this->info("✅ Módulo {$name} criado com sucesso em app/Modules/{$name}");
        $this->warn("Lembre-se de registrar o {$name}ServiceProvider em bootstrap/providers.php (Laravel 12+)");
    }

    protected function createServiceProvider($name, $basePath)
    {
        $stub = "<?php

namespace App\Modules\\{$name}\Providers;

use Illuminate\Support\ServiceProvider;

class {$name}ServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Registro de Repositories ou Bindings
    }

    public function boot(): void
    {
        \$this->loadMigrationsFrom(__DIR__ . '/../Persistence/Migrations');
    }
}
";
        File::put("{$basePath}/Providers/{$name}ServiceProvider.php", $stub);
    }
}
