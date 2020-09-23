<?php

namespace App\Repositories;


use DB;
use App\Exceptions\Handler;
use App\Repositories\BaseRepository;
use App\Models\EventSchedule;
use DateTime;
use Carbon\Carbon;
/**
 * Class ScheduleRepository.
 */
class ScheduleRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return EventSchedule::class;
    }
    
    
}
