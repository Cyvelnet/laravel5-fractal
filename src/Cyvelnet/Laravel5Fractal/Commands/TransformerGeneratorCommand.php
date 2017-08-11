<?php

namespace Cyvelnet\Laravel5Fractal\Commands;

use Illuminate\Config\Repository as Config;
use Illuminate\Filesystem\Filesystem as File;
use Illuminate\View\Factory as View;

/**
 * Class TransformerGeneratorCommand.
 */
class TransformerGeneratorCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:transformer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new transformer class';
    /**
     * @var
     */
    protected $view;
    /**
     * @var Config
     */
    protected $config;
    /**
     * @var File
     */
    protected $filesystem;
    /**
     * @var \Illuminate\Foundation\Application|\Laravel\Lumen\Application
     */
    protected $app;

    /**
     * @param \Illuminate\Config\Repository                                 $config
     * @param View                                                          $view
     * @param \Illuminate\Filesystem\Filesystem                             $filesystem
     * @param \Illuminate\Foundation\Application|\Laravel\Lumen\Application $app
     */
    public function __construct(Config $config, View $view, File $filesystem, $app)
    {
        parent::__construct();
        $this->config = $config;
        $this->view = $view;
        $this->filesystem = $filesystem;
        $this->app = $app;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {

            // replace all space after ucwords
            $class_name = preg_replace('/\s+/', '', ucwords($this->argument('name')));

            //retrieves store directory configuration
            $directory = $this->option('directory') ? $this->appPath($this->option('directory')) : $this->appPath($this->config->get('fractal.directory'));

            $model = ucwords($this->option('model'));

            $modelClass = $this->getModelNamespace($model);

            $transformerAttrs = $this->getModelColumns($model);

            //retrieves namespace configuration
            $namespace = $this->option('namespace') ? $this->option('namespace') : $this->config->get('fractal.namespace');

            list($class, $namespace, $directory) = $this->getTransformerProperties($class_name, $namespace, $directory);

            is_dir($directory) ?: $this->filesystem->makeDirectory($directory, 0755, true);

            $create = true;

            // transformer store path
            $transformer = "{$directory}/{$class}";

            if ($this->filesystem->exists("{$transformer}.php")) {
                if ($usrResponse = strtolower($this->ask("The filesystem ['{$class}'] already exists, overwrite? [y/n]",
                    null))
                ) {
                    switch ($usrResponse) {
                        case 'y':
                            $backupFile = "{$directory}/{$class}.php";

                            while ($this->filesystem->exists($backupFile)) {
                                $prefix = (new \DateTime())->format('Y_m_d_His');
                                $backupFile = "{$directory}/{$prefix}_{$class}.php";
                            }
                            rename("{$directory}/{$class}.php", $backupFile);
                            $this->info("A backup has been generated at {$backupFile}");
                            break;
                        default:
                            $this->info('No filesystem has been created.');
                            $create = false;
                    }
                }
            }

            // loading transformers template from views
            $view = $this->view->make('fractal::transformer',
                [
                    'namespace'   => $namespace,
                    'class_name'  => $class,
                    'attributes'  => $transformerAttrs,
                    'modelClass'  => $model ? $modelClass : null,
                    'model'       => $model,
                    'parentClass' => $this->config->get('fractal.abstract_parent',
                        'TransformerAbstract') ?: 'TransformerAbstract',
                ]);

            if ($create) {
                $this->filesystem->put("{$directory}/{$class}.php", $view->render());
                $this->info("The class {$class} generated successfully.");
            }
        } catch (\Exception $e) {
            $this->error("Transformer creation failed due to : {$e->getMessage()}");
        }
    }
}
