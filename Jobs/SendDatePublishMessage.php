<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Mail\SendDatePublishEmail;
use Illuminate\Support\Facades\Mail;
use App\Models\EventsShiftLog;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use EventHelper;

class SendDatePublishMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $shiftLog;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($shiftLog)
    {
        $this->shiftLog = $shiftLog;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            Mail::send(new SendDatePublishEmail($this->shiftLog));
            /**Shift Log Success**/
            $this->changeShiftLogStatus(1);
        } catch(Exception $e) {
            /**Shift Log Failed**/
            $this->changeShiftLogStatus(2);
        }
    }
    
    /*** Update Shift Log Status ***/
    public function changeShiftLogStatus($status)
    {  
        $this->shiftLog;
        try {
            if($status == 1 && isset($this->shiftLog['ShiftID'])){
                EventsShiftLog::where('ID', $this->shiftLog['ID'])->delete();
            }
        } catch (\Illuminate\Database\QueryException $ex) {
        }
    }

}
