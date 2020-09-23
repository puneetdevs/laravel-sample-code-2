<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Aloha\Twilio\TwilioInterface;
use App\Models\EventsShiftLog;
use Illuminate\Support\Facades\Config;
use EventHelper;
use Twilio;

class SendDatePublishSMS implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $shiftLog;
    protected $mobile_number;
    protected $message;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($shiftLog)
    {
        $event_name = isset($shiftLog['event']['EventName']) ? $shiftLog['event']['EventName']: '' ;
        $event_date = isset($shiftLog['date']['Eventdate']) ? date('Y-m-d',strtotime($shiftLog['date']['Eventdate'])): '' ;
        $location = isset($shiftLog['event']['location']) ? $shiftLog['event']['location']['VAddressLine1'].' '.$shiftLog['event']['location']['VCity'].' '.$shiftLog['event']['location']['VCountry']: '' ;
        $position = isset($shiftLog['position']['Position']) ? $shiftLog['position']['Position']: '' ;
        
        $start_time = '';
        if(!empty($this->user['Start3'])){
            $start_time = $this->user['Start3'];
        }
        if(!empty($this->user['Start2'])){
            $start_time = $this->user['Start2'];
        }
        if(!empty($this->user['Start1'])){
            $start_time = $this->user['Start1'];
        }

        $end_time = '';
        if(!empty($this->user['Finish1'])){
            $end_time = $this->user['Finish1'];
        }
        if(!empty($this->user['Finish2'])){
            $end_time = $this->user['Finish2'];
        }
        if(!empty($this->user['Finish3'])){
            $end_time = $this->user['Finish3'];
        }

        $start_time = $start_time != '' ? date('H:i', strtotime($start_time)) : '';
        $end_time = $end_time != '' ? date('H:i', strtotime($end_time)) : '';
        $shift_time = $start_time.' - '.$end_time;
        $shift_id = isset($shiftLog['ShiftID']) ? $shiftLog['ShiftID'] : $shiftLog['ID'] ;
        $encripted_id = EventHelper::base62encode($shift_id);
        $link = Config::get('app.url').'/invite/'.$encripted_id;
       
        
        $this->shiftLog = $shiftLog;
        $this->mobile_number = '+'.$this->shiftLog['people']['WorkExt'].$this->shiftLog['people']['Cell'];
        if($shiftLog['Confirmed'] == 0){
            $this->message = "You have been invited for shift:\r\nEvent Name: ".$event_name."\r\nEvent Location: ".$location."\r\nShift Date: ".$event_date."\r\nPosition: ".$position."\r\nShift Time: ".$shift_time."\r\nClick on blow link for Accept/Reject\r\n".$link;
        }else if($shiftLog['Confirmed'] == 1){
            $this->message = "You have been confirmed for shift:\r\nEvent Name: ".$event_name."\r\nEvent Location: ".$location."\r\nShift Date: ".$event_date."\r\nPosition: ".$position."\r\nShift Time: ".$shift_time;
        }else if($shiftLog['Confirmed'] == 2){
            $this->message = "Your invitation accepted by admin for shift:\r\nEvent Name: ".$event_name."\r\nEvent Location: ".$location."\r\nShift Date: ".$event_date."\r\nPosition: ".$position."\r\nShift Time: ".$shift_time;
        }else if($shiftLog['Confirmed'] == 3){
            $this->message = "You have been added in waiting list we will connect you asap. for shift:\r\nEvent Name: ".$event_name."\r\nEvent Location: ".$location."\r\nShift Date: ".$event_date."\r\nPosition: ".$position."\r\nShift Time: ".$shift_time;
        }else if($shiftLog['Confirmed'] == 4){
            $this->message = "You have been declined for shift:\r\nEvent Name: ".$event_name."\r\nEvent Location: ".$location."\r\nShift Date: ".$event_date."\r\nPosition: ".$position."\r\nShift Time: ".$shift_time;
        }        
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            Twilio::message($this->mobile_number,$this->message);
            /**Shift Log Success**/
            $this->changeShiftLogStatus(1);
        } catch(\Twilio\Exceptions\RestException  $e) {
            /**Shift Log Failed**/
            $this->changeShiftLogStatus(2);
        }
    }

    /*** Update Shift Status ***/
    public function changeShiftLogStatus($status)
    {  
        $this->shiftLog;
        try {
            if($status == 1 && isset($this->shiftLog['ShiftID'])){
                EventsShiftLog::where('ID', $this->shiftLog['ID'])->delete();
            }
        } catch (\Illuminate\Database\QueryException $ex) {
        }
    }

}
