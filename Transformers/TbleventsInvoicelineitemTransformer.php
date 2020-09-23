<?php
namespace App\Transformers;

use LaraCrud\Helpers\TransformerAbstract;
use League\Fractal\ParamBag;
use App\Models\TbleventsInvoicelineitem;
use App\Models\DataFileSettings;
use Auth;


class TbleventsInvoicelineitemTransformer extends TransformerAbstract
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


    public function transform(TbleventsInvoicelineitem $tbleventsInvoicelineitem)
    {
        $setting = DataFileSettings::where('region_id', Auth::user()->region_id)->first();
        $data= [
			"ID" => $tbleventsInvoicelineitem->ID,
			"EID" => $tbleventsInvoicelineitem->EID,
			"Description" => $tbleventsInvoicelineitem->Description,
			"Amount" => round ($tbleventsInvoicelineitem->Amount,2),
            "Tax1" => $tbleventsInvoicelineitem->Tax1,
            "Tax2" => $tbleventsInvoicelineitem->Tax2,
            "Tax3" => $tbleventsInvoicelineitem->Tax3,
            "Tax1_name" => ($setting) ? $setting->Tax1_Name:'',
            "Tax2_name" => ($setting) ? $setting->Tax2_Name:'',
            "Tax3_name" => ($setting) ? $setting->Tax3_Name:'',
            "region_id" => $tbleventsInvoicelineitem->region_id,
			"created_at" => $tbleventsInvoicelineitem->created_at,
			"updated_at" => $tbleventsInvoicelineitem->updated_at,
			"deleted_at" => $tbleventsInvoicelineitem->deleted_at,

        ];
        return $this->filterFields($data);

    }

    
}