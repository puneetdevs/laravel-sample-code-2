<?php
namespace App\Transformers;

use LaraCrud\Helpers\TransformerAbstract;
use League\Fractal\ParamBag;
use App\Models\ConfigurationTemplate;



class ConfigurationTemplateTransformer extends TransformerAbstract
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


    public function transform(ConfigurationTemplate $configurationTemplate)
    {
        $data= [
			"ID" => $configurationTemplate->ID,
			"ConfigName" => $configurationTemplate->ConfigName,
			"THBegins" => $configurationTemplate->THBegins,
			"THEnds" => $configurationTemplate->THEnds,
			"SpecOTStart" => $configurationTemplate->SpecOTStart,
			"SpecOTEnd" => $configurationTemplate->SpecOTEnd,
			"BreakPeriod" => $configurationTemplate->BreakPeriod,
			"OverTimeCalc" => $configurationTemplate->OverTimeCalc,
			"OTStarts" => $configurationTemplate->OTStarts,
			"DTStarts" => $configurationTemplate->DTStarts,
			"BreakPeriodAcross" => $configurationTemplate->BreakPeriodAcross,
			"OFARate" => $configurationTemplate->OFARate,
			"OFABasePay" => $configurationTemplate->OFABasePay,
			"OFABaseCharge" => $configurationTemplate->OFABaseCharge,
			"OFAOtherCharge" => $configurationTemplate->OFAOtherCharge,
			"OFASpecialRate" => $configurationTemplate->OFASpecialRate,
			"BaseCharge" => $configurationTemplate->BaseCharge,
			"OtherCharge" => $configurationTemplate->OtherCharge,
			"region_id" => $configurationTemplate->region_id,
			"created_at" => $configurationTemplate->created_at,
			"updated_at" => $configurationTemplate->updated_at,
			"deleted_at" => $configurationTemplate->deleted_at,
			"Country" => $configurationTemplate->Country,
			"Prov" => $configurationTemplate->Prov,

        ];
        return $this->filterFields($data);

    }

    
}