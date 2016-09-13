<?php namespace Cyvelnet\Laravel5Fractal\Commands;

use Illuminate\Config\Repository as Config;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem as File;
use Illuminate\View\Factory as View;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class TransformerGeneratorCommand
 *
 * @package Cyvelnet\Laravel5Fractal\Commands
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
    private $file;

    /**
     * @param \Illuminate\Config\Repository $config
     * @param View $view
     * @param \Illuminate\Filesystem\Filesystem $file
     */
    public function __construct(Config $config, View $view, File $file)
    {
        parent::__construct();
        $this->config = $config;
        $this->view = $view;
        $this->file = $file;
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

            //retrieves namespace configuration
            $namespace = $this->option('namespace') ? $this->option('namespace') : $this->config->get('fractal.namespace');

            list($class, $namespace, $directory) = $this->getTransformerProperties($class_name, $namespace, $directory);

            is_dir($directory) ?: $this->file->makeDirectory($directory, 0755, true);

            $create = true;

            // transformer store path
            $transformer = "{$directory}/{$class}";

            if ($this->file->exists("{$transformer}.php")) {
                if ($usrResponse = strtolower($this->ask("The file ['{$class}'] already exists, overwrite? [y/n]",
                    null))
                ) {
                    switch ($usrResponse) {
                        case 'y' :
                            $backupFile = "{$directory}/{$class}.php";

                            while ($this->file->exists($backupFile)) {
                                $prefix = (new \DateTime())->format('Y_m_d_His');
                                $backupFile = "{$directory}/{$prefix}_{$class}.php";
                            }
                            rename("{$directory}/{$class}.php", $backupFile);
                            $this->info("A backup has been generated at {$backupFile}");
                            break;
                        default:
                            $this->info('No file has been created.');
                            $create = false;
                    }
                }

            }

            // loading transformers template from views
            $view = $this->view->make('fractal::transformer',
                ['namespace' => $namespace, 'class_name' => $class]);


            if ($create) {
                $this->file->put("{$directory}/{$class}.php", $view->render());
                $this->info("The class {$class} generated successfully.");
            }


        } catch (\Exception $e) {
            $this->error("Transformer creation failed due to : {$e->getMessage()}");
        }


    }

    /**
     * get application path
     *
     * @param $path
     *
     * @return string
     */
    private function appPath($path)
    {
        return base_path('/app/' . $path);
    }

    /**
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('name', InputArgument::REQUIRED, 'Name of the transformer class'),
        );
    }

    /**
     * @return array
     */
    protected function getOptions()
    {

        return array(
            array(
                'directory',
                null,
                InputOption::VALUE_OPTIONAL,
                'transformer store directory (relative to App\)',
                null,
            ),
            array(
                'namespace',
                null,
                InputOption::VALUE_OPTIONAL,
                'transformer namespace',
                null,
            ),
        );
    }

    /**
     * @param $class
     * @param $namespace
     * @param $storePath
     *
     * @return array
     */
    private function getTransformerProperties($class, $namespace, $storePath)
    {

        // check if class contains additional level
        if (strpos($class, '/') !== false) {
            $additionalLevel = substr($class, 0, strrpos($class, '/'));

            $class = basename($class);

            $namespace = rtrim(str_replace('/', '\\', $namespace . "\\{$additionalLevel}"), '\\');

            $storePath = str_replace('//', '/', rtrim($storePath, '/') . "/{$additionalLevel}");

        }

        return [
            $class,
            $namespace,
            $storePath,
        ];
    }


}
