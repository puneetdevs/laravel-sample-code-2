<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Mail\SendPersonalShiftEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendPersonalShift implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $shift;
    protected $email;
    protected $subject;
    protected $message;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($shift, $email, $subject, $message)
    {
        $this->shift = $shift;
        $this->email = $email;
        $this->subject = $subject;
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $email_array = $this->email;
        
        foreach($email_array as $email_address){
            try {
                Mail::send(new SendPersonalShiftEmail($this->shift, $email_address, $this->subject, $this->message));
            } catch(Exception $e) {
            }
        }
    }

}
