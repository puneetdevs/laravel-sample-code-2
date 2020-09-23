<?php
namespace App\Transformers;

use LaraCrud\Helpers\TransformerAbstract;
use League\Fractal\ParamBag;
use App\Models\HolidayList;



class HolidayListTransformer extends TransformerAbstract
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


    public function transform(HolidayList $holidayList)
    {
        $data= [
			"id" => $holidayList->ID,
			"name" => $holidayList->Description,
            "date" => $holidayList->StatDate,
            "ApplyStat" => $holidayList->ApplyStat,
			"created_at" => $holidayList->created_at,
			"updated_at" => $holidayList->updated_at,
			"deleted_at" => $holidayList->deleted_at,

        ];
        return $this->filterFields($data);

    }

    
}