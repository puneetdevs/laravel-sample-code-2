<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Mail\SendShiftReminderEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Config;
use App\Models\NotificationLog;

class SendShiftReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $reminder_message;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user,$email_content)
    {
        
        $this->user = $user;
        $this->user['message'] = $email_content['message'];
        $this->user['subject'] = $email_content['subject'];
        
        $reminder_message = $this->user['message'];

        $event_date = isset($this->user['date']['Eventdate']) ? date('Y-m-d',strtotime($this->user['date']['Eventdate'])) : '';
        $reminder_message = str_replace("{{ event_date }}",$event_date,$reminder_message);
        
        $user_name = isset($this->user['people']['FirstName']) ? $this->user['people']['FirstName'] : '';
        $reminder_message = str_replace("{{ user_name }}",$user_name,$reminder_message);
        
        $event_name = isset($this->user['event']['EventName']) ? $this->user['event']['EventName'] : '';
        $reminder_message = str_replace("{{ event_name }}",$event_name,$reminder_message);

        $event_location = isset($this->user['event']['location']) && !empty($this->user['event']['location']) ? $this->user['event']['location']['VAddressLine1'].' '.$this->user['event']['location']['VCity'].' '.$this->user['event']['location']['VCountry'] : '';
        $reminder_message = str_replace("{{ event_location }}",$event_location,$reminder_message);

        $event_position = isset($this->user['position']) && !empty($this->user['position']) ? $this->user['position']['Position'] : '';
        $reminder_message = str_replace("{{ event_position }}",$event_position,$reminder_message);

        $start_time = '';
            if(!empty($this->user['Start3'])){
                $start_time = $this->user['Start3'];
            }
            if(!empty($this->user['Start2'])){
                $start_time = $this->user['Start2'];
            }
            if(!empty($this->user['Start1'])){
                $start_time = $this->user['Start1'];
            }
        $start_time = $start_time != '' ? date('H:i', strtotime($start_time)) : '';
        $reminder_message = str_replace("{{ start_time }}",$start_time,$reminder_message);

        $end_time = '';
            if(!empty($this->user['Finish1'])){
                $end_time = $this->user['Finish1'];
            }
            if(!empty($this->user['Finish2'])){
                $end_time = $this->user['Finish2'];
            }
            if(!empty($this->user['Finish3'])){
                $end_time = $this->user['Finish3'];
            }
        $end_time = $end_time != '' ? date('H:i', strtotime($end_time)) : '';
        $reminder_message = str_replace("{{ end_time }}",$end_time,$reminder_message);

        $this->reminder_message = $reminder_message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            Mail::send(new SendShiftReminderEmail($this->user, $this->reminder_message));
            /**Notification Log**/
            $this->saveNotificationLog();
        } catch(Exception $e) {}
    }

    /*** Save Reminder Notification Log ***/
    public function saveNotificationLog()
    {  
        $shift_data = $this->user;
        $message_content = $this->reminder_message;
        $data['notification_type'] = 'Shift-Reminder';
        $data['subject'] = $shift_data['subject'];
        $data['message'] = $message_content;
        $data['message_type'] = 'Email';
        $data['send_to'] = $shift_data['people']['Email'];
        $data['send_to_id'] = $shift_data['people']['PeopleID'];
        $data['send_by'] = env('MAIL_FROM');
        $data['region_id'] = $shift_data['region_id'];
        $data['send_by_id'] = '';
        $data['object_id'] = $shift_data['ID'];
        $data['object_type'] = 'Shift';
        try {
            NotificationLog::create($data);
        } catch (\Illuminate\Database\QueryException $ex) {}
    }

}
