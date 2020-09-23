<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Mail\SendBulkEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendBulkMessageEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    protected $bulk_template;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, $bulk_template)
    {
        
        $this->user = $user;
        $start_time = '';
        if(!empty($user['Start3'])){
            $start_time = $user['Start3'];
        }
        if(!empty($user['Start2'])){
            $start_time = $user['Start2'];
        }
        if(!empty($user['Start1'])){
            $start_time = $user['Start1'];
        }
        

        $end_time = '';
        if(!empty($user['Finish1'])){
            $end_time = $user['Finish1'];
        }
        if(!empty($user['Finish2'])){
            $end_time = $user['Finish2'];
        }
        if(!empty($user['Finish3'])){
            $end_time = $user['Finish3'];
        }
        
        $start_time = $start_time != '' ? date('H:i', strtotime($start_time)) : '';
        $end_time = $end_time != '' ? date('H:i', strtotime($end_time)) : '';
        $shift_time = $start_time.' - '.$end_time;
        $date = date('Y-m-d', strtotime($user['date']['Eventdate']));
        $this->bulk_template = ['subject' => $bulk_template['subject'],
                                'message' => $bulk_template['message'], 
                                "user_name" => $user['people']['FirstName'],
                                "event_name" => $user['event']['EventName'],
                                "date" => $date,
                                "shift_time" => $shift_time,
                                "position" => $user['position']['Position']
                            ];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            Mail::send(new SendBulkEmail($this->user, $this->bulk_template));
        } catch(Exception $e) {
        }
    }
    

}
