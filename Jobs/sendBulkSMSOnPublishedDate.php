<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\TblpeopleNotification;
use App\Models\ExternalNoteDetail;
use Aloha\Twilio\TwilioInterface;
use Twilio;

class sendBulkSMSOnPublishedDate implements ShouldQueue
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
        $this->notification = $notification;
        $this->mobile_number = '+'.$this->notification['people']['WorkExt'].$this->notification['people']['Cell'];
        $this->message = $notification['message'];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $response  = Twilio::message($this->mobile_number,$this->message);
            /**Notification Success**/
            $this->changeNotificationStatus(1);
        } catch(\Twilio\Exceptions\RestException $e) {
            /**Notification not send**/
            $this->changeNotificationStatus(2);
        }
    }

    /*** Update Notification Status ***/
    public function changeNotificationStatus($status)
    {  
        $this->notification;
        try {
            TblpeopleNotification::where('id', $this->notification['id'])->update(['status'=>$status]);
            ExternalNoteDetail::where('DID', $this->notification['DID'])
                        ->where('EID', $this->notification['EID'])
                        ->where('PID', $this->notification['PID'])
                        ->update(['status'=>1]);
        } catch (\Illuminate\Database\QueryException $ex) {
        }
    }
    

}
