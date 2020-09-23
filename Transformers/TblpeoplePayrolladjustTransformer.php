<?php
namespace App\Transformers;

use LaraCrud\Helpers\TransformerAbstract;
use League\Fractal\ParamBag;
use App\Models\TblpeoplePayrolladjust;



class TblpeoplePayrolladjustTransformer extends TransformerAbstract
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


    public function transform(TblpeoplePayrolladjust $tblpeoplePayrolladjust)
    {
        $data= [
			"ID" => $tblpeoplePayrolladjust->ID,
			"PeopleID" => $tblpeoplePayrolladjust->PeopleID,
			"Value" => $tblpeoplePayrolladjust->Value,
			"DateEffectiveStart" => $tblpeoplePayrolladjust->DateEffectiveStart,
			"DateEffectiveEnd" => $tblpeoplePayrolladjust->DateEffectiveEnd,
			"payroll_variable_type" => $tblpeoplePayrolladjust->payroll_variable_type,
			"created_at" => $tblpeoplePayrolladjust->created_at,
			"updated_at" => $tblpeoplePayrolladjust->updated_at,
			"deleted_at" => $tblpeoplePayrolladjust->deleted_at,

        ];
        return $this->filterFields($data);

    }

    
}