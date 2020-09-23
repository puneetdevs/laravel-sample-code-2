<?php

namespace App\Http\Controllers\Api;

use App\Helpers\AppHelper;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\TeamCalendarEventListRequest;
use App\Http\Requests\TeamCalendarEventRequest;
use App\Models\Tbleventdate;
use App\Models\People;
use App\Models\PeopleNotAvailabilities;
use App\Models\HolidayList;
use Carbon\Carbon;
use Davaxi\VCalendar;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Auth;

/**
 * calendar
 *
 * @Resource("calendar", uri="/calendar")
 */
class CalendarEventsController extends ApiController
{
    /*
    * Function: Get Events 
    * Functionality: Get events with Calendar dates
    * Method: GET
    * Response: Array of events
    */
    public function getCalendar(Request $request)
    {
        #Get Month events
        if($request->has('from_date') && $request->has('to_date') && $request->from_date != '' && $request->to_date != ''){
            $events_data = Tbleventdate::where('Eventdate','>=',$request->from_date)
                                        ->where('Eventdate','<=',$request->to_date);
        }
        #Get Single Date Event with timing
        if($request->has('single_date') && !empty($request->single_date)){
            $events_data = Tbleventdate::where('Eventdate',$request->single_date);
        }
        $events_data->with([
                'event'=>function($event_q){
                    $event_q->select('VID','EID','EventName');
                },'event.location'=> function($location_q){
                    $location_q->select('VID', 'VName',"VAddressLine1","VAddressLine2","VProv","VCity","VPostal","VCountry");
                },'event.note' => function($note_q){
                    $note_q->where('ParentCode', 'EVENT');
                }
            ])->select('DID','EID','Eventdate');
        $events = $events_data->whereHas('event')->get();
        $response = $this->formatResponseData($events->toArray());
        return $this->response->array(['status' => 200, 'data' => $response]);
    }

    #Format calandar data here
    private function formatResponseData($events_data){
        $result = array();
        if(!empty($events_data)){
            foreach($events_data as $key => $event_value){
                $result[$key]['id'] = $event_value['EID'];   
                $result[$key]['allDay'] = true;
                $result[$key]['title'] = '';
                $result[$key]['start'] = $event_value['Eventdate'];
                $result[$key]['end'] = $event_value['Eventdate'];
                $result[$key]['meta'] = [];

                if(!empty($event_value['event'])){
                    $result[$key]['title'] = $event_value['event']['EventName'];
                    $result[$key]['meta']['location'] = isset($event_value['event']['location'])? 
                                                        $event_value['event']['location']['VAddressLine1'].' '.
                                                        $event_value['event']['location']['VPostal'].' '.
                                                        $event_value['event']['location']['VCity'].' '.
                                                        $event_value['event']['location']['VCountry']:"";
                    $note_array = array();
                    if(isset($event_value['event']['note']) && !empty($event_value['event']['note'])){
                        foreach($event_value['event']['note'] as $sub_key => $note){
                            $note_array[$sub_key] = $note['Note'];
                        }
                    }
                    $result[$key]['meta']['note'] = $note_array;
                }
            }
        }
        return $result;
    }

    public function myCalendar(Request $request){
        #Get Month events
        if(Auth::user()->role_id == 2){
            #Get people id here
            $people = People::where('User_id', Auth::user()->id)->select('PeopleID')->first();
            $people_id = ($people) ? $people->PeopleID : '' ;

            #Start Query from tbleventdate table with from_date and to_date
            if($request->has('from_date') && $request->has('to_date') && $request->from_date != '' && $request->to_date != ''){
                $events_data = Tbleventdate::where('Eventdate','>=',$request->from_date)
                                            ->where('Eventdate','<=',$request->to_date);
                #Get all Not-Availabilities
                $unavailability = PeopleNotAvailabilities::where('peopleId',$people_id)
                                                        ->where('StartDate','>=',$request->from_date)
                                                        ->where('EndDate','<=',$request->to_date)
                                                        ->get()->toArray();
                #Get all Holidays
                $holiday_list = HolidayList::where('StatDate','>=',$request->from_date)
                                            ->where('StatDate','<=',$request->to_date)
                                            ->get()->toArray();
            }
            
            #Get Data for single date
            if($request->has('single_date') && !empty($request->single_date)){
                $events_data = Tbleventdate::where('Eventdate',$request->single_date);
                #Get all Not-Availabilities
                $unavailability = PeopleNotAvailabilities::where('peopleId',$people_id)
                                                        ->where('StartDate',$request->single_date)
                                                        ->get()->toArray();
                #Get all Holidays
                $holiday_list = HolidayList::where('StatDate',$request->single_date)
                                            ->get()->toArray();
            }     

            #Get Event Detail here using With function
            $events_data->with([
                    'shift' => function($shift_query) use ($people_id){
                        $shift_query->where('PeopleID',$people_id)
                        ->select('ID','PeopleID','EID','PID','DID',
                        'Start1','Finish1','Start2','Finish2','Start3',
                        'Finish3','Confirmed');
                    },'shift.event'=>function($event_q){
                        $event_q->select('VID','EID','EventName');
                    }
                ]);
                
            #Get only shift where people_id = logged in people id
            $events =  $events_data->whereHas('shift',function($shift_query) use ($people_id){
                $shift_query->where('PeopleID',$people_id);
            })->select('DID','EID','Eventdate')->get();
            $response = $this->formatCalendarResponseData($events->toArray());
            $key = count($response);
            if(!empty($unavailability)){
                foreach($unavailability as $unavailability_data){
                    $response[$key]['id'] = $unavailability_data['ID'];
                    $response[$key]['allDay'] = true;
                    $response[$key]['title'] = $unavailability_data['Reason'];
                    $response[$key]['start'] = $unavailability_data['StartDate'];
                    $response[$key]['end'] = $unavailability_data['EndDate'];
                    $response[$key]['type'] = 'unavailability';
                    $key++;
                }
            }

            $second_key = count($response);
            if(!empty($holiday_list)){
                foreach($holiday_list as $holiday_data){
                    $response[$second_key]['id'] = $holiday_data['ID'];
                    $response[$second_key]['allDay'] = true;
                    $response[$second_key]['title'] = $holiday_data['Description'];
                    $response[$second_key]['start'] = $holiday_data['StatDate'];
                    $response[$second_key]['end'] = $holiday_data['StatDate'];
                    $response[$second_key]['type'] = 'holiday';
                    $second_key++;
                }
            }
            
            return $this->response->array(['status' => 200, 'data' => $response]);
        }
        return response()->json(['error' => 'Not authorized to use this api.'], 422);
    }

    #Format calandar data here
    private function formatCalendarResponseData($events_data){
        $result = array();
        if(!empty($events_data)){
            foreach($events_data as $key => $event_value){
                if(isset($event_value['shift']) && !empty($event_value['shift'])){
                    foreach($event_value['shift'] as $shift_data){
                        $result[$key]['Confirmed'] = $shift_data['Confirmed']; 
                        $result[$key]['id'] = $shift_data['EID'];   
                        $result[$key]['shift_id'] = $shift_data['ID']; 
                        $result[$key]['incripted_shift_id'] =  $this->base62encode($shift_data['ID']);
                        $result[$key]['allDay'] = true;
                        $result[$key]['title'] = '';
                        $result[$key]['start'] = $event_value['Eventdate'];
                        $result[$key]['end'] = $event_value['Eventdate'];
                        $result[$key]['from_time'] = '';
                        $result[$key]['to_time'] = '';
                        $result[$key]['type'] = 'event';
                        if(!empty($shift_data['event'])){
                            $result[$key]['title'] = $shift_data['event']['EventName'];
                        }
                        if($shift_data['Start3']){
                            $result[$key]['from_time'] = date('H:i', strtotime($shift_data['Start3']));
                        }
                        if($shift_data['Start2']){
                            $result[$key]['from_time'] = date('H:i', strtotime($shift_data['Start2']));
                        }
                        if($shift_data['Start1']){
                            $result[$key]['from_time'] = date('H:i', strtotime($shift_data['Start1']));
                        }
                       
                        if($shift_data['Finish1']){
                            $result[$key]['to_time'] = date('H:i', strtotime($shift_data['Finish1']));
                        }
                        if($shift_data['Finish2']){
                            $result[$key]['to_time'] = date('H:i', strtotime($shift_data['Finish2']));
                        }
                        if($shift_data['Finish3']){
                            $result[$key]['to_time'] = date('H:i', strtotime($shift_data['Finish3']));
                        }
                    }
                }
            }
        }
        return $result;
    }

    /***Encode shift Id***/
    private function base62encode($data) {
        $outstring = '';
        $l = strlen($data);
        for ($i = 0; $i < $l; $i += 8) {
            $chunk = substr($data, $i, 8);
            $outlen = ceil((strlen($chunk) * 8)/6); //8bit/char in, 6bits/char out, round up
            $x = bin2hex($chunk);  //gmp won't convert from binary, so go via hex
            $w = gmp_strval(gmp_init(ltrim($x, '0'), 16), 62); //gmp doesn't like leading 0s
            $pad = str_pad($w, $outlen, '0', STR_PAD_LEFT);
            $outstring .= $pad;
        }
        return $outstring;
    }
        

}
