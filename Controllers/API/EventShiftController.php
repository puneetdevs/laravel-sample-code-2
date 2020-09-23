<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use App\Models\Tblevent;
use App\Models\Tbleventdate;
use App\Models\EventSchedule;
use App\Models\People;
use App\Models\Position;
use App\Models\TbleventsShifthour;
use App\Models\OfferEmailTemplate;
use App\Models\TblpeopleNotification;
use App\Models\ShiftApproval;
use App\User;
use App\Jobs\SaveNotification;
use App\Jobs\SendPersonalShift; 
use App\Http\Requests\Api\EventShift\ShiftRequest;
use App\Http\Requests\Api\EventShift\InviteRequest;
use App\Http\Requests\Api\EventShift\UpdateShift;
use App\Http\Requests\Api\EventShift\SubmittedRequest;
use App\Http\Requests\Api\EventShift\SendShiftRequest;
use App\Transformers\ShiftBySupervisorTransformer;
use App\Transformers\GroupTransformer\TbleventsShifthourTransformer;
use Illuminate\Http\Response;
use EventHelper;
use Auth;
use DB;


/**
 * EventShift
 *
 * @Resource("EventShift", uri="/event_shift")
 */

class EventShiftController extends ApiController
{   
    /**
     * Get Date With Schedule
     *
     * @param  mixed $request
     *
     * @return void
     */
    public function getDateWithSchedule(ShiftRequest $request){
        #Get all event dates in array
        $event_dates = Tbleventdate::whereIn('EID', $request->EID)->where('Eventdate','>=',date('Y-m-d'));
        $event_dates = $event_dates->whereHas('schedule')->get()->pluck('Eventdate')->toArray();
        if(!empty($event_dates)){
            #Query get dates for other event to check the shift is join or not for same date
            $other_event_dates = Tbleventdate::whereIn('Eventdate', $event_dates)->get()->toArray();
            $Other_Eventdate_accepted = array();
            $Other_Eventdate = array();
            foreach($other_event_dates as $k => $date){
                $shift = TbleventsShifthour::where('PeopleID',$request->employee_id)
                                        ->where('DID',$date['DID'])
                                        ->where('Confirmed',1)
                                        ->first();
                $Other_Eventdate[$k] = $date['Eventdate'];
                if($shift){
                    $Other_Eventdate_accepted[$k] = $date['Eventdate'];
                }
            }
            
            $final_dates = array_unique(array_diff($Other_Eventdate,$Other_Eventdate_accepted));
            #date is not accepted by employee array here
            if(!empty($final_dates)){
                $date_data = Tblevent::whereIn('EID', $request->EID)
                                        ->with(['date'=>function($query) use($final_dates){
                                            $query->whereIn('Eventdate',$final_dates);
                                        }])
                                        ->select('EID','EventName')
                                        ->orderBy('EID','desc')
                                        ->get()->toArray();
                                       
                $date_final = array();                           
                foreach($date_data as $key=>$event){
                    $date_final[$key] = $event;
                    $i = 0;
                    foreach($event['date'] as $k=>$shift){
                        $position_ids = EventSchedule::where('date_id',$shift['DID'])->get()->pluck('position_id')->toArray();
                        $positions = Position::whereIn('PID',$position_ids)->get()->toArray();
                        $date_final[$key]['date'][$i]['positions'] = [];
                        if(!empty($positions)){
                            $schedule = array();
                            foreach($positions as $p_key => $position){
                                $schedule  = EventSchedule::where('event_id',$shift['EID'])
                                                            ->where('date_id',$shift['DID'])
                                                            ->where('position_id',$position['PID'])
                                                            ->with('department')
                                                            ->first();
                                $date_final[$key]['date'][$i]['positions'][$p_key] = $position;
                                $date_final[$key]['date'][$i]['positions'][$p_key]['schedule'] = $schedule;
                            }
                        }else{
                            unset($date_final[$key]['date'][$k]);
                        }
                        $i++;
                    }
                }
                return response()->json(['data' => $date_final], 200);
            }
            return response()->json(['data' => []], 200);
        }
        return response()->json(['error' => 'Selected events dates are not found, Please check schedule is prepared or not and may be dates are expired.'], 422);
    }

    public function sendEventInvite(InviteRequest $request){
        #foreach function for multiple event data
        $send_publish_date_shift = array();
        $save_unpublish_date_shift = array();
        $i = 0;
        foreach($request->data as $key=>$request_shift){
            #Foreach data ] 
            $save_shift_data = array();
            foreach($request_shift['date_data'] as $k=>$request_date){
                #Save shift here
                $save_shift_data['EID'] = $request_shift['EID'];
                $save_shift_data['DID'][$k] = $request_date['DID'];
                $save_shift_data['schedule_id'][$k] = $request_date['shift_id'];
                $save_shift_data['PID'] = $request_date['PID'];
                $save_shift_data['people_obj'][$k]['people_id'] = $request->employee_id; 
                $save_shift_data['people_obj'][$k]['Pre'] = 0;
                
                $formated_data = EventHelper::formatData($save_shift_data);
                if($formated_data){
                    TbleventsShifthour::insert($formated_data['people_shifts']);
                }
                
                $save_shift_data = [];
                $formated_data = [];
                #Get Shift here
                $shift_data = TbleventsShifthour::where('EID',$request_shift['EID'])
                                    ->where('DID',$request_date['DID'])
                                    ->where('PID',$request_date['PID'])
                                    ->where('PeopleID',$request->employee_id)
                                    ->where('Confirmed',0)
                                    ->with(['people'=>function($query){
                                        $query->select('PeopleID','Email','FirstName','Cell','WorkExt','email_notification','sms_notification');
                                    },'event'=>function($query){
                                        $query->select('EID','EventName','VID');
                                    },'event.location'=>function($query){
                                        $query->select('VID','VAddressLine1','VCity','VCountry');
                                    },'date'=>function($query){
                                        $query->select('DID','Eventdate');
                                    },'position'=>function($query){
                                        $query->select('PID','Position');
                                    }])->first();
                                    
                #add array is publish send invite now
                //if($request_date['is_publish'] == '1' && $shift_data){
                    $send_publish_date_shift[$i] = ($shift_data) ? $shift_data->toArray(): [];
                    
                #Add array is not publish save invite for now, When date is publish then send invitation     
                // }else if($shift_data){
                //     $save_unpublish_date_shift[$i] = $shift_data->toArray();
                // }
                $i++;
            }
        }
        
        #Send Invite Now Beacuse Date is already publish
        if(!empty($send_publish_date_shift)){
            $this->sendBulkInvite($send_publish_date_shift);
        }
        #Save Notification for invitation Beacuse Date is not publish yet
        // if(!empty($save_unpublish_date_shift)){
        //     #Save and Update
        //     foreach($save_unpublish_date_shift as $shift){
        //         $offer_content = $this->saveOfferEmailContent($shift['EID'], $shift['DID']);
        //         $email_template['subject'] = $offer_content['subject'];
        //         $email_template['message'] = $offer_content['message'];
        //         $email_template['sms_message'] = $offer_content['sms_message'];
        //         $notifcation_already_exist = TblpeopleNotification::where('PeopleID',$shift['PeopleID'])->where('EID',$shift['EID'])->where('DID',$shift['DID'])->where('shift_id',$shift['ID'])->first();
        //         if(!$notifcation_already_exist){
        //             $this->dispatch(new SaveNotification($shift,$email_template,Auth::user()->region_id,Auth::user()->id));
        //         }
        //     }
        // }
        if(!empty($send_publish_date_shift) && empty($save_unpublish_date_shift)){
            return response()->json(['message' => "Invitation has been sent successfully."], 200);
        }else if(empty($send_publish_date_shift) && !empty($save_unpublish_date_shift)){
            return response()->json(['message' => "For unpublished dates user will receive the invitation after publish the date."], 200);
        }else if(!empty($send_publish_date_shift) && !empty($save_unpublish_date_shift)){
            return response()->json(['message' => "Invitation has been sent successfully for the published dates.And for unpublished dates user will receive the invitation after publish the date."], 200);
        }else{
            return response()->json(['message' => "Invitation not sent, Please try again."], 466);
        }
        
    }

    /*****Get Content message here*****/
    private function saveOfferEmailContent($event_id, $date_id){
        $offer_content = OfferEmailTemplate::where('EID',$event_id)->where('status',1)->where('DID',$date_id)->first();
        if(!$offer_content){
            $offer_content = OfferEmailTemplate::where('EID',0)->where('status',1)->where('DID',0)->first();
        }
        return $offer_content->toArray();
    }

    private function sendBulkInvite($send_publish_date_shift){
        $message = array();
        $email = '';
        $email_notification = '';
        foreach($send_publish_date_shift as $key=>$shift){
            if(!empty($shift)){
                $encripted_id = EventHelper::base62encode($shift['ID']);
                $url = env("FRONTEND_URL").'/invite';
                $email = isset($shift['people']['Email']) ? $shift['people']['Email'] : ''; 
                $email_notification = isset($shift['people']['email_notification']) ? $shift['people']['email_notification'] : '';
                $message[$key]['accept_link'] = $url.'/'.$encripted_id.'/1';            
                $message[$key]['reject_link'] = $url.'/'.$encripted_id.'/0';            
                $message[$key]['event_date'] = isset($shift['date']['Eventdate']) ? date('Y-m-d',strtotime($shift['date']['Eventdate'])) : '';           
                $message[$key]['user_name'] = isset($shift['people']['FirstName']) ? $shift['people']['FirstName'] : '';   
                $message[$key]['event_name'] = isset($shift['event']['EventName']) ? $shift['event']['EventName'] : '';
                $message[$key]['event_location'] = isset($shift['event']['location']) && !empty($shift['event']['location']) ? $shift['event']['location']['VAddressLine1'].' '.$shift['event']['location']['VCity'].' '.$shift['event']['location']['VCountry'] : '';
                $message[$key]['event_position'] = isset($shift['position']) && !empty($shift['position']) ? $shift['position']['Position'] : '';
                $start_time = '';
                if(!empty($shift['Start3'])){
                    $start_time = $shift['Start3'];
                }
                if(!empty($shift['Start2'])){
                    $start_time = $shift['Start2'];
                }
                if(!empty($shift['Start1'])){
                    $start_time = $shift['Start1'];
                }
                $message[$key]['start_time'] = $start_time != '' ? date('H:i', strtotime($start_time)) : '';
                $end_time = '';
                if(!empty($shift['Finish1'])){
                    $end_time = $shift['Finish1'];
                }
                if(!empty($shift['Finish2'])){
                    $end_time = $shift['Finish2'];
                }
                if(!empty($shift['Finish3'])){
                    $end_time = $shift['Finish3'];
                }
                $message[$key]['end_time'] = $end_time != '' ? date('H:i', strtotime($end_time)) : '';
            }
        }
        if(!empty($message) && $email != '' && $email_notification == 1){
            try {
                \Mail::send(['html' => 'email.bulk-invitation-email'], array('data' => $message), function ($message) use ($email) {
                    $message->to($email)->subject('Event Inviation');
                });
            } catch (\Exception $e) {}
        }
        return true;
    }

    /**** Update Shift by Supervisor ****/
    /**
     * Supervisor Update shift and submit To Admin
     *
     * @param  mixed $request
     * @param  mixed $date_id
     *
     * @return void
     */
    public function submitShiftToAdmin(UpdateShift $request, $date_id)
    {   
        #Check Date field DataCheck not checked yet
        $date = Tbleventdate::where('DID', $date_id)
                            ->where('DataChecked',0)
                            ->with('event')->first();
        if($date){
            $requested_data = $request->all();
            $send_notification_email = false;
            foreach($requested_data['shift'] as $value){
                #Check Supervisor already updated shift or not
                $shift = ShiftApproval::where('shift_id',$value['shift_id'])->first();
                if($shift){
                    #update shift
                    ShiftApproval::where('id',$shift->id)->update($value);
                }else{
                    #create shift
                    $send_notification_email = true;
                    $result = ShiftApproval::create($value);
                    Tbleventdate::where('DID',$date->DID)->update(['is_check' => 1]);
                }
                $shift = '';
            }
            #Send Notification to admin for update shift time by Supervisor
            if(Auth::user()->role_id == 3 && $send_notification_email ){
                $admin_emails = User::where('role_id',1)->where('region_id',Auth::user()->region_id)->get()->pluck('email')->toArray();
                $data['event_date'] = date("Y-m-d", strtotime($date->Eventdate));
                $data['event_name'] = $date['event']->EventName;
                $data['supervisor'] = Auth::user()->fullname;
                try {
                    \Mail::send(['html' => 'email.supervisor-shift-to-admin'], array('data' => $data), function ($message) use ($admin_emails) {
                        $message->to($admin_emails)->subject('Shift For Check');
                    });
                } catch (\Exception $e) {
                    report($e);
                    return response()->json(['success' => false, 'message' => $e->getMessage(), 'data' => []], 500);
                }
            }
            return response()->json(['message' => 'Shift updated successfully.'], 200);
        }
        return response()->json(['error' => 'Date already checked by Admin.'], 466);
    }

    /**
     * Get Shift Submitted By Supervisor
     *
     * @param  mixed $request
     *
     * @return void
     */
    public function getSubmittedShiftBySupervisor(Request $request){
        if($request->has('DID')){
            $perPage = 10;
            if($request->has('per_page')){
                $perPage = $request->per_page;
            }
            $updated_shift = ShiftApproval::where('DID',$request->DID);
            $updated_shift = $updated_shift->whereHas('shift')->paginate($perPage);
            if($updated_shift->toArray()['total'] > 0){
                Tbleventdate::where('DID',$request->DID)->update(['is_check' => 2]);
                return $this->response->paginator($updated_shift, new ShiftBySupervisorTransformer());
            }else{
                $shift_data =  TbleventsShifthour::where('DID',$request->DID)->paginate($perPage);
                $dates_array = Tbleventdate::where('DID',$request->DID)->pluck('Eventdate')->toArray();
                
                /****Send Response here****/
                return $this->response->paginator($shift_data, new TbleventsShifthourTransformer($dates_array));
            }
        }
        return response()->json(['error' => 'Please send date id.'], 466);
    }

    /**
     * Approve Shift By Admin
     *
     * @param  mixed $request
     * @param  mixed $date_id
     *
     * @return void
     */
    public function approveShiftByAdmin(Request $request, $date_id){
        #Check Date id is send or not
        if($date_id && $date_id != ''){
            #Get Shift not approved by admin and submitted by supervisor
            $pending_for_approval = ShiftApproval::where('DID',$date_id)->where('Status',0)->get()->toArray();
            if(!empty($pending_for_approval)){
                #Update Date DataChecked and shift approval status here
                Tbleventdate::where('DID',$date_id)->update(['DataChecked' => 1]);
                ShiftApproval::where('DID',$date_id)->update(['Status' => 1]);
                #Update shift timing here
                $timing_data = array();
                foreach($pending_for_approval as $key=>$shift){
                    $timing_data['Start1'] = $shift['Start1'];
                    $timing_data['Finish1'] = $shift['Finish1'];
                    $timing_data['Start2'] = $shift['Start2'];
                    $timing_data['Finish2'] = $shift['Finish2'];
                    $timing_data['Start3'] = $shift['Start3'];
                    $timing_data['Finish3'] = $shift['Finish3'];
                    TbleventsShifthour::where('id',$shift['shift_id'])->update($timing_data);
                    $timing_data = array();
                }
                return response()->json(['message' => "Shift is approved"], 200);
            }
            return response()->json(['error' => 'This date is already checked.'], 466);
        }
        return response()->json(['error' => 'Please send date id.'], 466);
    }

    /**
     * Send Shift By Email
     *
     * @param  mixed $request
     *
     * @return void
     */
    public function sendShiftByEmail(SendShiftRequest $request){
        $shift = ShiftApproval::where('DID',$request->DID)
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
                                            ])->get()->toArray();
                        
        if(!empty($shift)){
            $this->dispatch(new SendPersonalShift($shift, $request->email_send_to, $request->email_subject, $request->email_message)); 
            return $this->response->array(['status' => 200, 'message' => 'Shift sent successfully.']);
        }
        return response()->json(['error' => 'Shift not found.'], 422);
    }
        
}