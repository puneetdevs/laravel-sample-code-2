<?php
namespace App\Transformers\EmployeeTransformer;

use LaraCrud\Helpers\TransformerAbstract;
use League\Fractal\ParamBag;
use App\Models\People;
use App\User;



class EmployeeShortResponseTransformer extends TransformerAbstract
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


    public function transform(People $People)
    {
       
        $data= [
			"id" => $People->PeopleID,
            "FirstName" => $People->FirstName,
			"LastName" => $People->LastName,
            "City" => $People->City,
            "Prov" => $People->Prov,
            "Postal" => $People->Postal,
            "Country" => $People->Country,
            "Region" => $People->Region,
            "user_id" => $People->user_id,
            'region_id'=>$People->region_id,
            'is_archive' => $People->is_archive
        ];
        return $this->filterFields($data);
    }

    
}