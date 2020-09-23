<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use App\Jobs\SendShiftReminder;
use App\Jobs\SendPersonalShift; 
use App\Models\ShiftStatus;
use App\Models\Tblevent;
use App\Models\People;
use App\Models\DataFileSettings;
use App\Models\Region;
use App\Models\OfferEmailTemplate;
use App\Models\SysEmailTemplate;
use App\Models\NotificationLog;
use App\User;
use App\Models\TbleventsShifthour;
use App\Transformers\ShiftStatusTransformer;
use App\Http\Requests\Api\ShiftStatus\ConflictRequest;
use App\Http\Requests\Api\ShiftStatus\PersonalShiftRequest;
use App\Http\Requests\Api\ShiftStatus\Create;
use App\Http\Requests\Api\ShiftStatus\Update;
use App\Http\Requests\Api\ShiftStatus\OfferEmail;
use Illuminate\Console\Scheduling\Schedule;
use DB;
use Carbon\Carbon;
use DateTime;

/**
 * ShiftStatus
 *
 * @Resource("ShiftStatus", uri="/shift-statuses")
 */

class ShiftStatusController extends ApiController
{
    
    public function index(Request $request)
    {
        $perPage = 10;
        $offset = '0';
        if($request->has('per_page')){
            $perPage = $request->per_page;
        }
        $columns_search = ['Status'];

        $data = ShiftStatus::where([]);

        /****** Search *******/
        if($request->has('q')){
            foreach($columns_search as $column){
                $data->orWhere($column, 'LIKE', '%' . $request->q . '%');
            }
        }

       $data =  $data->paginate($perPage);
       return $this->response->paginator($data, new ShiftStatusTransformer());
    }

    public function show(Request $request,  $shiftstatus)
    {
        $shiftstatus = ShiftStatus::find($shiftstatus);
        if( $shiftstatus && is_null($shiftstatus)==false ){
            return $this->response->item($shiftstatus, new ShiftStatusTransformer());
        }
        return $this->response->errorNotFound('Shift Status Not Found', 404);
    }

    public function store(Create $request)
    {
        $model=new ShiftStatus;
        $model->fill($request->all());
        $model->Status = $request->name;

        if ($model->save()) {
            return $this->response->item($model, new ShiftStatusTransformer());
        } else {
            return response()->json(['error' => 'Error occurred while saving Shift Status.'], 422);
        }
    }
 
    public function update(Update $request, $shiftstatus)
    {
        $shiftstatus = ShiftStatus::findOrFail($shiftstatus);
        $shiftstatus->fill($request->all());
        $shiftstatus->Status = $request->name;
        if ($shiftstatus->save()) {
            return $this->response->item($shiftstatus, new ShiftStatusTransformer());
        } else {
            return response()->json(['error' => 'Error occurred while saving Shift Status.'], 422);
        }
    }

    public function destroy(Request $request, $shiftstatus)
    {
        $shiftstatus = ShiftStatus::findOrFail($shiftstatus);
        if ($shiftstatus->delete()) {
            return $this->response->array(['status' => 200, 'message' => 'Shift Status successfully deleted']);
        } else {
            return response()->json(['error' => 'Error occurred while deleting Shift Status.'], 422);
        }
    }

    
    /**
     * sendPersonalShift
     *
     * @param  mixed $request
     *
     * @return void
     */
    public function sendPersonalShift(PersonalShiftRequest $request){
        
        $people = People::where('PeopleID',$request->employee_id)->first();
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $people_shift = TbleventsShifthour::where('PeopleID',$people->PeopleID)
                                            ->whereIn('ID',$request->shift_ids)
                                            ->with([
                                                'event'=>function($query){
                                                    $query->select('EID','EventName','VID');
                                                },'event.location'=>function($query){
                                                    $query->select('VID','VAddressLine1','VCity','VCountry');
                                                },'date'=>function($query){
                                                    $query->select('DID','Eventdate');
                                                },'people'=>function($query){
                                                    $query->select('PeopleID','FirstName','LastName');
                                                },'position'
                                            ]);
        $shift = $people_shift->whereHas('date',function($query) use($from_date, $to_date){
                                $query->where('Eventdate', '>=', $from_date)
                                        ->where('Eventdate', '<=', $to_date);
                            })->limit(50)->get()->toArray();
                        
        if(!empty($shift)){
            $this->dispatch(new SendPersonalShift($shift, $request->email_send_to, $request->email_subject, $request->email_message)); 
            return $this->response->array(['status' => 200, 'message' => 'Personal schedule email sent.']);
        }
        return response()->json(['error' => 'Shift not found.'], 422);
    }

    public function getConflictReport(ConflictRequest $request){
        #Set start and end date here   
        $start_date = $request->start_date;
        $date = strtotime($start_date);
        $add_week = '+'.$request->number_of_week.' week';
        $end_date = date('Y-m-d', strtotime($add_week, $date));
        #Set start and end date here 

        $people_shift = People::whereIn('PeopleID',$request->employee_id)
                                ->select('PeopleID', 'FirstName', 'LastName')
                                ->with([
                                    'shift'=>function($query){
                                        $query->select('EID','DID','PeopleID',DB::raw('IFNULL(TIMEDIFF(Finish1,Start1), 0) + IFNULL(TIMEDIFF(Finish2,Start2), 0) + IFNULL(TIMEDIFF(Finish3,Start3), 0) As hours'));
                                    },'shift.event'=>function($query){
                                        $query->select('EID','EventName','VID');
                                    },'shift.date'=>function($query){
                                        $query->select('DID','Eventdate','EventDescription');
                                    }
                                ]);
        $shift = $people_shift->whereHas('shift.date',function($query) use($start_date, $end_date){
                                $query->where('Eventdate', '>=', $start_date)
                                        ->where('Eventdate', '<=', $end_date);
                            })->get()->toArray();
        $data = array();
        if(!empty($shift)){
            
            foreach($shift as $key=>$shift_value){
                $data[$key]['PeopleID'] = $shift_value['PeopleID'];
                $data[$key]['FirstName'] = $shift_value['FirstName'];
                $data[$key]['LastName'] = $shift_value['LastName'];
                $data[$key]['shift'] = [];
                if($shift_value['shift'] && !empty($shift_value['shift'])){
                    $k = 0;
                    foreach($shift_value['shift'] as $shift_key => $shift_data){
                        if($shift_data['hours'] > $request->more_then_hours){
                            $data[$key]['shift'][$k] = $shift_data;
                            $data[$key]['shift'][$k]['is_warning'] = false;
                            if($shift_data['hours'] > $request->show_warning_hours){
                                $data[$key]['shift'][$k]['is_warning'] = true;
                            }
                            $k++;
                        }
                    }
                }   
            }

        }
        
        return response()->json(['success' => true, 'data' => $data], 200);
    }


    /**
     * Send Shift Reminder
     *
     * @return void
     */
    public function sendShiftReminder(){
        
        #Get Region with Region settings
        $regions = Region::with(['region_setting'=>function($query){
                                $query->select('ID','region_id','reminder_time_off_start',
                                'reminder_time_off_end','reminder_send_before_time','time_zone');
                            }])->get()->toArray();

        #Check regions not empty here                    
        if(!empty($regions)){
            $email_content = SysEmailTemplate::where('tplname', 'Shift-reminder')->first();
            foreach($regions as $region_data){
                #Check region setting set or not here
                if($region_data['region_setting'] !== null){
                    $shifts = $this->getRegionShift($region_data,$email_content);
                }
            }
        }
    }

    private function getRegionShift($region, $email_content){
       
        #Set start date configured hours hours before shift here
        $time_after_n_hours =  Carbon::now()
        ->addHours($region['region_setting']['reminder_send_before_time'])
        ->timezone($region['region_setting']['time_zone']);
        // echo $time_after_n_hours;
        // die;
        $time_now = $time_after_n_hours->format("H:i");
        $off_start = ($region['region_setting']['reminder_time_off_start']) ? $region['region_setting']['reminder_time_off_start'] : '00:00';
        $off_end = ($region['region_setting']['reminder_time_off_end']) ? $region['region_setting']['reminder_time_off_end'] : '07:00';
        $reminder_shifts = [];
        $shift_query = TbleventsShifthour::where('Confirmed',1)
                                            ->where('region_id', $region['id'])
                                            ->where('Start1',$time_now)
                                            ->with([
                                                'people'=>function($query){
                                                    $query->select('PeopleID', 'FirstName', 'LastName','Email','sms_notification','email_notification');
                                                },'event'=>function($query){
                                                    $query->select('EID','EventName','VID');
                                                },'event.location'=>function($query){
                                                    $query->select('VID','VAddressLine1','VCity','VCountry');
                                                },'date'=>function($query){
                                                    $query->select('DID','Eventdate');
                                                },'position'
                                            ]);
       
        $shift_data = $shift_query->whereHas('date',function($query) use($time_after_n_hours){
                                $query->where('Eventdate',  $time_after_n_hours->format('Y-m-d'));
                            })->get()->toArray();
                            
        if(!empty($shift_data)){     
            foreach($shift_data as $key=>$shift_value){   
                #Query on notification check already send or not notification for reminder same user  
                $notification_log = NotificationLog::where('notification_type','Shift-Reminder')
                                                    ->where('object_type','Shift')
                                                    ->where('object_id',$shift_value['ID'])
                                                    ->where('message_type','Email')
                                                    ->first();  
                #If notification log not found then send reminder         
                if(!$notification_log){
                    #If Shift start1 in region setting reminder time off start and end then add in query and send email after reminder time off end is completed
                    if($shift_value['Start1'] >= $off_start && $shift_value['Start1'] <= $off_end){
                        if(isset($shift_value['people']['Email']) && !empty($shift_value['people']['Email']) && $shift_value['people']['email_notification'] == '1'){
                            #Time get and convert into seconds
                            $startTime = new DateTime($time_now);
                            $endTime = new DateTime($off_end);
                            $duration = $startTime->diff($endTime);
                            $final_time = $duration->format("%H:%I");
                            $parsed = date_parse($final_time);
                            $seconds = $parsed['hour'] * 3600 + $parsed['minute'] * 60 + $parsed['second'];
                            $seconds += 60;
                            SendShiftReminder::dispatch($shift_value,$email_content)->delay($seconds);
                        }
                    }else{
                        if(isset($shift_value['people']['Email']) && !empty($shift_value['people']['Email']) && $shift_value['people']['email_notification'] == '1'){
                            SendShiftReminder::dispatch($shift_value,$email_content);
                        }
                    }
                }
            }
        }  
    }

    /**
     * Get Confirmed Emp For Event
     *
     * @param  mixed $request
     * @param  mixed $event_id
     *
     * @return void
     */
    public function getConfirmedEmpForEvent(Request $request, $event_id){ 

        $shift_datas = Tblevent::where('EID',$event_id)
                                ->with([
                                    'sales'=>function($query){
                                        $query->select('id','fullname');
                                    },
                                    'account'=>function($query){
                                        $query->select('id','fullname');
                                    },
                                    'shift'=>function($query){
                                        $query->select('ID', 'PeopleID', 'Confirmed','EID')
                                        ->where('Confirmed',1);
                                    },
                                    'shift.people'=>function($query){
                                        $query->select('PeopleID', 'FirstName', 'LastName','user_id');
                                    }
                                ])->first();
        $people_data = array();
        if($shift_datas ){
            $shift_data = $shift_datas->toArray();
           //print_r($shift_data); die;
            $people_data['sale_manager_id'] =  isset($shift_data['sales']['id']) ? $shift_data['sales']['id'] : '';
            $people_data['sale_manager_name'] =  isset($shift_data['sales']['fullname']) ? $shift_data['sales']['fullname'] : '';
            $people_data['account_manager_id'] =  isset($shift_data['account']['id']) ? $shift_data['account']['id'] : '';
            $people_data['account_manager_name'] =  isset($shift_data['account']['fullname']) ? $shift_data['account']['fullname'] : '';
            $people_data['employees'] = [];
            if(!empty($shift_data['shift'])){
                foreach($shift_data['shift'] as $key=>$shift){
                    $people_data['employees'][$key]['PeopleID'] = isset($shift['people']['PeopleID']) ? $shift['people']['PeopleID'] : '';
                    $people_data['employees'][$key]['FirstName'] = isset($shift['people']['FirstName']) ? $shift['people']['FirstName'] : '';
                    $people_data['employees'][$key]['LastName'] = isset($shift['people']['LastName']) ? $shift['people']['LastName'] : '';
                    $people_data['employees'][$key]['user_id'] = isset($shift['people']['user_id']) ? $shift['people']['user_id'] : '';
                }
            }
        }
        return response()->json(['success' => true, 'data' => $people_data], 200);
    } 

    /**
     * Get Offer Email Content
     *
     * @param  mixed $request
     *
     * @return void
     */
    public function getOfferEmailContent(OfferEmail $request){
        $offer_email_content = [];
        $email_content = OfferEmailTemplate::where('EID',$request->EID)->where('status',1)->where('DID',$request->DID)->first();
        if($email_content){
            $offer_email_content = $email_content->toArray();
        }else{
            $email_content = OfferEmailTemplate::where('EID',0)->where('status',1)->where('DID',0)->first();
            if($email_content){
                $offer_email_content = $email_content->toArray();
            }
        }
        return response()->json(['success' => true, 'email_data' => $offer_email_content], 200);
    }

}
