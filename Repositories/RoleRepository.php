<?php

namespace App\Repositories;


use DB;
use App\Exceptions\Handler;
use App\Repositories\BaseRepository;
use DateTime;
use Carbon\Carbon;
use App\Role;
use Auth;

/**
 * Class RoleRepository.
 */
class RoleRepository extends BaseRepository
{   
     /**
     * @return string
     */
    public function model()
    {
        return Role::class;
    }
}
