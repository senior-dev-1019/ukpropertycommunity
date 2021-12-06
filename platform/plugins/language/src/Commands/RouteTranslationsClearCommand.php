<?php

namespace Botble\Language\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Language;

class RouteTranslationsClearCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'route:trans:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove the translated route cache files for each locale';

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Create a new route clear command instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->call('route:clear');

        foreach (Language::getSupportedLanguagesKeys() as $locale) {

            $path = $this->makeLocaleRoutesPath($locale);

            if ($this->files->exists($path)) {
                $this->files->delete($path);
            }
        }

        $path = $this->laravel->getCachedRoutesPath();

        if ($this->files->exists($path)) {
            $this->files->delete($path);
        }

        $this->info('Route caches for locales cleared!');
    }

    /**
     * @param string $locale
     * @return string
     */
    protected function makeLocaleRoutesPath($locale = '')
    {
        $path = $this->laravel->getCachedRoutesPath();

        if (!$locale) {
            return $path;
        }

        return substr($path, 0, -4) . '_' . $locale . '.php';
    }
}
