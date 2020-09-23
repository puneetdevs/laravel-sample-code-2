<?php

namespace App\Repositories;


use DB;
use App\Exceptions\Handler;
use App\Repositories\BaseRepository;
use App\Models\Tbleventdate;
use DateTime;
use Carbon\Carbon;
/**
 * Class TbleventdateRepository.
 */
class TbleventdateRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return Tbleventdate::class;
    }
    
    public function getEventDate($event_id){
       return $this->model->where(['EID'=>$event_id])->get();
    }

    public function create(array $data)
    { 
        return DB::transaction(function () use ($data) {
            $date = Tbleventdate::create($data);
            if ($date) {
                return $date->DID;
            }
            throw new GeneralException('Oops! Something went wrong while creating event date.');
        });
    }
}
