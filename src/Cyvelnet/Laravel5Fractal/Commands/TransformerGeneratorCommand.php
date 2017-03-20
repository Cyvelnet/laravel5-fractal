<?php

namespace Cyvelnet\Laravel5Fractal\Commands;

use Illuminate\Config\Repository as Config;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem as File;
use Illuminate\Support\Arr;
use Illuminate\View\Factory as View;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

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
    private $view;
    /**
     * @var Config
     */
    private $config;
    /**
     * @var File
     */
    private $filesystem;
    /**
     * @var \Illuminate\Foundation\Application|\Laravel\Lumen\Application
     */
    private $app;

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

            $model = $this->option('model');

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
                ['namespace' => $namespace, 'class_name' => $class, 'attributes' => $transformerAttrs]);

            if ($create) {
                $this->filesystem->put("{$directory}/{$class}.php", $view->render());
                $this->info("The class {$class} generated successfully.");
            }
        } catch (\Exception $e) {
            $this->error("Transformer creation failed due to : {$e->getMessage()}");
        }
    }

    /**
     * get application path.
     *
     * @param $path
     *
     * @return string
     */
    private function appPath($path)
    {
        return base_path('/app/'.$path);
    }

    /**
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'Name of the transformer class'],
        ];
    }

    /**
     * @return array
     */
    protected function getOptions()
    {
        return [
            [
                'directory',
                'd',
                InputOption::VALUE_OPTIONAL,
                'transformer store directory (relative to App\)',
                null,
            ],
            [
                'namespace',
                'ns',
                InputOption::VALUE_OPTIONAL,
                'transformer namespace',
                null,
            ],
            [
                'model',
                'm',
                InputOption::VALUE_OPTIONAL,
                'model to get dump to transformer class',
                null,
            ],
        ];
    }

    /**
     * @param $class
     * @param $namespace
     * @param $storePath
     *
     * @return array
     */
    protected function getTransformerProperties($class, $namespace, $storePath)
    {

        // check if class contains additional level
        if (strpos($class, '/') !== false) {
            $additionalLevel = substr($class, 0, strrpos($class, '/'));

            $class = basename($class);

            $namespace = rtrim(str_replace('/', '\\', $namespace."\\{$additionalLevel}"), '\\');

            $storePath = str_replace('//', '/', rtrim($storePath, '/')."/{$additionalLevel}");
        }

        return [
            $class,
            $namespace,
            $storePath,
        ];
    }

    /**
     * @param $class
     *
     * @return array
     */
    protected function getModelColumns($class)
    {
        $attributes = [];

        $namespace = $this->config->get('fractal.model_namespace');

        $classNamespace = "\\{$namespace}\\{$class}";

        if ($class) {
            if (class_exists($classNamespace)) {
                $model = new \ReflectionClass($classNamespace);

                if ($model->isSubclassOf('Illuminate\Database\Eloquent\Model')) {
                    $mdl = $this->app->make($classNamespace);
                    $table = $mdl->getConnection()->getTablePrefix().$mdl->getTable();
                    $schema = $mdl->getConnection()->getDoctrineSchemaManager($table);

                    $database = null;
                    if (strpos($table, '.')) {
                        list($database, $table) = explode('.', $table);
                    }

                    $columns = Arr::except($schema->listTableColumns($table, $database), $mdl->getHidden());

                    foreach ($columns as $column) {
                        if ($column->getType() instanceof \Doctrine\DBAL\Types\JsonArrayType) {
                            continue;
                        }

                        $castTo = $this->getCasting($column->getType());

                        $attributes[] = ['column' => $column->getName(), 'casts' => $castTo];
                    }

                    return $attributes;
                }
            } else {
                $this->error("Your model {$class} was not found in {$namespace}\\ \r\nIf this is the first time you get this message, try to update /config/fractal.php to make changes to model_namespace accordingly.");
                exit();
            }
        }

        // use as a default attribute
        return [
            ['column' => 'id', 'casts' => null],
        ];
    }

    /**
     * get value cast type.
     *
     * @param $type
     *
     * @return null|string
     */
    private function getCasting($type)
    {
        if ($type instanceof \Doctrine\DBAL\Types\DecimalType or $type instanceof
            \Doctrine\DBAL\Types\FloatType
        ) {
            return 'double';
        } else {
            if ($type instanceof \Doctrine\DBAL\Types\BooleanType) {
                return 'bool';
            } else {
                if ($type instanceof \Doctrine\DBAL\Types\IntegerType or $type instanceof \Doctrine\DBAL\Types\BigIntType or $type instanceof
                    \Doctrine\DBAL\Types\SmallIntType
                ) {
                    return 'int';
                }
            }
        }
    }
}
