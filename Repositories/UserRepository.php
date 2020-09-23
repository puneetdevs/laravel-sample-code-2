<?php

namespace App\Repositories;


use DB;
use App\Exceptions\Handler;
use App\Repositories\BaseRepository;
use DateTime;
use Carbon\Carbon;
use App\User;
use Auth;
use App\Models\People;

/**
 * Class UserRepository.
 */
class UserRepository extends BaseRepository
{   
     /**
     * @return string
     */
    public function model()
    {
        return User::class;
    }
    
    public function create(array $data)
    { 
        /* Create User */
        
        return DB::transaction(function () use ($data) {
        $data['user_id'] = $this->createUser($data);
        $people_data['Email'] = $data['email'];
        $people_data['FirstName'] = $data['FirstName'];
        $people_data['LastName'] = $data['LastName'];
        $people_data['AddressLine1'] = $data['AddressLine1'];
        isset($data['Suite'])? $people_data['Suite'] = $data['Suite']: '';
        $people_data['Prov'] = $data['Prov'];
        $people_data['City'] = $data['City'];
        $people_data['Postal'] = $data['Postal'];
        $people_data['Country'] = $data['Country'];
        $people_data['Initial'] = isset($data['Initial']) ? $data['Initial'] : '';
        $people_data['user_id'] = $data['user_id'];
        $people_data['region_id'] = isset($data['region_id']) && !empty($data['region_id']) ? $data['region_id'] : Auth::user()->region_id;
        $people = People::create($people_data);
            if ($people) {
                return  $data['user_id'];
            }
            throw new GeneralException('Oops! Something went wrong while creating People.');
        });
    }

    protected function createUser(array $input){
       
        $input['img'] =  $input['img'];
        $input['email'] = $input['email'];
        $input['is_active'] = 0;
        $input['fullname'] = $input['FirstName'].' '.$input['LastName'];
        $input['username'] = $input['email'];
        $input['role_id'] = $input['role_id'];
        $input['region_id'] = isset($input['region_id']) && !empty($input['region_id']) ? $input['region_id'] : Auth::user()->region_id;
        $user = User::create($input);
        $user_id = $user->id;
        return $user_id;
    }

    public function updatePeople($user_id, array $data)
    {
        $people_data['FirstName'] = $data['FirstName'];
        $people_data['LastName'] = $data['LastName'];
        $people_data['AddressLine1'] = $data['AddressLine1'];
        isset($people_data['Suite']) ? $people_data['Suite'] = $data['Suite']: '';
        $people_data['Prov'] = $data['Prov'];
        $people_data['City'] = isset($data['City']) ? $data['City'] : '';
        $people_data['Initial'] = isset($data['Initial']) ? $data['Initial'] : '';
        $people_data['Postal'] = isset($data['Postal']) ? $data['Postal'] : '';
        $people_data['region_id'] = isset($data['region_id']) ? $data['region_id'] : '';
        $people_data['Country'] = $data['Country'];
        return People::where([
            'user_id'=>$user_id
        ])->update($people_data);
    }

    
}
