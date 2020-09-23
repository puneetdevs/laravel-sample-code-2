<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Mail\SendBulkEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Aloha\Twilio\TwilioInterface;
use Twilio;
use Auth;

class SendBulkMessageSMS implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user_mobile;

    protected $sms_message;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user_mobile, $sms_message)
    {
        
        $this->user_mobile = '+'.$user_mobile['people']['WorkExt'].$user_mobile['people']['Cell'];
        $this->sms_message = $sms_message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $response  = Twilio::message($this->user_mobile,$this->sms_message );
        } catch(\Twilio\Exceptions\RestException $e) {
           // $this->failed($e);
        }
    }
    

}
