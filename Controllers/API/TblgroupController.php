<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use App\Models\Tblgroup;
use App\Models\TblgroupPeople;
use App\Models\EventSchedule;
use App\Models\SysEmailTemplate;
use App\Models\TblpeopleNotification;
use App\Models\TbleventsShifthour;
use App\Models\Tbleventdate;
use App\Models\OfferEmailTemplate;
use App\Models\PreBooking;
use App\Models\EventsShiftLog;
use App\Jobs\SaveNotification;
use App\Jobs\SendInvite;
use App\Jobs\sendSMSInvite;
use App\Jobs\SendInviteSMS;
use App\Jobs\SendDeletedConformationEmail;
use App\Jobs\SendDeletedConformationSMS;
use App\Jobs\SendDatePublishMessage;
use App\Jobs\SendDatePublishSMS;
use App\Transformers\GroupTransformer\TblgroupTransformer;
use App\Transformers\GroupTransformer\TblgroupPeopleTransformer;
use App\Transformers\GroupTransformer\TbleventsShifthourTransformer;
use App\Transformers\GroupTransformer\PreBookingTransformer;
use App\Repositories\TblgroupPeopleRepository;
use App\Repositories\TbleventsShifthourRepository;
use App\Http\Requests\Api\Tblgroups\Index;
use App\Http\Requests\Api\Tblgroups\Show;
use App\Http\Requests\Api\Tblgroups\Create;
use App\Http\Requests\Api\Tblgroups\Store;
use App\Http\Requests\Api\Tblgroups\Edit;
use App\Http\Requests\Api\Tblgroups\Shift;
use App\Http\Requests\Api\Tblgroups\DeleteShift;
use App\Http\Requests\Api\Tblgroups\Update;
use App\Http\Requests\Api\Tblgroups\Invite;
use App\Http\Requests\Api\Tblgroups\ConfirmInvite;
use App\Http\Requests\Api\Tblgroups\Destroy;
use App\Http\Requests\Api\Tblgroups\OfferContent;
use App\Http\Requests\Api\Tblgroups\PreBook;
use App\Http\Requests\Api\Tblgroups\UpdatePreBook;
use Aloha\Twilio\TwilioInterface;
use Twilio;
use EventHelper;
use Auth;
use DB;


/**
 * Tblgroup
 *
 * @Resource("Tblgroup", uri="/groups")
 */

class TblgroupController extends ApiController
{
    
    public function index(Index $request)
    { 
        $perPage = EventHelper::getPerPage($request);
        return $this->response->paginator(Tblgroup::orderBy('id','desc')->paginate($perPage), new TblgroupTransformer());
    }

    public function show(Show $request, Tblgroup $tblgroup)
    {
      return $this->response->item($tblgroup, new TblgroupTransformer());
    }

    public function store(Store $request)
    {
        $model=new Tblgroup;
        $group_data = $request->all();
        $group_data['region_id'] = Auth::user()->region_id;
        $model->fill($group_data);
        if ($model->save()) {
            $id = DB::getPdo()->lastInsertId();
            foreach($request->people_ids as $people_id){
                $data['PeopleID'] = $people_id;
                $data['GID'] =  $id;
                TblgroupPeople::create($data);  
            }
            return $this->response->item($model, new TblgroupTransformer());
        } else {
            return response()->json(['error' => 'Error occurred while saving.'], 422);
        }
    }

 
    public function update(Update $request,  Tblgroup $tblgroup)
    {
        $tblgroup->fill($request->all());

        if ($tblgroup->save()) {
            return $this->response->item($tblgroup, new TblgroupTransformer());
        } else {
            return response()->json(['error' => 'Error occurred while saving.'], 422);
        }
    }


    public function destroy(Destroy $request, $tblgroup)
    {
        $tblgroup = Tblgroup::findOrFail($tblgroup);

        if ($tblgroup->delete()) {
            return $this->response->array(['status' => 200, 'message' => 'Tblgroup successfully deleted']);
        } else {
            return response()->json(['error' => 'Error occurred while deleting.'], 422);
        }
    }


    /**** Get Peoples with group_ids ****/
    public function getPeopleWithGroups(Request $request)
    {
        $group_ids = array();
        if($request->has('group_ids')){
            $group_ids =  explode(",",$request->group_ids);

             /****Query****/
            $perPage = EventHelper::getPerPage($request);
            $group_data =  TblgroupPeople::whereIn('GID',$group_ids)->groupBy('PeopleID')->paginate($perPage);

            /****Send Response here****/
            return $this->response->paginator($group_data, new TblgroupPeopleTransformer());
        }
        return response()->json(['error' => 'Please send valid data.'], 422);
    }

    /**** Confirm Shift Quick Crew Member ****/
    public function confirmShift(ConfirmInvite $request)
    {
        $formated_data = EventHelper::shiftFormatData($request->all());
        if($formated_data['status'] == 200){
            if(!isset($formated_data['data']['ID'])){
                TbleventsShifthour::create($formated_data['data']);
            }

            $users_data =  TbleventsShifthour::with(['people'=>function($query){
                                                        $query->select('PeopleID','Email','FirstName','email_notification','Cell','WorkExt','sms_notification');
                                                    },'event'=>function($query){
                                                        $query->select('EID','EventName','VID');
                                                    },'event.location'=>function($query){
                                                        $query->select('VID','VAddressLine1','VCity','VCountry');
                                                    },'date'=>function($query){
                                                        $query->select('DID','Eventdate');
                                                    },'position'])
                                                ->where('Confirmed',1)
                                                ->where('DID',$request->date_id)
                                                ->where('PeopleID',$request->employee_id)
                                                ->where('EID',$request->event_id)
                                                ->get()->toArray();

            //Set Queue Dispatch here                
            if(!empty($users_data)){
                foreach($users_data as $userData){
                    $userData['Confirmed'] = !isset($userData['is_published']) && $userData['Confirmed'] == 2 ? 1 : $userData['Confirmed'];
                    if(isset($userData['people']['Email']) && !empty($userData['people']['Email']) && $userData['people']['email_notification'] == '1'){
                        $this->dispatch(new SendDatePublishMessage($userData));
                    }
                    if(isset($userData['people']['Cell']) && $userData['people']['Cell'] !== null && isset($userData['people']['WorkExt']) && $userData['people']['WorkExt'] !== null && $userData['people']['sms_notification'] == '1'){
                        $this->dispatch(new SendDatePublishSMS($userData));
                    }
                }
                return $this->response->array(['status' => 200, 'message' => 'Invite send successfully.']);
            }else{
                return response()->json(['error' => 'Quantity already full, Please increase schedule quantity first.'], 422);
            }
            
        }else{
            return response()->json(['error' => $formated_data['message']], 422);
        }
    }


    /**** Invite Peoples ****/
    public function invitePeopleWithGroups(Invite $request)
    {
        $requested_data = $request->all();
        $formated_data = EventHelper::formatData($requested_data);
        if($formated_data){
            TbleventsShifthour::insert($formated_data['people_shifts']);

            /*** Get Shift Hours and Save notification in Queue ***/
            $shift_data = TbleventsShifthour::where('EID',$request->EID)->where('Confirmed',0)->whereIn('DID',$request->DID)->get()->toArray();
            
            if($shift_data){
                #Save and Update
                foreach($shift_data as $shift){
                    $offer_content_result = $this->saveOfferEmailContent($shift);
                    $email_template['subject'] = $offer_content_result['subject'];
                    $email_template['message'] = $offer_content_result['message'];
                    $email_template['sms_message'] = $offer_content_result['sms_message'];
                    $notifcation_already_exist = TblpeopleNotification::where('PeopleID',$shift['PeopleID'])->where('EID',$request->EID)->where('DID',$shift['DID'])->where('shift_id',$shift['ID'])->first();
                    if(!$notifcation_already_exist){
                        $this->dispatch(new SaveNotification($shift,$email_template,Auth::user()->region_id,Auth::user()->id));
                    }
                }
            }
            #Check date is publish or not if publish send invitation
            //$date_ids = Tbleventdate::whereIn('DID',$request->DID )->where('is_publish',1)->pluck('DID')->toArray();
            //if(!empty($date_ids)){
                $this->checkIsDatePublish($request->DID,$request->EID);
           // }
            return $this->response->array(['status' => 200, 'message' => 'Invite send successfully.']);
        }else{
            return response()->json(['error' => 'Schedule not found, Please create schedule first for this position.'], 422);
        }
    }

    /*****If message saved then update it else create new message in table*****/
    private function saveOfferEmailContent($shift){
        $offer_content_result = array();
        $offer_content = OfferEmailTemplate::where('EID',$shift['EID'])->where('DID',$shift['DID'])->first();
        if($offer_content){
            $offer_content_result = $offer_content->toArray();
        }else{
            $offer_content_result = OfferEmailTemplate::where('EID',0)->where('DID',0)->first()->toArray();
        }
        return $offer_content_result;
    }

    /*****If message saved then update it else create new message in offer_email_template table*****/
    public function updateOfferEmailContent(OfferContent $request){
        $offer_content = OfferEmailTemplate::where('DID',$request->DID)->where('EID',$request->EID)->first();
        if($offer_content){
            OfferEmailTemplate::where('DID',$request->DID)->where('EID',$request->EID)
                                ->update([
                                    'subject' => $request->email_subject, 
                                    'message' => $request->email_message, 
                                    'sms_message' => $request->sms_message]);
        }else{
            $data['EID'] = $request->EID;
            $data['DID'] = $request->DID;
            $data['subject'] = $request->email_subject;
            $data['message'] = $request->email_message;
            $data['sms_message'] = $request->sms_message;
            $data['status'] = 1; //Email
            OfferEmailTemplate::create($data); 
        }
        return $this->response->array(['status' => 200, 'message' => 'Offer content updated successfully.']);
    }
    

    private function checkIsDatePublish($date_ids, $event_id){
         /**** Validate is email exist in tbl people or not****/
         $users_data =  TblpeopleNotification::with(
                                            ['people'=>function($query){
                                                $query->select('PeopleID','Email','FirstName','Cell','WorkExt','email_notification','sms_notification');
                                            },'event'=>function($query){
                                                $query->select('EID','EventName','VID');
                                            },'event.location'=>function($query){
                                                $query->select('VID','VAddressLine1','VCity','VCountry');
                                            },'date'=>function($query){
                                                $query->select('DID','Eventdate');
                                            },'shift','shift.position'])
                                            ->where('status',0)
                                            ->whereIn('DID',$date_ids)
                                            ->where('EID',$event_id)
                                            ->get()->toArray();
                                            
        //Set Queue Dispatch here                
        if(!empty($users_data)){
            foreach($users_data as $userData){
                if(isset($userData['people']['Email']) && !empty($userData['people']['Email']) && $userData['people']['email_notification'] == '1'){
                    $this->dispatch(new SendInvite($userData));
                }
                if(isset($userData['people']['Cell']) && $userData['people']['Cell'] !== null && isset($userData['people']['WorkExt']) && $userData['people']['WorkExt'] !== null && $userData['people']['sms_notification'] == '1'){
                    $this->dispatch(new sendSMSInvite($userData));
                    $this->dispatch(new SendInviteSMS($userData));
                }
        
            }
        }
    }


    /**** Invite Shift with date_id ****/
    public function getDateShift(Request $request, TbleventsShifthourRepository $tbleventsShifthourRepository)
    {
        if($request->has('date_ids')){
            $date_ids =  explode(",",$request->date_ids);
            /****Query****/
            $perPage = EventHelper::getPerPage($request);
            $shift_data =  $tbleventsShifthourRepository->whereIn('DID',$date_ids)->paginate($perPage);
            $dates_array = Tbleventdate::whereIn('DID',$date_ids)->pluck('Eventdate')->toArray();
            
            /****Send Response here****/
            return $this->response->paginator($shift_data, new TbleventsShifthourTransformer($dates_array));
        }
        return response()->json(['error' => 'Please send valid request data.'], 422);
    }

    /**** Get shifts by date_ids ****/
    public function getDateAllShift(Request $request, TbleventsShifthourRepository $tbleventsShifthourRepository)
    {
        if($request->has('date_ids')){
            $date_ids =  explode(",",$request->date_ids);
           
            $dates_array = Tbleventdate::whereIn('DID',$date_ids)->orderBy('SortOrder', 'ASC')->get()->toArray();
            
            $is_published = 1;
            $final_data = array();
            if(!empty($dates_array)){
                foreach($dates_array as $key=>$data){                    
                    $final_data[$key] = $data;                    
                    $schedule = EventSchedule::where('date_id',$data['DID'])
                                            ->with(['shift' => function($q){
                                                $q->orderBy('PID','desc')->orderBy('schedule_id','desc');
                                            },
                                            'position',
                                            'department',
                                            'shift.position',
                                            'shift.department',
                                            'shift.people' =>function($q){
                                                return $q->select('PeopleID','FirstName','LastName');
                                            }])->get()->toArray();
                    if(!empty($schedule)){        
                        $i=0;                
                        foreach($schedule as $keys=>$schedule){
                            if(!empty($schedule['shift'])){
                                foreach($schedule['shift'] as $keys=>$shift){
                                    if(!empty($final_data[$key]['position']) && in_array($shift['position']['PID'], array_column($final_data[$key]['position'], 'PID'))){
                                        $shift_key = array_search($shift['position']['PID'], array_column($final_data[$key]['position'], 'PID'));
                                        $shift_total_key = count($final_data[$key]['position'][$shift_key]['shift']);
                                        $final_data[$key]['position'][$shift_key]['shift'][$shift_total_key] = $shift;
                                        $final_data[$key]['position'][$shift_key]['shift'][$shift_total_key]['Start1'] = ($shift['Start1']) ? date('H:i',strtotime($shift['Start1'])) : $shift['Start1'];
                                        $final_data[$key]['position'][$shift_key]['shift'][$shift_total_key]['Finish1'] = ($shift['Finish1']) ? date('H:i',strtotime($shift['Finish1'])) : $shift['Finish1'];
                                        $final_data[$key]['position'][$shift_key]['shift'][$shift_total_key]['Start2'] = ($shift['Start2']) ? date('H:i',strtotime($shift['Start2'])) : $shift['Start2'];
                                        $final_data[$key]['position'][$shift_key]['shift'][$shift_total_key]['Finish2'] = ($shift['Finish2']) ? date('H:i',strtotime($shift['Finish2'])) : $shift['Finish2'];
                                        $final_data[$key]['position'][$shift_key]['shift'][$shift_total_key]['Start3'] = ($shift['Start3']) ? date('H:i',strtotime($shift['Start3'])) : $shift['Start3'];
                                        $final_data[$key]['position'][$shift_key]['shift'][$shift_total_key]['Finish3'] = ($shift['Finish3']) ? date('H:i',strtotime($shift['Finish3'])) : $shift['Finish3'];
                                        if($final_data[$key]['position'][$shift_key]['schedule_id'] != $shift['schedule_id']){
                                            $final_data[$key]['position'][$shift_key]['total_shift'] += $shift['Quantity'];
                                            $final_data[$key]['position'][$shift_key]['schedule_id'] = $shift['schedule_id'];
                                        }
                                        $final_data[$key]['position'][$shift_key]['total_confirm_shift'] += $shift['Confirmed'] == 1 ? 1 : 0;
                                    }else{
                                        $final_data[$key]['position'][$i] = $shift['position'];
                                        $final_data[$key]['position'][$i]['schedule_id'] = $schedule['id'];
                                        $final_data[$key]['position'][$i]['shift'][0] = $shift;
                                        $final_data[$key]['position'][$i]['shift'][0]['Start1'] = ($shift['Start1']) ? date('H:i',strtotime($shift['Start1'])) : $shift['Start1'];
                                        $final_data[$key]['position'][$i]['shift'][0]['Finish1'] = ($shift['Finish1']) ? date('H:i',strtotime($shift['Finish1'])) : $shift['Finish1'];
                                        $final_data[$key]['position'][$i]['shift'][0]['Start2'] = ($shift['Start2']) ? date('H:i',strtotime($shift['Start2'])) : $shift['Start2'];
                                        $final_data[$key]['position'][$i]['shift'][0]['Finish2'] = ($shift['Finish2']) ? date('H:i',strtotime($shift['Finish2'])) : $shift['Finish2'];
                                        $final_data[$key]['position'][$i]['shift'][0]['Start3'] = ($shift['Start3']) ? date('H:i',strtotime($shift['Start3'])) : $shift['Start3'];
                                        $final_data[$key]['position'][$i]['shift'][0]['Finish3'] = ($shift['Finish3']) ? date('H:i',strtotime($shift['Finish3'])) : $shift['Finish3'];
                                        $final_data[$key]['position'][$i]['total_shift'] = $shift['Quantity'];
                                        $final_data[$key]['position'][$i]['total_confirm_shift'] = $shift['Confirmed'] == 1 ? 1 : 0;
                                        $i++;
                                    }  
                                }                      
                            
                            }                       
                        }     
                    }else{
                        $final_data[$key]['position'] = [];
                    }
                }
            }
                   
           
            $is_publish = EventsShiftLog::whereIn('DID',$date_ids)->where('is_published',0)->first();
            $is_published = ($is_publish) ? 1 : 0 ;
            /****Send Response here****/
            return response()->json(['data' => $final_data,'is_published' => $is_published]);
        }
        return response()->json(['error' => 'Please send valid request data.'], 422);
    }


    /**** Update Single Shift Schedule ****/
    public function updateShiftSchedule(Shift $request,TbleventsShifthour $tbleventsShifthour)
    {   
        if($request->quick_decline){
            TbleventsShifthour::where('ID', $request->ID)->update(['Confirmed' => 4]);
            $tbleventsShifthour = TbleventsShifthour::where('ID', $request->ID)->get();
            return $this->response->item($tbleventsShifthour, new TbleventsShifthourTransformer());
        }else{
            $requested_data = $request->all();
            unset($requested_data['quick_decline']);
            
           /*Check quantity and remanining*/
            $check_remaining = EventHelper::checkRemaining($requested_data);
            if($check_remaining){
                $date_detail = Tbleventdate::where('DID',$request->DID)->first();
                /*Date is already publish send email to confirm again if Con not selected else send updated time sheet on email*/
                $tbleventsShifthour->fill($requested_data);
        
                /*Check updated user available or not for updated single schedule*/
                $user_available = EventHelper::checkUserAvailable($requested_data);
                if($user_available){    
                    /*Update single schedule and send response here*/
                    if (TbleventsShifthour::where('ID', $request->ID)->update($requested_data)) {
        
                        /*Send Email if schedule date already publish else change TblpeopleNotification data only*/
                        //$this->sendConformationEmail($date_detail,$request);

                        /*Save & Update Shift log*/
                        $this->saveUpdateShiftLog($requested_data);

                        $tbleventsShifthour = TbleventsShifthour::where('ID', $request->ID)->get();
                        return $this->response->item($tbleventsShifthour, new TbleventsShifthourTransformer());
                    } else {
                        return response()->json(['error' => 'Error occurred while saving shift hours.'], 422);
                    } 
                }
                return response()->json(['error' => 'Selected user not available for this schedule.'], 422);
            }else{
                return response()->json(['error' => 'Quantity is full OR Employee added in same schedule.'], 422);
            } 
        }
    }

    private function saveUpdateShiftLog($request_data){
        EventsShiftLog::where('ShiftID',$request_data['ID'])->delete();
        $request_data['ShiftID'] = $request_data['ID'];
        unset($request_data['ID']);
        EventsShiftLog::create($request_data);
    }

    /**** Delete Multiple Shifts ****/
    public function deleteMultipleShift(DeleteShift $request)
    {   
        /*Delete shifts send response here*/
        $confirmed_shift = TbleventsShifthour::whereIn('ID', $request->ID)->whereIn('Confirmed',[1,2])
                                            ->with(['people'=>function($query){
                                                $query->select('PeopleID','Email','FirstName','email_notification','Cell','WorkExt','sms_notification');
                                            },'event'=>function($query){
                                                $query->select('EID','EventName','VID');
                                            },'event.location'=>function($query){
                                                $query->select('VID','VAddressLine1','VCity','VCountry');
                                            },'date'=>function($query){
                                                $query->select('DID','Eventdate');
                                            },'position'])
                                            ->get()->toArray();
        if(!empty($confirmed_shift)){
            /*Send Email notification to only confimed and accepted people */
            foreach($confirmed_shift as $userData){
                if(isset($userData['people']['Email']) && !empty($userData['people']['Email']) && $userData['people']['email_notification'] == '1'){
                    $this->dispatch(new SendDeletedConformationEmail($userData));
                }
                if(isset($userData['people']['Cell']) && $userData['people']['Cell'] !== null && isset($userData['people']['WorkExt']) && $userData['people']['WorkExt'] !== null && $userData['people']['sms_notification'] == '1'){
                    $this->dispatch(new SendDeletedConformationSMS($userData));
                }
            }
        }    

        $schedules = TbleventsShifthour::whereIn('ID', $request->ID)->get()->pluck('schedule_id')->toArray();

        /*Shift and shift log deleted here*/ 
        TbleventsShifthour::whereIn('ID', $request->ID)->delete();
        EventsShiftLog::whereIn('ShiftID', $request->ID)->delete(); 

        if(!empty($schedules)){
            $schedules = array_unique($schedules);
            foreach($schedules as $value){
                $shifts_count = TbleventsShifthour::where('schedule_id',$value)->count();
                $schedule = EventSchedule::where('id',$value)->first();
                if($shifts_count < $schedule->quantity){
                    $updated_schedule = EventSchedule::where('id',$value)->update(['quantity' => $shifts_count]);
                    TbleventsShifthour::where('schedule_id',$value)->update(['Quantity' => $shifts_count]);
                    if($updated_schedule){
                        EventSchedule::where('id',$value)->where('quantity',0)->update(['deleted_at' => date('Y-m-d H:i:s')]);
                    }
                }
            }
        }
        
        return $this->response->array(['status' => 200, 'message' => 'Shift has been deleted successfully.']);   
    }


    /***Send Email if schedule date already publish else change TblpeopleNotification data only***/
    public function sendConformationEmail($date_detail, $request){
       
        if(isset($date_detail->is_publish) && $date_detail->is_publish == 1){
            /***Get shfit and users details***/
            $users_data = EventHelper::shiftUserDetails($request->ID);
            if($users_data['people']['email_notification'] == '1'){
                if($request->Confirmed == 1 ){
                    /***Make email message here***/
                    $user_data = EventHelper::getMessageForEmail($users_data);
    
                    $email = $users_data['people']['Email'];
                    $subject = 'Shift update confirmation';
                    try {
                        \Mail::send(['html' => 'email.shift-update-confirmation-email'], array('user_data' => $user_data), function ($message) use ($email,$subject) {
                            $message->to($email)->subject($subject);
                        });
                    } catch (\Exception $e) {
                        report($e);
                        return response()->json(['success' => false, 'message' => $e->getMessage(), 'data' => []], 500);
                    }
                    
                } else {
                    $encripted_id = EventHelper::base62encode($request->ID);
                    $url = env("FRONTEND_URL").'/invite';
    
                    /***Make email message here***/
                    $email_response = EventHelper::sendInviteEmailMessage($encripted_id, $url, $users_data);
                    
                    $user_data['message'] = $email_response['message'];
                    $user_data['accept_link'] = $email_response['accept_link'];
                    $user_data['reject_link'] = $email_response['reject_link'];
                    $email = $users_data['people']['Email'];
                    $subject = $email_response['subject'];
                    
                    try {
                        \Mail::send(['html' => 'email.invitation-email'], array('user_data' => $user_data), function ($message) use ($email,$subject) {
                            $message->to($email)->subject($subject);
                        });
                    } catch (\Exception $e) {
                        report($e);
                        return response()->json(['success' => false, 'message' => $e->getMessage(), 'data' => []], 500);
                    }
                }  
            } 
        }else{
            TblpeopleNotification::where('shift_id',  $request->ID)->update(['PID'=>$request->PID]);
        }
    }

    
    /**
     * Pre-Book Invite To Employee
     *
     * @param  mixed $request
     *
     * @return void
     */
    public function preBookInviteToEmployee(PreBook $request)
    {
        #Save pre booking here
        $requested = $request->all();
        $added_people = array();
        $added_date = array();
        foreach($requested['DID'] as $k=>$date){
            foreach($requested['people_ids'] as $key=>$people_id){
                $preBooking = PreBooking::where('DID',$date)->where('EID',$requested['EID'])->where('PeopleID',$people_id)->first();
                if(!$preBooking){
                    $data[$key]['EID'] = $requested['EID'];
                    $data[$key]['DID'] = $date;
                    $data[$key]['PeopleID'] = $people_id;
                    $data[$key]['Status'] = 1;
                    $data[$key]['note'] = $requested['note'];
                    $data[$key]['region_id'] = Auth::user()->region_id;
                    $data[$key]['created_at'] = date('Y-m-d H:i:s');
                    $data[$key]['updated_at'] = date('Y-m-d H:i:s');
                    $added_people[$people_id]['people'] = $people_id;
                    $added_people[$people_id]['DID'][$k] = $date;
                }
            }
            if(!empty($data)){
                $final[$k] = $data;
                PreBooking::insert($data); 
            }
        }
        #send pre-booking notification with email here
        if(!empty($final) && !empty($added_people)){
            foreach($added_people as $key=>$value){
                $invite_booking = PreBooking::where('PeopleID',$value['people'])
                                            ->whereIn('DID',$value['DID'])
                                            ->with([
                                                'people' => function($query){
                                                    $query->select('PeopleID','FirstName','Email');
                                                },
                                                'date' => function($query){
                                                    $query->select('DID','Eventdate');
                                                },
                                                'event' => function($query){
                                                    $query->select('EID','EventName');
                                                }])->get()->toArray();
                if(!empty($invite_booking)){
                    $email = $invite_booking[0]['people']['Email'];
                    $subject = 'Pre Booking ';
                    try {
                        \Mail::send(['html' => 'email.pre-booking'], array('pre_book' => $invite_booking), function ($message) use ($email,$subject) {
                            $message->to($email)->subject($subject);
                        });
                    } catch (\Exception $e) {
                        report($e);
                        return response()->json(['success' => false, 'message' => $e->getMessage(), 'data' => []], 500);
                    }
                }
            }
            return $this->response->array(['status' => 200, 'message' => 'Employee Pre-Booked successfully.']);
        }else{
            return response()->json(['error' => 'Already pre-booked.'], 422);
        }
    }

    /**
     * Get Pre-Book Employee
     *
     * @param  mixed $request
     *
     * @return void
     */
    public function getPreBookEmployee(Request $request)
    {
        $perPage = EventHelper::getPerPage($request);
        if($request->has('date_ids')){
            $date_ids =  explode(",",$request->date_ids);
            return $this->response->paginator(PreBooking::whereIn('DID',$date_ids)->paginate($perPage), new PreBookingTransformer());
        }else{
            return response()->json(['error' => 'Please send valid date.'], 422);
        }
    }

    /**
     * Update Pre-Book Employee
     *
     * @param  mixed $request
     * @param  mixed $pre_book_id
     *
     * @return void
     */
    public function updatePreBookEmployee(UpdatePreBook $request, $pre_book_id )
    {
        if(PreBooking::where('id',$request->id)->update(['note' => $request->note])){
            return $this->response->paginator(PreBooking::where('id',$request->id)->paginate(1), new PreBookingTransformer());
        }
        return response()->json(['error' => 'Note not updated, Please try again.'], 422);
    }

    /**
     * Delete Pre-Book Employee
     *
     * @param  mixed $request
     * @param  mixed $id
     *
     * @return void
     */
    public function deletePreBookEmployee(Request $request, $id)
    {
        if($id){
            $pre_book_data = PreBooking::where('id',$id)
                                        ->with([
                                                'people' => function($query){
                                                    $query->select('PeopleID','FirstName','Email');
                                                },
                                                'date' => function($query){
                                                    $query->select('DID','Eventdate');
                                                },
                                                'event' => function($query){
                                                    $query->select('EID','EventName');
                                                }
                                        ])->first();
            
            $delete_pre_booking = PreBooking::where('id',$id)->update(['deleted_at' => date('Y-m-d H:i:s')]);
            if($delete_pre_booking && $pre_book_data){
                $pre_book_data = $pre_book_data->toArray();
                $email = $pre_book_data['people']['Email'];
                try {
                    \Mail::send(['html' => 'email.delete-pre-booking-notification'], array('data' => $pre_book_data), function ($message) use ($email) {
                        $message->to($email)->subject('Removed From Pre Booking');
                    });
                } catch (\Exception $e) {}
            }
            return $this->response->array(['status' => 200, 'message' => 'Pre-Booking has been deleted successfully.']);
        }else{
            return response()->json(['error' => 'Please send valid id.'], 422);
        }
    }

}
