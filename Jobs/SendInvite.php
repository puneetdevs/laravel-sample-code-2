<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Mail\SendInviteEmail;
use Illuminate\Support\Facades\Mail;
use App\Models\TblpeopleNotification;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendInvite implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
       
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            Mail::send(new SendInviteEmail($this->user));
            /**Notification Success**/
            $this->changeNotificationStatus(1);
        } catch(Exception $e) {
            /**Notification Failed**/
            $this->changeNotificationStatus(2);
        }
    }
    
    /*** Update Notification Status ***/
    public function changeNotificationStatus($status)
    {  
        $this->user;
        try {
            TblpeopleNotification::where('id', $this->user['id'])->update(['status'=>$status]);
        } catch (\Illuminate\Database\QueryException $ex) {
        }
    }

}
