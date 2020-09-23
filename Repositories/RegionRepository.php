<?php

namespace App\Repositories;


use DB;
use App\Exceptions\Handler;
use App\Repositories\BaseRepository;
use App\Models\DataFileSettings;
use DateTime;
use Carbon\Carbon;
use Auth;
use App\Models\EmployeeCounterPrefix;
/**
 * Class NotesRepository.
 */
class RegionRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return DataFileSettings::class;
    }

    /**
     * @param $email
     *
     * @return User
    */

    function getRegionSetiings(){
        $user = Auth::user();
        $region = $user->region_id;
        $region_data = DataFileSettings::where('region_id', $region)->first();
        if($region_data) {
            return $region_data;
        } else {
            $region_settings = new DataFileSettings;
            $region_settings->region_id = $region;
            $region_settings->CompanyName = '';
            $region_settings->Tax1_ApplyLabour = '0';
            $region_settings->Tax2_ApplyLabour = '0';
            $region_settings->Default_AS_Tax1 = '0';
            $region_settings->Default_AS_Tax2 = '0';
            $region_settings->EmployeeCounter = 0;
            $region_settings->EventCounter = 0;
            $region_settings->reminder_time_off_start = '00:00';
            $region_settings->reminder_time_off_end = '07:00';
            $region_settings->reminder_send_before_time = '24';
            $region_settings->time_zone = 'America/Los_Angeles';
            $region_settings->save();
            return $region_settings;
        }
    }
    
    function getEmployeeCodePrefix(){
        return EmployeeCounterPrefix::get();
    }
    
    function addEmployeeCodePrefix($data) {
        $emp_counter = new EmployeeCounterPrefix;
        $data['region_id'] = Auth::user()->region_id;
        $emp_counter->fill($data);
        $emp_counter->save();
        return $emp_counter;
    }
    
    function getLabourSurcharge() {
        $user = Auth::user();
        $region = $user->region_id;
        $region_data = DataFileSettings::where('region_id', $region)->first();
        if($region_data) {
            return $region_data;
        }
    }

}
