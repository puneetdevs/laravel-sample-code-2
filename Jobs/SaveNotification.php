<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use App\Models\TblpeopleNotification;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SaveNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $shift;
    protected $email_template;
    protected $region_id;
    protected $user_id;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($shift,$email_template,$region_id, $user_id)
    {
        //echo $shift_id; die;
        
        $this->shift = $shift;
        $this->email_template = $email_template;
        $this->region_id = $region_id;
        $this->user_id = $user_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {  
        $notification_data['PeopleID'] = $this->shift['PeopleID'];
        $notification_data['type'] = 'invite';
        $notification_data['DID'] = $this->shift['DID'];
        $notification_data['EID'] = $this->shift['EID'];
        $notification_data['PID'] = $this->shift['PID'];
        $notification_data['shift_id'] = $this->shift['ID'];
        $notification_data['status'] = 0;
        $notification_data['region_id'] = $this->region_id;
        $notification_data['send_by'] = $this->user_id;
        $notification_data['sms_message'] = !empty($this->email_template) ? $this->email_template['sms_message'] : '';
        $notification_data['message'] = !empty($this->email_template) ? $this->email_template['message'] : '';
        $notification_data['subject'] = !empty($this->email_template) ? $this->email_template['subject']: '';
        $notification_data['message_type'] = 'EMAIL';
        TblpeopleNotification::create($notification_data);
    }
}
