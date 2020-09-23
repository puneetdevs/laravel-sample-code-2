<?php
namespace App\Transformers;

use LaraCrud\Helpers\TransformerAbstract;
use League\Fractal\ParamBag;
use App\Models\Tbltemplate;
use App\Models\Client;
use App\Models\Note;
use App\Models\Location;
use App\Transformers\LocationTransformer;
use App\Transformers\EventTransformer\NoteDetailTransformer;
use App\Transformers\ClientTransformer;
use App\Transformers\ConfigurationsTransformer;


class TbltemplateTransformer extends TransformerAbstract
{
     /**
     * @var array
     */
    private $validParams = ['q', 'limit', 'page','fields'];

    /**
     * @var array
     */
    protected $availableIncludes = [
        'client','location','note','rate'
    ];

     /**
      * @var array
      */
    protected $defaultIncludes = [];


    public function transform(Tbltemplate $tbltemplate)
    {
        $data= [
			"TemplateID" => $tbltemplate->TemplateID,
			"TemplateName" => $tbltemplate->TemplateName,
			"RateConfiguration" => $tbltemplate->RateConfiguration,
			"Venue" => $tbltemplate->Venue,
			"Client" => $tbltemplate->Client,
			"DefaultName" => $tbltemplate->DefaultName,
			"Schedule" => $tbltemplate->Schedule,
			"created_at" => $tbltemplate->created_at,
			"updated_at" => $tbltemplate->updated_at,
			"deleted_at" => $tbltemplate->deleted_at,

        ];
        return $this->filterFields($data);

    }

    /*Location Relation with Template get ITEM*/
	public function includeLocation(Tbltemplate $entity){
		if( $entity->location != NULL ){
			return $this->item($entity->location, new LocationTransformer());
		}
		return null;
    }
    
    /*Client Relation with Template get ITEM*/
	public function includeClient(Tbltemplate $entity){
		if( $entity->client != NULL ){
			return $this->item($entity->client, new ClientTransformer());
		}
		return null;
    }
    
    /*Rate Relation with Template get ITEM*/
	public function includeRate(Tbltemplate $entity){
		if( $entity->rate != NULL ){
			return $this->item($entity->rate, new ConfigurationsTransformer());
		}
		return null;
    }

    /*Account Manager Relation with Template get ITEM*/
	public function includeNote(Tbltemplate $entity){
		if( $entity->note != NULL ){
			return $this->collection($entity->note, new NoteDetailTransformer());
		}
		return null;
	}    
}