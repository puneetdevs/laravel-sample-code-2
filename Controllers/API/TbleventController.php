<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use App\Models\Tblevent;
use App\Models\Tbleventdate;
use App\Models\EventSchedule;
use App\Models\People;
use App\Models\TblpeopleNotification;
use App\Models\TbleventsShifthour;
use App\Models\TbleventWaitingList;
use App\Models\SysEmailTemplate;
use App\Models\DataFileSettings;
use App\Models\Tblprogramsetting;
use App\Models\OfferEmailTemplate;
use App\Models\HolidayList;
use App\Models\EventsShiftLog;
use App\User;
use App\Models\TbleventsInvoicelineitem;
use App\Jobs\SendInvite; 
use App\Jobs\sendSMSInvite;
use App\Jobs\SendInviteSMS;
use App\Jobs\SendDatePublishMessage;
use App\Jobs\SendDatePublishSMS;
use App\Jobs\SendDeletedConfirmation;
use App\Jobs\sendBulkMessageOnPublishedDate;
use App\Jobs\sendBulkSMSOnPublishedDate;
use App\Jobs\SaveNotification;
use App\Transformers\EventTransformer\TbleventTransformer;
use App\Transformers\EventTransformer\ScheduleTransformer;
use App\Transformers\EventTransformer\ShiftSummaryTransformer;
use App\Transformers\EventTransformer\TbleventdateTransformer;
use App\Transformers\EventTransformer\WaitingListTransformer;
use App\Transformers\EmployeeTransformer\EmployeeTransformer;
use App\Transformers\EmployeeTransformer\EmployeeShortResponseTransformer;
use App\Transformers\EmployeeTransformer\EmployeeFilterTransformer;
use App\Transformers\GroupTransformer\TbleventsShifthourTransformer;
use App\Http\Requests\Api\Tblevents\Index;
use App\Http\Requests\Api\Tblevents\Show;
use App\Http\Requests\Api\Tblevents\Create;
use App\Http\Requests\Api\Tblevents\Store;
use App\Http\Requests\Api\Tblevents\Edit;
use App\Http\Requests\Api\Tblevents\Update;
use App\Http\Requests\Api\Tblevents\Destroy;
use App\Http\Requests\Api\Tblevents\Bulkupdate;
use App\Http\Requests\Api\Tblevents\Publish;
use App\Http\Requests\Api\Tblevents\ChangeStatus;
use App\Http\Requests\Api\Tblevents\Tbleventdaterequest;
use App\Http\Requests\Api\Tblevents\Schedulerequest;
use App\Http\Requests\Api\Tblevents\DeletePeopleFromSchedule;
use App\Http\Requests\Api\Tblevents\Note;
use App\Http\Requests\Api\Tblevents\InvoiceRequest;
use App\Http\Requests\Api\Tblevents\QucikRequest;
use App\Repositories\EventRepository;
use App\Repositories\NotesRepository;
use App\Repositories\TbleventdateRepository;
use App\Repositories\TbleventsShifthourRepository;
use App\Repositories\ScheduleRepository;
use App\Repositories\EmployeeRepository;
use App\Repositories\WaitinglistRepository;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use App\Helpers\AppHelper;
use EventHelper;
use Auth;
use DB;


/**
 * Tblevent
 *
 * @Resource("Tblevent", uri="/events")
 */

class TbleventController extends ApiController
{   
    public function __construct(NotesRepository $notesRepository,
    TbleventdateRepository $tbleventdateRepository,WaitinglistRepository $waitinglistRepository){
      $this->notesRepository = $notesRepository;
      $this->tbleventdateRepository = $tbleventdateRepository;
      $this->waitinglistRepository = $waitinglistRepository;
    }
     
    public function index(Index $request, EventRepository $eventRepository)
    {
        $perPage = EventHelper::getPerPage($request);
        $where = EventHelper::eventFilterConditions($request->input());
        if(!empty($where)){
            $event_data = Tblevent::where($where);
        }else{
            $event_data = Tblevent::whereNull('deleted_at');
        }
        
        //Account Manager Id Filter
        if($request->has('account_manager_id') && !empty($request->account_manager_id)){
            $serachs = $request->account_manager_id;
            
            $account_ids = User::where('fullname', 'like', '%' . $serachs . '%')->where('role_id',3)->get()->pluck('id')->toArray();
            $event_data->whereIn('account_manager', $account_ids);
        }
        //Date Filter here
        if($request->has('date_to') && $request->has('date_from') && !empty($request->date_to) && !empty($request->date_from)){
            $date_to = $request->date_to;
            $date_from = $request->date_from;
            $data = $event_data->whereHas('date',function($date_q) use($date_from,$date_to) {
                $date_q->whereBetween('Eventdate',[$date_from,$date_to]);
            })->orderBy('created_at', 'desc')->paginate($perPage);
        }else{
            $data = $event_data->orderBy('created_at', 'desc')->paginate($perPage);
        }
        return $this->response->paginator($data, new TbleventTransformer());
    }


    public function show(Show $request, $tblevent)
    { 
        $Tblevent = Tblevent::where('EID', $tblevent)->first();
        if( $Tblevent){
            return $this->response->item($Tblevent, new TbleventTransformer());
        }
        return $this->response->errorNotFound('Event Not Found', 404);
    }


    public function store(Store $request, EventRepository $eventRepository)
    {
        if ($event = $eventRepository->create($request->all())) {
            return $this->response->item($event, new TbleventTransformer());
        } else {
            return response()->json(['error' => 'Error occurred while saving Event.'], 422);
        }
    }

 
    public function update(Update $request, $tblevent, EventRepository $eventRepository)
    {
        $requested_data = $request->all();
        $requested_data['EventUpdatedBy'] = Auth::user()->id;
        if ($event =  $eventRepository->updateById( $tblevent, $requested_data ) ) {
            return $this->response->item($event, new TbleventTransformer());
        } else {
            return response()->json(['error' => 'Error occurred while saving Event.'], 422);
        }
    }


    public function destroy(Destroy $request, $tblevent, EventRepository $eventRepository)
    {
        $publish_date = Tbleventdate::where('EID',$tblevent)->where('is_publish',1)->first();
        $shift = TbleventsShifthour::where('EID',$tblevent)->whereIn('Confirmed',[1,2])->first();
        if(!$shift){
            if(!$publish_date){
                if ( $eventRepository->deleteById($tblevent) ) {
                    return $this->response->array(['status' => 200, 'message' => 'Event successfully deleted']);
                } else {
                    return response()->json(['error' => 'Error occurred while deleting Event.'], 422);
                }
            }
            return response()->json(['error' => 'Event date published.'], 422);
        }
        return response()->json(['error' => 'Please delete employee shift first.'], 422);
    }


    public function getNotes(Request $request, $event_id){
        $notes =  $this->notesRepository->getNotesByType('EVENT', $event_id);
        return response()->json(['data'=>$notes]);
    }


    public function postNote(Note $request, $event_id){
        if(!empty($request->all())) {
            $request->ParentID = $event_id;
            $notes_data['Note'] = $request->Note;
            $notes_data['ParentCode'] = 'EVENT';
            $notes_data['ParentID'] =  $event_id;
            $notes_data['AddedBy'] =  Auth::user()->id;

            $note =  $this->notesRepository->create($notes_data);
            if ($note) {
                    return response()->json(['data' => 'Note has been added successfully.'], 200);
            }
            return response()->json(['error' => 'Note has been added already for this Sale.'], 422);
        }
    }


    /***Get All Events Date With Pagination ***/
    public function getDate(Request $request, $event_id){
        $perPage = EventHelper::getPerPage($request);
        $dates =  $this->tbleventdateRepository->where('EID',$event_id,'=')->orderBy('SortOrder', 'ASC')->paginate($perPage);
        return $this->response->paginator($dates, new TbleventdateTransformer());
    }


    /***Create New Events Date ***/
    public function postDate(Tbleventdaterequest $request, $event_id){
        $dates_data = $request->all();
        $dates_data['EID'] =  $event_id;
        $checkShortOrder = true;
        if(!$request->has('SortOrder')){
            $dates_data['SortOrder'] = 1;
            $checkShortOrder = false;
            $query_result = Tbleventdate::where('EID',$event_id)->orderBy('SortOrder','desc')->limit(1)->get()->toArray();
            if(!empty($query_result)){
                $dates_data['SortOrder'] = $query_result[0]['SortOrder'] + 1;
            }
            $request->SortOrder = $dates_data['SortOrder'];
        }
        
        if(HolidayList::where('StatDate',$request->Eventdate)->first()){
            $dates_data['StatHoliday'] = 1;
        }
        
        $date_id =  $this->tbleventdateRepository->create($dates_data);       
        if ($date_id) {
            if($checkShortOrder){
                EventHelper::setSortOrderr($request->SortOrder,$event_id,$date_id);
            }
            return response()->json(['data' => 'Event date has been added successfully.'], 200);
        }
        return response()->json(['error' => 'Event date has been added already for this Sale.'], 422);
       
    }


    /***Update Events Date ***/
    public function updateDate(Tbleventdaterequest $request, $event_id, $date_id){
        $dates_data = $request->all();
        $dates_data['EID'] =  $event_id;        
        $Tbleventdate = Tbleventdate::where('DID', '=', $date_id)->where('EID', '=', $event_id)->first();
        if($Tbleventdate){
            $event_date =  $this->tbleventdateRepository->updateById($date_id, $dates_data);
            if ($event_date) {
                if($request->has('SortOrder') && !empty($request->SortOrder)){
                    EventHelper::setSortOrderr($request->SortOrder,$event_id,$date_id);
                }
                return response()->json(['data' => 'Event date has been updated successfully.'], 200);
            }
            return response()->json(['error' => 'Event date not updated, Please try again.'], 422);
        }
        return response()->json(['error' => 'Event date not found.'], 422);
    }

    
    public function deleteDate(Request $request, $event_id, $date_id, TbleventdateRepository $tbleventdateRepository)
    {
        $shift = TbleventsShifthour::where('DID',$date_id)->first();
        if($shift && $request->has('force_delete') && $request->force_delete == 0){
            return response()->json(['error' => 'Some shift perpared, Are you sure? You want to delete this'], 422);
        }else{
            if ( $tbleventdateRepository->deleteById($date_id) ) {
                TbleventsShifthour::where('DID',$date_id)->update(['deleted_at' => date('Y-m-d H:i:s')]);
                return $this->response->array(['status' => 200, 'message' => 'Event date successfully deleted']);
            } else {
                return response()->json(['error' => 'Error occurred while deleting Event date.'], 422);
            }
        }
    }


    /***Create New Schedule for Event Date ***/
    public function postSchedule(Schedulerequest $request, $event_id,ScheduleRepository $scheduleRepository){
        $schedule_data = $request->all();
        $response = EventHelper::compareTimeValidation($schedule_data);
        if($response['status'] == 200){
            //Add Schedule here
            foreach($schedule_data['data'] as $key=>$schedule){
                foreach($schedule['schedule'] as $schedule_value){
                    #If schedule already exit and update here
                    if(isset($schedule_value['id']) && !empty($schedule_value['id'])){
                        #Check if Quantity less then exists records.
                        $schedules = $scheduleRepository->getById($schedule_value['id']);
                        if($schedule_value['quantity'] < $schedules->quantity){
                            #Check shifts if people not added then delete shifts else send error message.
                            $quantity_descrese = $schedules->quantity - $schedule_value['quantity'];
                            if(!$this->checkShift($schedule_value['id'], $quantity_descrese, $schedule_value['quantity'], $schedules->quantity)){
                                return response()->json(['error' => 'In shifts employees are added, Please remove employees from shift first.'], 422);
                            }
                        }
                        #Update Schedule here
                        $schedule_value['event_id'] =  $event_id;
                        $schedule_value['date_id'] =  $schedule['DID'];
                        $schedule_result =  $scheduleRepository->updateById( $schedule_value['id'], $schedule_value );
                        $shift_count = TbleventsShifthour::where('schedule_id',$schedule_value['id'])->count();
                        TbleventsShifthour::where('schedule_id',$schedule_value['id'])->update(['quantity'=> $schedule_value['quantity'], 'PID' => $schedule_value['position_id'], 'Department' => $schedule_value['department_id']]);
                        #If schedule quantity increase
                        if($schedule_value['quantity'] > $shift_count){
                            $add_shift =  $schedule_value['quantity'] - $shift_count;
                            for($i=0; $i< $add_shift; $i++){
                                $formated_data = $this->scheduleDataFormated($schedule_value, $schedule, $schedule_value['id']);
                                TbleventsShifthour::create($formated_data);
                            }
                        }
                    }else{
                        $schedule_value['event_id'] =  $event_id;
                        $schedule_value['date_id'] =  $schedule['DID'];
                        $schedule_result =  $scheduleRepository->create($schedule_value);
                        for($i=0; $i< $schedule_value['quantity']; $i++){
                            $formated_data = $this->scheduleDataFormated($schedule_value, $schedule, $schedule_result->id);
                            TbleventsShifthour::create($formated_data);
                        }
                    }
                }
            }
            return response()->json(['data' => 'Event schedule has been added successfully.'], 200);
        }else{
            return response()->json(['error' => $response['message']], 422);
        }
    }

    private function checkShift($schedule_id, $quantity_descrese, $post_quantity, $exist_quantity){
        
        $result = true;
        $shift_people_count = TbleventsShifthour::where('schedule_id',$schedule_id)->whereNotNull('PeopleID')->count();
        if($shift_people_count > 0){
            $pending_quantity = $exist_quantity - $shift_people_count;
            if($pending_quantity < $quantity_descrese){
                $result = false;
            }else{
                for($i=0; $i<$quantity_descrese; $i++){
                    $shift = TbleventsShifthour::whereNull('PeopleID')->where('schedule_id', $schedule_id)->first();
                    if($shift){
                        TbleventsShifthour::where('ID', $shift->ID)->update(['deleted_at' => date('Y-m-d H:i:s')]);
                    }
                }
                $result = true;
            }
        }else{
            for($i=0; $i< $quantity_descrese; $i++){
                $shift = TbleventsShifthour::whereNull('PeopleID')->where('schedule_id', $schedule_id)->first();
                if($shift){
                    TbleventsShifthour::where('ID', $shift->ID)->update(['deleted_at' => date('Y-m-d H:i:s')]);
                }
            }
            $result = true;
        }
        return $result; 
    }

    /***Formated Schedule data for Create Shifts ***/
    private function scheduleDataFormated($schedule_value, $schedule_data, $id){
        $people_shifts['PeopleID'] = null;
        $people_shifts['EID'] = $schedule_data['EID'];
        $people_shifts['PID'] = $schedule_value['position_id'];
        $people_shifts['DID'] = $schedule_data['DID'];
        $people_shifts['Pre'] = 0;
        $people_shifts['Confirmed'] = 0;
        $people_shifts['schedule_id'] = $id;
        $people_shifts['region_id'] = Auth::user()->region_id;
        $people_shifts['Department'] = isset($schedule_value['department_id'])? $schedule_value['department_id']: null;
        $people_shifts['Quantity'] = $schedule_value['quantity'];
        $people_shifts['Start1'] = ($schedule_value['start_one']) ?  date('H:i:s', strtotime($schedule_value['start_one'])) : null;
        $people_shifts['Finish1'] = ($schedule_value['finish_one']) ? date('H:i:s', strtotime($schedule_value['finish_one'])) : null;
        $people_shifts['Start2'] = ($schedule_value['start_two']) ? date('H:i:s', strtotime($schedule_value['start_two'])) : null;
        $people_shifts['Finish2'] = ($schedule_value['finish_two']) ? date('H:i:s', strtotime($schedule_value['finish_two'])) : null;
        $people_shifts['Start3'] = ($schedule_value['start_three']) ? date('H:i:s', strtotime($schedule_value['start_three'])) : null;
        $people_shifts['Finish3'] = ($schedule_value['finish_three']) ? date('H:i:s', strtotime($schedule_value['finish_three'])) : null;
        $people_shifts['created_at'] = date('Y-m-d H:i:s');
        $people_shifts['updated_at'] = date('Y-m-d H:i:s');
        return $people_shifts;
    }


    /***Get All Events Schedule With Pagination for Bulk update ***/
    public function getSchedule(Request $request, $event_id,ScheduleRepository $scheduleRepository){
        $perPage = EventHelper::getPerPage($request);
        if($request->has('date_id')){
            $date_id = $request->date_id;
            $schedule_data =  $scheduleRepository->where('event_id',$event_id,'=')
                                                ->where('date_id',$date_id,'=')
                                                ->paginate($perPage);
            return $this->response->paginator($schedule_data, new ScheduleTransformer());
        }
        return response()->json(['error' => 'Please send date id.'], 422);
    }

    
    /***Get All Events Schedule***/
    public function getAllSchedule(Request $request, $event_id){
        if($request->has('date_ids')){
            $date_ids =  explode(",",$request->date_ids);
            $schedule_data =  Tbleventdate::where('EID',$event_id)
                                            ->whereIn('DID',$date_ids)
                                            ->with('schedule')
                                            ->select('DID','EID','Eventdate')
                                            ->get()->toArray();
            return $this->response->array(['data'=> $schedule_data ,'status' => 200]);
        }
        return response()->json(['error' => 'Please send date id.'], 422);
    }


    /***Delete Schedule***/
    public function destroySchedule(Request $request, $tblevent, $schedule, ScheduleRepository $scheduleRepository)
    {   
        $schedule_data = $scheduleRepository->where('event_id', $tblevent)->where('id',$schedule);
        if(!empty($schedule_data)){
            if ( $scheduleRepository->deleteById($schedule) ) {
                EventHelper::deleteShifAndNotification($schedule, $tblevent);
                return $this->response->array(['status' => 200, 'message' => 'Schedule successfully deleted']);
            } else {
                return response()->json(['error' => 'Error occurred while deleting schedule.'], 422);
            }
        }
        return response()->json(['error' => 'Invalid schedule and event, Please try again'], 422);
    }


    /***Before delete schedule check is schedule perpared or not***/
    public function checkScheduleForDestroy(Request $request, $tblevent, $schedule, ScheduleRepository $scheduleRepository)
    {   
        $schedule_data = $scheduleRepository->where('event_id', $tblevent)->where('id',$schedule)->first();
        if($schedule_data){
            $response = EventHelper::checkScheduleIsDelete($schedule, $tblevent);
            return $this->response->array($response);
        }
        return response()->json(['error' => 'Invalid schedule and event, Please try again.'], 422);
    }


    /***Get Employees With Search and Filter With Pagination ***/
    public function getEmployeeForSchedule(Request $request,$event_id, EmployeeRepository $employeeRepository){
        
        if($request->has('date_ids') ){
            /****Pagination****/
            $perPage = EventHelper::getPerPage($request);
            
            /****Filter and Query****/
            $date_ids =  explode(",",$request->date_ids);
            
            $schedule_ids = array();
            if($request->has('schedule_ids')){
                $schedule_ids =  explode(",",$request->schedule_ids);
            }
            
            $response = EventHelper::employeeSearchFilter($request, $date_ids, $schedule_ids, $event_id);
            if(!empty($response['people'])){
                $people = $response['people'];
                /****Send Response here****/
                $data = $people->orderBy('created_at', 'desc')->paginate($perPage);
                return $this->response->paginator($data, new EmployeeFilterTransformer($response['date'],$date_ids));
            }
            return response()->json(['error' => 'Invalid date, Please try agian.'], 422);
        }        
        return response()->json(['error' => 'Please send date id.'], 422);
    }


    /***Get All Position selected in Schedule ***/
    public function getDatePosition(Request $request,$event_id,ScheduleRepository $scheduleRepository){
        $dates = array();
        
        if($request->has('dates')){
            /****Query****/
            $dates =  explode(",",$request->dates);
            $schedule_data =  EventSchedule::whereIn('date_id',$dates)
                                            ->with(['date','position']);
            $schedule_data->whereHas('position'); 
            $scheduledata = $schedule_data->whereHas('date')->get()->toArray();                             
            if(!empty($scheduledata)){
                $position = array();

                foreach($scheduledata as $key=>$schedule){
                    if(in_array($schedule['position_id'], array_column($position, 'position_id'))){
                        $search_date_key = array_search($schedule['position_id'], array_column($position, 'position_id'));
                        
                        $date_already_key = count($position[$search_date_key]['date']);
                        $position[$search_date_key]['date'][$date_already_key]['schedule_id'] = $schedule['id'];
                        $position[$search_date_key]['date'][$date_already_key]['date_id'] = $schedule['date_id'];
                        $position[$search_date_key]['date'][$date_already_key]['date'] = $schedule['date']['Eventdate'];
                        $position[$search_date_key]['date'][$date_already_key]['is_publish'] = $schedule['date']['is_publish'];
                        $position[$search_date_key]['date'][$date_already_key]['quantity'] = $schedule['quantity'];
                        $position[$search_date_key]['date'][$date_already_key]['remaining'] = $this->getRemaningCount($schedule);
                        $position[$search_date_key]['date'][$date_already_key]['start_one'] = $schedule['start_one'];
                        $position[$search_date_key]['date'][$date_already_key]['finish_one'] = $schedule['finish_one'];
                        $position[$search_date_key]['date'][$date_already_key]['start_two'] = $schedule['start_two'];
                        $position[$search_date_key]['date'][$date_already_key]['finish_two'] = $schedule['finish_two'];
                        $position[$search_date_key]['date'][$date_already_key]['start_three'] = $schedule['start_three'];
                        $position[$search_date_key]['date'][$date_already_key]['finish_three'] = $schedule['finish_three'];
                    }else{
                        $date_key = count($position);
                        $position[$date_key]['position_id'] = $schedule['position_id'];
                        $position[$date_key]['Position'] = $schedule['position']['Position'];
                        $position[$date_key]['date'][0]['date_id'] = $schedule['date_id'];
                        $position[$date_key]['date'][0]['schedule_id'] = $schedule['id'];
                        $position[$date_key]['date'][0]['date'] = $schedule['date']['Eventdate'];
                        $position[$date_key]['date'][0]['is_publish'] = $schedule['date']['is_publish'];
                        $position[$date_key]['date'][0]['quantity'] = $schedule['quantity'];
                        $position[$date_key]['date'][0]['remaining'] = $this->getRemaningCount($schedule);
                        $position[$date_key]['date'][0]['start_one'] = $schedule['start_one'];
                        $position[$date_key]['date'][0]['finish_one'] = $schedule['finish_one'];
                        $position[$date_key]['date'][0]['start_two'] = $schedule['start_two'];
                        $position[$date_key]['date'][0]['finish_two'] = $schedule['finish_two'];
                        $position[$date_key]['date'][0]['start_three'] = $schedule['start_three'];
                        $position[$date_key]['date'][0]['finish_three'] = $schedule['finish_three'];
                    }
                }
                return $this->response->array(['data'=> $position ,'status' => 200]);
            }
            return response()->json(['error' => 'Please prepare schedule first.'], 422);
        }
        return response()->json(['error' => 'Please send date id.'], 422);
        
    }

    private function getRemaningCount($schedule){
        $remaining_count = TbleventsShifthour::where('EID',$schedule['event_id'])
											->where('DID',$schedule['date_id'])
											->where('PID',$schedule['position_id'])
											->where('Confirmed',1)->count();

		$remaining = $schedule['quantity'];	
		if($remaining_count){
			$remaining = $schedule['quantity'] - $remaining_count;
        }
        return $remaining;
    }

    /***Pulish date and send messages ***/
    public function datePublished(Publish $request, $tblevent){
        
        if(EventSchedule::where('event_id',$tblevent)->first()){


            $autoconfirm = Tblevent::where('EID', $tblevent)->where('autoconfirm',1)->first(); 

            /**** Get shifts form shift log tables if Admin change andy status ****/
            $users_data =  EventsShiftLog::with(
                            ['people'=>function($query){
                                $query->select('PeopleID','Email','FirstName','email_notification','Cell','WorkExt','sms_notification');
                            },'event'=>function($query){
                                $query->select('EID','EventName','VID');
                            },'event.location'=>function($query){
                                $query->select('VID','VAddressLine1','VCity','VCountry');
                            },'date'=>function($query){
                                $query->select('DID','Eventdate');
                            },'position'])
                            ->where('is_published',0)
                            ->whereIn('DID',$request->DID)
                            ->get()->toArray();

            /**** If Log not saved then get Assigned user to confirmed ****/                
            if(empty($users_data) && $autoconfirm){
                $users_data =  TbleventsShifthour::with(
                    ['people'=>function($query){
                        $query->select('PeopleID','Email','FirstName','email_notification','Cell','WorkExt','sms_notification');
                    },'event'=>function($query){
                        $query->select('EID','EventName','VID');
                    },'event.location'=>function($query){
                        $query->select('VID','VAddressLine1','VCity','VCountry');
                    },'date'=>function($query){
                        $query->select('DID','Eventdate');
                    },'position'])
                    ->where('Confirmed',2)
                    ->whereIn('DID',$request->DID)
                    ->get()->toArray();
                    //Update Assigned to confirmed user
                    
            }

            if($autoconfirm){
                TbleventsShifthour::whereIn('DID',$request->DID)->where('EID', $tblevent)->where('Confirmed',2)->update(['Confirmed' => 1]);
            }    

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
                /****Publish Date****/
                $date_obj = Tbleventdate::whereIn('DID',  $request->DID)->where('EID', '=', $tblevent)->update(['is_publish'=>1]);
                if(!$date_obj){
                    return response()->json(['error' => 'Event Date not match with this event, Please try again.'], 422);
                }
                Tblevent::where('EID', $tblevent)->update(['status'=>1]);
            }
            
            return response()->json(['data' => 'Date has been published successfully.'], 200);
        }else{
            return response()->json(['error' => 'No shifts found to publish.'], 422);
        }
    }

    /***Get All Position selected in Schedule ***/
    public function invitePublish(Publish $request, $tblevent, TbleventsShifthourRepository $tbleventsShifthourRepository){
        
        $date_publish = Tbleventdate::whereIn('DID',  $request->DID)->where('EID', '=', $tblevent)->where('is_publish',1)->get()->toArray();
        if(!empty($date_publish )){
            return response()->json(['error' => 'Selected date is already publish.'], 422);
        }

        #Send Bulk Message Email
        $this->sendBulkMessage($request->DID, $tblevent);

        #Send Bulk Message SMS
        $this->sendBulkMessageSMS($request->DID, $tblevent);

       
        /**** Validate is email exist in tbl people or not****/
        $users_data =  TblpeopleNotification::with(
                        ['people'=>function($query){
                            $query->select('PeopleID','Email','FirstName','email_notification','Cell','WorkExt','sms_notification');
                        },'event'=>function($query){
                            $query->select('EID','EventName','VID');
                        },'event.location'=>function($query){
                            $query->select('VID','VAddressLine1','VCity','VCountry');
                        },'date'=>function($query){
                            $query->select('DID','Eventdate');
                        },'shift','shift.position'])
                        ->where('status',0)
                        ->where('type','invite')
                        ->whereIn('DID',$request->DID)
                        ->get()->toArray();
        
        //Set Queue Dispatch here                
        if(!empty($users_data)){
            foreach($users_data as $userData){
                if(isset($userData['people']['Email']) && !empty($userData['people']['Email']) && $userData['people']['email_notification'] == '1'){
                    $this->dispatch(new SendInvite($userData));
                }
                if(isset($userData['people']['Cell']) && $userData['people']['Cell'] !== null && isset($userData['people']['WorkExt']) && $userData['people']['WorkExt'] !== null && $userData['people']['sms_notification'] == '1'){
                    $this->dispatch(new SendInviteSMS($userData));
                }
            }
        }else{
            return response()->json(['error' => 'Please select employees for send invite to join this event.'], 422);
        }
        /****Publish Date****/
        $date_obj = Tbleventdate::whereIn('DID',  $request->DID)->where('EID', '=', $tblevent)->update(['is_publish'=>1, 'send_sms' => $send_sms]);
        if(!$date_obj){
            return response()->json(['error' => 'Event Date not match with this event, Please try again.'], 422);
        }
        Tblevent::where('EID', $tblevent)->update(['status'=>1]);
        return response()->json(['data' => 'Invitiation sent successfully.'], 200);
    }
    

    /**
     * sendBulkMessage
     *
     * @param  mixed $DID
     * @param  mixed $EID
     *
     * @return void
     */
    private function sendBulkMessage($DID, $EID){
        $notification_data = TblpeopleNotification::where('DID', $DID)
                                    ->where('EID',$EID)
                                    ->where('type','bulk_email')
                                    ->where('status',0)
                                    ->with([
                                        'shift',
                                        'people'=>function($query){
                                            $query->select('PeopleID','Email','FirstName','email_notification');
                                        },'event'=>function($query){
                                            $query->select('EID','EventName');
                                        },'date'=>function($query){
                                            $query->select('DID','Eventdate');
                                        },'position'=>function($query){
                                            $query->select('PID','Position');
                                        }
                                    ])->get()->toArray();
        //Set Queue Dispatch here                
        if(!empty($notification_data)){
            foreach($notification_data as $notification){
                if(isset($notification['people']['Email']) && !empty($notification['people']['Email']) && $notification['people']['email_notification'] == '1'){
                    $this->dispatch(new sendBulkMessageOnPublishedDate($notification));
                }
            }
        }
    }

    /**
     * sendBulkMessageSMS
     *
     * @param  mixed $DID
     * @param  mixed $EID
     *
     * @return void
     */
    private function sendBulkMessageSMS($DID, $EID){
        $notification_data = TblpeopleNotification::where('DID', $DID)
                                    ->where('EID',$EID)
                                    ->where('type','bulk_sms')
                                    ->where('status',0)
                                    ->with(['people'=>function($query){
                                        $query->select('PeopleID','Cell','WorkExt','FirstName'.'sms_notification');
                                    }])->get()->toArray();
        //Set Queue Dispatch here                
        if(!empty($notification_data)){
            foreach($notification_data as $notification){
                if(isset($notification['people']['Cell']) && $notification['people']['Cell'] !== null && isset($notification['people']['WorkExt']) && $notification['people']['WorkExt'] !== null && $notification['people']['sms_notification'] == '1'){
                    $this->dispatch(new sendBulkSMSOnPublishedDate($notification));
                }
            }
        }
    }

    public function showEventDetail($shift_id){
        /***Decode shift_id***/
        $shift_id = EventHelper::base62decode($shift_id); 
        $shift_data = array();
        $shift_detail = TbleventsShifthour::where('ID',$shift_id)
                                        ->with([
                                            'event'=>function($query){
                                                $query->select('EID','EventName','VID');
                                            },'event.location'=>function($query){
                                                $query->select('VID','VAddressLine1','VCity','VCountry');
                                            },'date'=>function($query){
                                                $query->select('DID','Eventdate');
                                            },'position'
                                        ])->first();
        if($shift_detail){
            $shift_data = $shift_detail->toArray();
            $shift_data['event']['location'] = isset($shift_detail['event']['location']) ? $shift_detail['event']['location']['VAddressLine1'].' '.$shift_detail['event']['location']['VCity'].' '.$shift_detail['event']['location']['VCountry']: '' ;
           
            $start_time = '';
            if(!empty($shift_detail['Start3'])){
                $start_time = $shift_detail['Start3'];
            }
            if(!empty($shift_detail['Start2'])){
                $start_time = $shift_detail['Start2'];
            }
            if(!empty($shift_detail['Start1'])){
                $start_time = $shift_detail['Start1'];
            }
            $shift_data['start_time'] = $start_time;

            $end_time = '';
            if(!empty($shift_detail['Finish1'])){
                $end_time = $shift_detail['Finish1'];
            }
            if(!empty($shift_detail['Finish2'])){
                $end_time = $shift_detail['Finish2'];
            }
            if(!empty($shift_detail['Finish3'])){
                $end_time = $shift_detail['Finish3'];
            }
            $shift_data['end_time'] = $end_time;
            $shift_data['encripted_id'] = EventHelper::base62encode($shift_detail['ID']);
            
            
        }
        return view('layouts.shift_detail')->with(compact('shift_data'));
    }


    /***Change Invited user status ***/
    public function inviteStatusChange(ChangeStatus $request){
        /***Decode shift_id***/
        $shift_id = EventHelper::base62decode($request->shift_id);
        
        /***Invitation Accept Reject Conditions***/
        $response = EventHelper::inviteAcceptanceRejectedValidation($shift_id,$request->status);
                
        if($response['status'] == 200){
            return $this->response->array(['message'=> $response['message'] ,'status' => $response['status'], 'data'=>$response['data']]);
        }else{
            return response()->json($response[0]);
        }
    }

    /***Get Waiting List With Pagination ***/
    public function getWaitingList(Request $request, $event_id, WaitinglistRepository $waitinglistRepository){
        $perPage = EventHelper::getPerPage($request);
        if($request->has('date_id')){
            $date_id = $request->date_id;
            $waiting_list =  $waitinglistRepository->where('EID',$event_id,'=')->where('DID',$date_id,'=')->paginate($perPage);
            return $this->response->paginator($waiting_list, new WaitingListTransformer());
        }
        return response()->json(['error' => 'Date id is required.'], 422);
        
    }


    /***Bulk Schedule Update***/
    public function bulkScheduleUpdate(Bulkupdate $request, $event_id, ScheduleRepository $scheduleRepository){
        
        /***Get event date result***/
        $date_result = Tbleventdate::where('DID', $request->DID)->where('EID', $request->EID)->first();
        if($date_result){

            /***Update Schedule time here and return PID in array***/
            $PID = EventHelper::updateSchedule($request->schedule);
            if(!empty($PID)){
                
                /***Check Data is publish or not***/
                if($date_result->is_publish == 1){
                    /***Update shift***/
                    EventHelper::updateShiftTime($request,$PID);

                    /***Soft Delete Old Notification for same DID EID and PID***/
                    EventHelper::deleteOldNotification($request->EID, $request->DID, $PID);

                    /***Insert New notification for same DID EID and PID***/
                    $this->addNotification($request->EID, $request->DID, $PID);
                    

                    /***Send Invitation for new schedule In Queue***/
                    $this->sendNotification($request->EID, $request->DID, $PID);
                    

                    return response()->json(['data' => 'Schedule updated sccessfully, And Invitation email sent.'], 200);

                }else{
                    /***Update shift***/
                    EventHelper::updateShiftTime($request,$PID);
                    return response()->json(['data' => 'Schedule updated sccessfully.'], 200);
                }
            }
            return response()->json(['error' => 'Please update schedule first.'], 422);
        }
        return response()->json(['error' => 'Event and Date not match, Please try again.'], 422);
       
    }


    private function addNotification($EID, $DID, $PID){
        $shift_data = TbleventsShifthour::where('EID',$EID)->where('DID',$DID)->whereIn('PID',$PID)->get()->toArray();
                                
        if($shift_data){
           
            foreach($shift_data as $shift){
                $email_template = OfferEmailTemplate::where('EID', $shift['EID'])->where('status',1)->where('DID', $shift['DID'])->first();
                if($email_template){
                    $email_template = $email_template->toArray();
                }else{
                    $email_template = OfferEmailTemplate::where('EID', 0)->where('status',1)->where('DID', 0)->first()->toArray();
                }
                $notifcation_already_exist = TblpeopleNotification::where('PeopleID',$shift['PeopleID'])->where('EID',$EID)->where('DID',$shift['DID'])->where('shift_id',$shift['ID'])->first();
                if(!$notifcation_already_exist){
                    $this->dispatch(new SaveNotification($shift,$email_template,Auth::user()->region_id,Auth::user()->id));
                }
            }
        }
    }


    private function sendNotification($EID, $DID, $PID){
        $users_data =  TblpeopleNotification::with([
                            'people'=>function($query){
                                $query->select('PeopleID','Email','FirstName','email_notification');
                            },'event'=>function($query){
                                $query->select('EID','EventName');
                            },'date'=>function($query){
                                $query->select('DID','Eventdate');
                            }
                        ])
                        ->where('status',0)
                        ->where('DID',$DID)
                        ->where('EID',$EID)
                        ->where('PID',$PID)
                        ->get()
                        ->toArray();

        //Set Queue Dispatch here                
        if(!empty($users_data)){
            foreach($users_data as $userData){
                if(isset($userData['people']['Email']) && !empty($userData['people']['Email']) && $userData['people']['email_notification'] == '1'){
                    $this->dispatch(new SendInvite($userData));
                }
            }
        }
    }


    /***Get People Do Not Call***/
    public function getDoNotCallPeople(Request $request, $event_id){
        $perPage = EventHelper::getPerPage($request);
        if($request->has('date_ids')){
            $date_ids =  explode(",",$request->date_ids);
            $shift_query =  TbleventsShifthour::where('EID',$event_id)->whereIn('DID',$date_ids);
        }else{
            $shift_query =  TbleventsShifthour::where('EID',$event_id);
        }
        $shift_query->whereHas('people',function ($query) {
            return $query->select('PeopleID', 'FirstName', 'LastName', 'City', 'Postal', 'do_not_call')->where('do_not_call',  '1');
        });
        $shift_query->groupBy('PeopleID');

        $donotcall = $shift_query->paginate($perPage);
        return $this->response->paginator($donotcall, new TbleventsShifthourTransformer());
    }

    
    /***Get Shift Summary***/
    public function getShiftSummary(Request $request, $event_id, ScheduleRepository $scheduleRepository){
        $perPage = EventHelper::getPerPage($request);

        /***Check if select any date then get result according date ids else get first date_id result***/
        if($request->has('date_ids')){
            $date_ids =  explode(",",$request->date_ids);
            $dateids =   Tbleventdate::CheckDateIds($date_ids)->orderBy('SortOrder', 'asc')->pluck('DID')->toArray();
            $sehedule_data =  EventSchedule::whereIn('date_id',$date_ids)->where('event_id',$event_id,'=')->orderByRaw("FIELD(date_id, $request->date_ids)")->paginate($perPage);
        } 
        else {
            $first_date =  Tbleventdate::where('EID',$event_id)->orderBy('SortOrder', 'asc')->pluck('DID')->first();
            $sehedule_data =  $scheduleRepository
                ->where('event_id', $event_id)
                ->where('date_id', $first_date)
                ->paginate($perPage);    
        }
        return $this->response->paginator($sehedule_data, new ShiftSummaryTransformer());
    }


    /***Get Time Sheet***/
    public function getTimeSheet(Request $request, $event_id){

        /***Check if select any date then get result according date ids else get all date_id result***/
        if($request->has('date_ids')){
            $date_ids =  explode(",",$request->date_ids);
        }else{
            $date_ids =  Tbleventdate::where('EID',$event_id)->pluck("DID");
        }
        $query_response = EventHelper::timeSheetQuery($event_id, $date_ids);
        return $this->response->array(['status' => 200, 'data' => $query_response]);
    }


    /***Get No Show List - List all rejected/cancelled***/
    public function getNoShowList(Request $request, $event_id, TbleventsShifthourRepository $tbleventsShifthourRepository){
        /***Get Per Page here***/
        $perPage = EventHelper::getPerPage($request);
        
        /***Check if select any date then get result according date ids else get all date_id result***/
        $date_ids = [];
        if($request->has('date_ids')){
            $date_ids_get =  explode(",",$request->date_ids);
            $date_ids =  Tbleventdate::where('EID',$event_id)->whereIn('DID',$date_ids_get)->pluck("DID");
        }else{
            $date_ids =  Tbleventdate::where('EID',$event_id)->get()->pluck("DID")->toArray();
        }
        $query_response = $tbleventsShifthourRepository->where('Confirmed',2)->where('EID',$event_id)
                                            ->whereIn('DID',$date_ids)->paginate($perPage);
        return $this->response->paginator($query_response, new TbleventsShifthourTransformer());
    }


    /***Get Not Available People***/
    public function getNotAvailablePeople(Request $request, $event_id){

        /***Get Per Page here***/
        $perPage = EventHelper::getPerPage($request);
        
        /***Check if select any date then get result according date ids else get all date_id result***/
        if($request->has('date_ids')){
            $date_ids_get =  explode(",",$request->date_ids);
            $date_ids =  Tbleventdate::where('EID',$event_id)->whereIn('DID',$date_ids_get)->pluck("DID");
        }else{
            $date_ids =  Tbleventdate::where('EID',$event_id)->pluck("DID");
        }
        if(!empty($date_ids)){
            $query_response = EventHelper::notAvailablePeopleQuery($date_ids);
            $data = $query_response->paginate($perPage);
            return $this->response->paginator($data, new EmployeeShortResponseTransformer());
        }
        return response()->json(['error' => 'Event date not found, Please create date first.'], 422);
    }


    /***Delete peoples from schedule***/
    public function deletePeople(DeletePeopleFromSchedule $request){
        #Check if user confirmed for this schedule send delete notification by Email
        $this->sendDeletedPeopleConfirmation($request->shift);
        #Soft Delete shift here
        TbleventsShifthour::whereIn('ID',$request->shift)->update(['deleted_at'=> date('Y-m-d H:i:s')]);
        #Soft Delete shift Notification here
        TblpeopleNotification::whereIn('shift_id',$request->shift)->update(['deleted_at'=> date('Y-m-d H:i:s')]);
        return response()->json(['data' => 'People deleted successfully from schedule.'], 200);
    }
    

    private function sendDeletedPeopleConfirmation($shift_ids){
        #Get People IDs where confirmed 1 and send notification
        $people_data = TbleventsShifthour::whereIn('ID',$shift_ids)
                                        ->where('Confirmed',1)
                                        ->with(['people'=>function($query){
                                            $query->select('PeopleID','Email','FirstName','email_notification');
                                        },'event'=>function($query){
                                            $query->select('EID','EventName','VID');
                                        },'event.location'=>function($query){
                                            $query->select('VID','VAddressLine1','VCity','VCountry');
                                        },'date'=>function($query){
                                            $query->select('DID','Eventdate');
                                        },'position'])->get()->toArray();
        if(!empty($people_data)){
            foreach($people_data as $userData){
                if(isset($userData['people']['Email']) && !empty($userData['people']['Email']) && $userData['people']['email_notification'] == '1'){
                    $this->dispatch(new SendDeletedConfirmation($userData));
                }
            }
        }
    }

    
    public function createInvoice(InvoiceRequest $request, $event_id){
        $dates = $request->DID;
        
        //Estimate report
        $query_response = array();

        $shift_ids = array();
        if(!$request->invoiced){
            $shift_ids = $this->getShiftIds($event_id,$dates);
        }

        $last_date = Tbleventdate::select('Eventdate')->whereIn('DID',$dates)->orderBy('Eventdate', 'desc')->first();
        $start_date = Tbleventdate::select('Eventdate')->whereIn('DID',$dates)->orderBy('Eventdate', 'asc')->first();
        
        #Get all dates between selected date range
        $all_Dates = EventHelper::getDatesFromRange(date('Y-m-d',strtotime($start_date->Eventdate)), date('Y-m-d',strtotime($last_date->Eventdate))); 

        #dates make week wise
        $week_dates = EventHelper::weekWiseDays($all_Dates);
        
        //Invoice report       
        $event_query = Tblevent::select('EID','EventName','EventID','VID','CfgID','ClientId','WorkCategoryID','labor_surcharge','PONumber')
                                ->where('EID', $event_id)
                                ->with([
                                    'lineitem',
                                    'work',
                                    'client',
                                    'location'=>function($query){
                                        $query->select('VID','VAddressLine1','VCity','VCountry','VName');
                                    },
                                    'configration',
                                    'date'=>function($query) use($dates){
                                        $query->whereIn('DID',$dates)->orderBy('SortOrder', 'asc');
                                    },
                                    'date.shift'=>function($query)use($request,$shift_ids){
                                        if($request->invoiced){
                                            $query->where('Confirmed',1);
                                        }else{
                                            $query->whereIn('ID',$shift_ids);
                                        }
                                        $query->orderBy('PID', 'asc')->orderBy('Start1', 'asc')->orderBy('Finish1', 'asc')->orderBy('Start2', 'asc')->orderBy('Finish2', 'asc');
                                        
                                    },
                                    'date.shift.people'=>function($query){
                                        $query->select('PeopleID','FirstName');
                                    }
                                    ,'date.shift.position'
                                ])->first();
                                
        if(!empty($event_query)){
           // dd($event_query->toArray());
            $line_item = TbleventsInvoicelineitem::where('EID',$event_id)->get()->toArray();
            $settings = DataFileSettings::where('region_id', Auth::user()->region_id)->first();
            if($settings){
                $error_setting = Tblprogramsetting::first();
                if($error_setting){
                    $query_response = EventHelper::Invoice($event_query->toArray(), $settings, $request->schedule, $request->detail_summary, $error_setting, $line_item, $request->invoiced, $week_dates );
                }else{
                    return response()->json(['error' => 'Please set error setting first.'], 422);
                }
            }else{
                return response()->json(['error' => 'Please set region setting first.'], 422);
            }
        }        
        return response()->json(['data' => $query_response], 200);
    }

    private function getShiftIds($event_id,$dates){
        $shift_ids = array();
        
        $shifts = TbleventsShifthour::where('EID', $event_id)
                                    ->whereIn('DID',$dates)
                                    ->orderBy('schedule_id','desc')
                                    ->get()->toArray();
                                    
        if(!empty($shifts)){
            $data = array();
            $qty = 0;
            foreach($shifts as $key=>$shift){
                if(!empty($data)){
                    if(in_array($shift['schedule_id'], $data)){
                        if($qty != 0){
                            $data[] = $shift['schedule_id'];
                            $shift_ids[] = $shift['ID'];
                            $qty = $qty-1;
                        }
                    }else{
                        $data[] = $shift['schedule_id'];
                        $shift_ids[] = $shift['ID'];
                        $qty = $shift['Quantity']-1;
                    }  
                }else{
                    $data[] = $shift['schedule_id'];
                    $shift_ids[] = $shift['ID'];
                    $qty = $shift['Quantity']-1;
                }
            }
        }
        return $shift_ids;
    }


    /*
     * Get Events schedule
     * For Selected Employee
     * In Selected Date Range
     * With Filters:
     * Client if selected
     * Position if selected
     * Event if selected  
     */
    public function quickCrewMember(QucikRequest $request){
        /****Pagination****/
        $perPage = EventHelper::getPerPage($request);
        
        /****Filter and Query****/
        $response = EventHelper::searchEventWithFilter($request);
        if($response['status'] == 200){
            return $this->response->array(['status' => 200, 'data' => $response['data']]);
        }
        return response()->json(['error' => $response['message']], 422);
    }

}
