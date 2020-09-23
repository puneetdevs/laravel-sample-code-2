<?php
namespace App\Transformers\EmployeeTransformer;

use LaraCrud\Helpers\TransformerAbstract;
use League\Fractal\ParamBag;
use App\Models\EmployeeCounterPrefix;

class EmployeeCounterTransformer extends TransformerAbstract
{
     /**
     * @var array
     */
    private $validParams = ['q', 'limit', 'page','fields'];

    /**
     * @var array
     */
    protected $availableIncludes = [];

     /**
      * @var array
      */
    protected $defaultIncludes = [];


    public function transform(EmployeeCounterPrefix $counter_prefix)
    {
        $data= [
            "ID" => $counter_prefix->ID,
            "Emp_Cntr_Code" => $counter_prefix->Emp_Cntr_Code,
            "Emp_Cntr_Description" => $counter_prefix->Emp_Cntr_Description
        ];
        return $this->filterFields($data);
    }

    
}