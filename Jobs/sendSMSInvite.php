<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Aloha\Twilio\TwilioInterface;
use Illuminate\Support\Facades\Config;
use EventHelper;
use Twilio;

class sendSMSInvite implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $notification;
    protected $mobile_number;
    protected $message;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($notification)
    {
        $event_name = isset($notification['event']['EventName']) ? $notification['event']['EventName']: '' ;
        $event_date = isset($notification['date']['Eventdate']) ? date('Y-m-d',strtotime($notification['date']['Eventdate'])): '' ;
        $location = isset($notification['event']['location']) ? $notification['event']['location']['VAddressLine1'].' '.$notification['event']['location']['VCity'].' '.$notification['event']['location']['VCountry']: '' ;
        $position = isset($notification['position']['Position']) ? $notification['position']['Position']: '' ;
        
        $start_time = '';
        if(isset($notification['shift']) && !empty($notification['shift'])){
            if(!empty($notification['shift']['Start3'])){
                $start_time = $notification['shift']['Start3'];
            }
            if(!empty($notification['shift']['Start2'])){
                $start_time = $notification['shift']['Start2'];
            }
            if(!empty($notification['shift']['Start1'])){
                $start_time = $notification['shift']['Start1'];
            }
        }

        $end_time = '';
        if(isset($notification['shift']) && !empty($notification['shift'])){
            if(!empty($notification['shift']['Finish1'])){
                $end_time = $notification['shift']['Finish1'];
            }
            if(!empty($notification['shift']['Finish2'])){
                $end_time = $notification['shift']['Finish2'];
            }
            if(!empty($notification['shift']['Finish3'])){
                $end_time = $notification['shift']['Finish3'];
            }
        }
        $start_time = $start_time != '' ? date('H:i', strtotime($start_time)) : '';
        $end_time = $end_time != '' ? date('H:i', strtotime($end_time)) : '';
        $shift_time = $start_time.' - '.$end_time;
        $encripted_id = EventHelper::base62encode($notification['shift_id']);
        $link = Config::get('app.url').'/invite/'.$encripted_id;
       
        
        $this->notification = $notification;
        $this->mobile_number = '+'.$this->notification['people']['WorkExt'].$this->notification['people']['Cell'];
        $this->message = "You have been invited for shift:\r\nEvent Name: ".$event_name."\r\nEvent Location: ".$location."\r\nShift Date: ".$event_date."\r\nPosition: ".$position."\r\nShift Time: ".$shift_time."\r\nClick on blow link for Accept/Reject\r\n".$link;
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
        } catch(\Twilio\Exceptions\RestException  $e) {
           
        }
    }
}
