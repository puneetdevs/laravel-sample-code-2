<?php

namespace App\Repositories;

use App\Exceptions\Handler;
use App\Repositories\BaseRepository;
use App\Models\TbleventWaitingList;
use DateTime;
use Carbon\Carbon;
use App\User;
use Auth;
use DB;


/**
 * Class WaitinglistRepository.
 */
class WaitinglistRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return TbleventWaitingList::class;
    }
}
