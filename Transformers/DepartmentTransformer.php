<?php
namespace App\Transformers;

use LaraCrud\Helpers\TransformerAbstract;
use League\Fractal\ParamBag;
use App\Models\Department;




class DepartmentTransformer extends TransformerAbstract
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


    public function transform(Department $department)
    {
        $data= [
			"id" => $department->ID,
            "name" => $department->Departments,
            "status" => $department->status,
			"created_at" => $department->created_at,
			"updated_at" => $department->updated_at,

        ];
        return $this->filterFields($data);
    }

    
}