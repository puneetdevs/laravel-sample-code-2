<?php

namespace App\Repositories;

use App\Exceptions\Handler;
use App\Repositories\BaseRepository;
use App\Models\Tblevent;
use App\Models\DataFileSettings;
use App\Models\Region;
use DateTime;
use Carbon\Carbon;
use App\User;
use Auth;
use DB;
use App\Models\EventCounter;


/**
 * Class NotesRepository.
 */
class EventRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return Tblevent::class;
    }

    public function create(array $data)
    { 
        $region = Region::where('id',Auth::user()->region_id)->first();
        
        $data['EventID'] = ($region) ? $region->code.date('y').'-'.$this->getEventId() : $this->getEventId();
        $data['EventCreatedBy'] = Auth::user()->id;
        $data['EventUpdatedBy'] = Auth::user()->id;
        return DB::transaction(function () use ($data) {
        $event = parent::create($data);
            if ($event) {
               $event->id = $event->EventID;
               $this->updateEventID();
               return $event;
            }
            throw new GeneralException('Oops! Something went wrong while creating Tblevent.');
        });
        
    }

    function getEventId() {
        $user = Auth::user();
        $event_counter = EventCounter::select('Value')->where(['region_id'=>$user->region_id])->first();
        if($event_counter){
            return $event_counter->Value;
        }
        $value = 1;
        $region_setting = DataFileSettings::where('region_id',Auth::user()->region_id)->first();
        if($region_setting){
            $value = $region_setting->EventCounter;
        }
        EventCounter::insert(['Value'=>$value, 'region_id'=>$user->region_id]);
        return $value;
    }

    function updateEventID(){
        $user = Auth::user();
        $last_number = $this->getEventId();
        $new_Number = $last_number+1;
        EventCounter::query()->update(['Value'=>$new_Number, 'region_id'=>$user->region_id]);
        return $new_Number;
    }
    
}
