<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Aloha\Twilio\TwilioInterface;
use Illuminate\Support\Facades\Config;
use Twilio;

class SendDeletedConformationSMS implements ShouldQueue
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
        if(!empty($shiftLog['Start3'])){
            $start_time = $shiftLog['Start3'];
        }
        if(!empty($shiftLog['Start2'])){
            $start_time = $shiftLog['Start2'];
        }
        if(!empty($shiftLog['Start1'])){
            $start_time = $shiftLog['Start1'];
        }

        $end_time = '';
        if(!empty($shiftLog['Finish1'])){
            $end_time = $shiftLog['Finish1'];
        }
        if(!empty($shiftLog['Finish2'])){
            $end_time = $shiftLog['Finish2'];
        }
        if(!empty($shiftLog['Finish3'])){
            $end_time = $shiftLog['Finish3'];
        }
        $start_time = $start_time != '' ? date('H:i', strtotime($start_time)) : '';
        $end_time = $end_time != '' ? date('H:i', strtotime($end_time)) : '';
        $shift_time = $start_time.' - '.$end_time;      
        
        $this->shiftLog = $shiftLog;
        $this->mobile_number = '+'.$this->shiftLog['people']['WorkExt'].$this->shiftLog['people']['Cell'];
  
        $this->message = "The shift has been deleted by Admin details are:\r\nEvent Name: ".$event_name."\r\nEvent Location: ".$location."\r\nShift Date: ".$event_date."\r\nPosition: ".$position."\r\nShift Time: ".$shift_time;
        
        
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
