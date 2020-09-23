<?php
namespace App\Transformers\EventTransformer;

use LaraCrud\Helpers\TransformerAbstract;
use League\Fractal\ParamBag;
use App\Models\Tbleventdate;

class TbleventdateTransformer extends TransformerAbstract
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


    public function transform(Tbleventdate $tbleventdate){
    	$data = [
			"DID" => $tbleventdate->DID,
			"EID" => $tbleventdate->EID,
			"Eventdate" => $tbleventdate->Eventdate,
			"EventDescription" => $tbleventdate->EventDescription,
			"StatHoliday" => $tbleventdate->StatHoliday,
			"DataChecked" => $tbleventdate->DataChecked,
			"SortOrder" => $tbleventdate->SortOrder,
			"is_publish" => $tbleventdate->is_publish,
			"DoubleTime" => $tbleventdate->DoubleTime,
			"Invoiced" => $tbleventdate->Invoiced,
			"DBO" => $tbleventdate->DBO,
			"Tax1" => $tbleventdate->Tax1,
			"Tax2" => $tbleventdate->Tax2,
			"is_check" => $tbleventdate->is_check,
			"ApplySurcharge" => $tbleventdate->ApplySurcharge,
			"created_at" => $tbleventdate->created_at,
			"updated_at" => $tbleventdate->updated_at,
			"deleted_at" => $tbleventdate->deleted_at

        ];
        return $this->filterFields($data);

	}
}