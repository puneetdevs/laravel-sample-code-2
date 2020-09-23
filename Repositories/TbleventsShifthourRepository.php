<?php

namespace App\Repositories;


use DB;
use App\Exceptions\Handler;
use App\Repositories\BaseRepository;
use App\Models\TbleventsShifthour;
use DateTime;
use Carbon\Carbon;
/**
 * Class TbleventsShifthourRepository.
 */
class TbleventsShifthourRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return TbleventsShifthour::class;
    }
   
}
