<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Mail\SendBulkEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\TblpeopleNotification;
use App\Models\ExternalNoteDetail;

class sendBulkMessageOnPublishedDate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $bulk_template;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;

        $start_time = '';
        if(isset($user['shift']) && !empty($user['shift'])){
            if(!empty($user['shift']['Start3'])){
                $start_time = $user['shift']['Start3'];
            }
            if(!empty($user['shift']['Start2'])){
                $start_time = $user['shift']['Start2'];
            }
            if(!empty($user['shift']['Start1'])){
                $start_time = $user['shift']['Start1'];
            }
        }

        $end_time = '';
        if(isset($user['shift']) && !empty($user['shift'])){
            if(!empty($user['shift']['Finish1'])){
                $end_time = $user['shift']['Finish1'];
            }
            if(!empty($user['shift']['Finish2'])){
                $end_time = $user['shift']['Finish2'];
            }
            if(!empty($user['shift']['Finish3'])){
                $end_time = $user['shift']['Finish3'];
            }
        }
        $start_time = $start_time != '' ? date('H:i', strtotime($start_time)) : '';
        $end_time = $end_time != '' ? date('H:i', strtotime($end_time)) : '';
        $shift_time = $start_time.' - '.$end_time;
        $date = date('Y-m-d', strtotime($user['date']['Eventdate']));

        $this->bulk_template = [
            'subject' => $user['subject'],
            'message' => $user['message'], 
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
            /**Notification Success**/
            $this->changeNotificationStatus(1);
        } catch(Exception $e) {
            /**Notification Success**/
            $this->changeNotificationStatus(2);
        }
    }

    /*** Update Notification Status ***/
    public function changeNotificationStatus($status)
    {  
        $this->user;
        try {
            TblpeopleNotification::where('id', $this->user['id'])->update(['status'=>$status]);
            ExternalNoteDetail::where('DID', $this->user['DID'])
                        ->where('EID', $this->user['EID'])
                        ->where('PID', $this->user['PID'])
                        ->update(['status'=>1]);
        } catch (\Illuminate\Database\QueryException $ex) {
        }
    }
    

}
