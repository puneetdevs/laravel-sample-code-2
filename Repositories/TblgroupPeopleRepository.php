<?php

namespace App\Repositories;


use DB;
use App\Exceptions\Handler;
use App\Repositories\BaseRepository;
use App\Models\TblgroupPeople;
use DateTime;
use Carbon\Carbon;
/**
 * Class TblgroupPeopleRepository.
 */
class TblgroupPeopleRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return TblgroupPeople::class;
    }
   
}
