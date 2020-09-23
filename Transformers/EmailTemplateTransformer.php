<?php
namespace App\Transformers;

use LaraCrud\Helpers\TransformerAbstract;
use League\Fractal\ParamBag;
use App\Models\EmailTemplate;



class EmailTemplateTransformer extends TransformerAbstract
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


    public function transform(EmailTemplate $emailTemplate)
    {
        $data= [
			"id" => $emailTemplate->id,
			"name" => $emailTemplate->name,
			"slug" => $emailTemplate->slug,
			"content" => $emailTemplate->content,
			"created_at" => $emailTemplate->created_at,
			"updated_at" => $emailTemplate->updated_at,
			"deleted_at" => $emailTemplate->deleted_at,

        ];
        return $this->filterFields($data);

    }

    
}