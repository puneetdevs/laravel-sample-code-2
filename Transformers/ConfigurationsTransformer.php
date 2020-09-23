<?php
namespace App\Transformers;

use LaraCrud\Helpers\TransformerAbstract;
use League\Fractal\ParamBag;
use App\Models\Configuration;



class ConfigurationsTransformer extends TransformerAbstract
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


    public function transform(Configuration $Configuration)
    {
        $data= [
			"id" => $Configuration->CfgID,
            "ConfigName" => $Configuration->ConfigName,
            "SpecOTStart" => $Configuration->SpecOTStart,
			"SpecOTEnd" => $Configuration->SpecOTEnd,
			"BreakPeriod" => $Configuration->BreakPeriod,
            "OverTimeCalc" => $Configuration->OverTimeCalc,
            "OTStarts" => $Configuration->OTStarts,
            "DTStarts" => $Configuration->DTStarts,
            "THBegins" => $Configuration->THBegins,
            "THEnds" => $Configuration->THEnds,
            "BreakPeriodAcross" => $Configuration->BreakPeriodAcross,
            "OFARate" => $Configuration->OFARate,
            "OFABasePay" => $Configuration->OFABasePay,
            "OFABaseCharge" => $Configuration->OFABaseCharge,
            "OFAOtherCharge" => $Configuration->OFAOtherCharge,
            "region_id" => $Configuration->region_id,
            "Country" => $Configuration->Country,
            "Prov" => $Configuration->Prov,
            "OFASpecialRate" => $Configuration->OFASpecialRate,            
        ];
        return $this->filterFields($data);

    }

    
}