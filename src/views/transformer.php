<?= "<?php

namespace {$namespace};"?>

<?= '
use League\Fractal;
use League\Fractal\TransformerAbstract;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
' ?>

<?= "class {$class_name} extends TransformerAbstract
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
     * @var $resource
     * @return array
     */
    public function transform($resource)
    {
        return [

            <?php
                foreach ($attributes as $attribute){


                    if($attribute['casts'])
                    {
                        echo "'{$attribute['column']}' => ({$attribute['casts']}) \$resource->{$attribute['column']},\r\n\t\t\t";

                    }else{

                        echo "'{$attribute['column']}' => \$resource->{$attribute['column']},\r\n\t\t\t";

                    }
                }

            ?>

        ];
    }
}
