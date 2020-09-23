<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Aloha\Twilio\TwilioInterface;
use App\Models\TblpeopleNotification;
use Illuminate\Support\Facades\Config;
use EventHelper;
use Twilio;

class SendInviteSMS implements ShouldQueue
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
        $this->message = $notification['sms_message'];
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
            /**Notification Success**/
            $this->changeNotificationStatus(1);
        } catch(\Twilio\Exceptions\RestException  $e) {
           
           /**Notification Failed**/
           $this->changeNotificationStatus(2);
        }
    }

    /*** Update Notification Status ***/
    public function changeNotificationStatus($status)
    {  
        $this->notification;
        try {
            TblpeopleNotification::where('id', $this->notification['id'])->update(['status'=>$status]);
        } catch (\Illuminate\Database\QueryException $ex) {
        }
    }
}
