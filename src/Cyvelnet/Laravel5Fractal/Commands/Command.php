<?php

namespace Cyvelnet\Laravel5Fractal\Commands;

use Illuminate\Support\Arr;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

abstract class Command extends \Illuminate\Console\Command
{
    /**
     * get application path.
     *
     * @param $path
     *
     * @return string
     */
    protected function appPath($path)
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

        $classNamespace = $this->getModelNamespace($class);

        if ($class) {
            if (class_exists($classNamespace)) {
                $model = new \ReflectionClass($classNamespace);

                if ($model->isSubclassOf('Illuminate\Database\Eloquent\Model')) {
                    $mdl = $this->app->make($classNamespace);
                    $table = $mdl->getTable();

                    if (is_array($table)) {
                        $table = array_first($table);
                    }

                    $table = $mdl->getConnection()->getTablePrefix().$table;
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
                $this->error("Your model {$class} was not found in {$this->config->get('fractal.model_namespace')}\\ \r\nIf this is the first time you get this message, try to update /config/fractal.php to make changes to model_namespace accordingly.");
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
    protected function getCasting($type)
    {
        if ($type instanceof \Doctrine\DBAL\Types\DecimalType or $type instanceof
            \Doctrine\DBAL\Types\FloatType
        ) {
            return 'double';
        } else {
            if ($type instanceof \Doctrine\DBAL\Types\IntegerType or $type instanceof \Doctrine\DBAL\Types\BigIntType or $type instanceof
                \Doctrine\DBAL\Types\SmallIntType
            ) {
                return 'int';
            }
        }
    }

    /**
     * get model namespace from a class name.
     *
     * @param $class
     *
     * @return string
     */
    protected function getModelNamespace($class)
    {
        $namespace = $this->config->get('fractal.model_namespace');

        return "\\{$namespace}\\{$class}";
    }
}
