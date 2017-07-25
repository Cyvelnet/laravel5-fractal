<?= "<?php

namespace {$namespace};"?>

<?= '
use League\Fractal;
use League\Fractal\TransformerAbstract;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;' ?>

<?= $modelClass ? 'use '.trim($modelClass, '\\').'; ' : ''; ?>
<?= PHP_EOL ?>

<?=
"class {$class_name} extends {$parentClass}
{" ?>

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * Transform object into a generic array
     *
     * @var <?= ltrim("{$modelClass} \$resource").PHP_EOL ?>
     * @return array
     */
    public function transform(<?= ltrim("{$model} \$resource") ?>)
    {
        return [

            <?php
                foreach ($attributes as $attribute) {
                    if ($attribute['casts']) {
                        echo "'{$attribute['column']}' => ({$attribute['casts']}) \$resource->{$attribute['column']},\r\n\t\t\t";
                    } else {
                        echo "'{$attribute['column']}' => \$resource->{$attribute['column']},\r\n\t\t\t";
                    }
                }

            ?>

        ];
    }
}
