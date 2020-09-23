<?php
namespace App\Transformers;

use LaraCrud\Helpers\TransformerAbstract;
use League\Fractal\ParamBag;
use App\Models\ExternalNote;
use App\Models\ExternalNoteDetail;
use App\Models\TbleventsShifthour;



class ExternalNoteSingleTransformer extends TransformerAbstract
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
        $positions_result = $positions_data->toArray();
        $positions = array();
        if(!empty($positions_result)){
            foreach($positions_result as $key=>$position){


                
                 if(in_array($position['DID'], array_column($positions, 'DID'))) {
                    $date_key = array_search($position['DID'], array_column($positions, 'DID'));
                    $positions[$date_key]['PID'][$key] = array('id' => $position['PID'], 'positions' => isset($position['positions']['Position']) ? $position['positions']['Position']: '');
                 } else {
                    if(!empty($positions)){
                        $new_key = count($positions);
                        $pid_key = isset($positions['PID']) ? count($positions['PID']) : 0;
                    }else{
                        $new_key = $key;
                        $pid_key = $key;
                    }
                    $positions[$new_key]['DID'] = $position['DID'];
                    $positions[$new_key]['Eventdate'] = isset($position['date']['Eventdate']) ? $position['date']['Eventdate']: '';
                    $positions[$new_key]['status'] = $position['status'];
                    $positions[$new_key]['PID'][$pid_key]['id'] = $position['PID'];
                    $positions[$new_key]['PID'][$pid_key]['positions'] = isset($position['positions']['Position']) ? $position['positions']['Position']: '';
               }
            }
        }

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