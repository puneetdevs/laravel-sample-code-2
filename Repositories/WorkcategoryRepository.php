<?php

namespace App\Repositories;

use App\Exceptions\Handler;
use App\Repositories\BaseRepository;
use App\Models\Tblworkcategory;
use DateTime;
use Carbon\Carbon;
use App\User;
use Auth;
use DB;


/**
 * Class WorkcategoryRepository.
 */
class WorkcategoryRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return Tblworkcategory::class;
    }
}
