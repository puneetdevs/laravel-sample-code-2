<?php
namespace App\Transformers;

use LaraCrud\Helpers\TransformerAbstract;
use League\Fractal\ParamBag;
use App\Models\ExternalNote;
use App\Models\ExternalNoteDetail;
use App\Models\TbleventsShifthour;



class ExternalNoteTransformer extends TransformerAbstract
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


    public function transform(ExternalNote $external_note)
    {   
        #Get positions here
        $external_node = ExternalNoteDetail::where('ENID',$external_note->id);
        $external_node->with(['positions','date']);
        $external_node->whereHas('date');
        $positions_data = $external_node->whereHas('positions')->get();
        $positions = $positions_data->pluck('positions.Position')->toArray();

        #Get status here
        $external_status = ExternalNoteDetail::where('ENID',$external_note->id)->where('status',0)->first();
        $external_note_status = 0;
        if(!$external_status){
            $external_note_status = 1;
        }                                
        

        $data= [
			"id" => $external_note->id,
            "title" => $external_note->title,
            "email_subject" => $external_note->email_subject,
            "email_message" => $external_note->email_message,
            "email_template_id" => $external_note->email_template_id,
            "sms_message" => $external_note->sms_message,
            "sms_template_id" => $external_note->sms_template_id,
            "email" => $external_note->email,
            "sms" => $external_note->sms,
            "status" => $external_note_status,
            "positions" => $positions,
			"created_at" => $external_note->created_at,
			"updated_at" => $external_note->updated_at,
		];
        return $this->filterFields($data);
    }
    
}