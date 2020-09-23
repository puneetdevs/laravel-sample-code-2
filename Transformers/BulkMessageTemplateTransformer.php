<?php
namespace App\Transformers;

use LaraCrud\Helpers\TransformerAbstract;
use League\Fractal\ParamBag;
use App\Models\BulkMessageTemplate;



class BulkMessageTemplateTransformer extends TransformerAbstract
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


    public function transform(BulkMessageTemplate $bulkMessageTemplate)
    {
        $data= [
			"id" => $bulkMessageTemplate->id,
			"title" => $bulkMessageTemplate->title,
			"type" => $bulkMessageTemplate->type,
			"subject" => $bulkMessageTemplate->subject,
			"message" => $bulkMessageTemplate->message,
			"created_at" => $bulkMessageTemplate->created_at,
			"updated_at" => $bulkMessageTemplate->updated_at,
			"deleted_at" => $bulkMessageTemplate->deleted_at,

        ];
        return $this->filterFields($data);

    }

    
}