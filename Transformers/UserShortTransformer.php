<?php
namespace App\Transformers;

use LaraCrud\Helpers\TransformerAbstract;
use App\Transformers\RoleTransformer;
use App\Transformers\PeopleTransformer;
use App\Transformers\FileTransformer;
use League\Fractal\ParamBag;
use App\User;

class UserShortTransformer extends TransformerAbstract
{
     /**
     * @var array
     */
    private $validParams = ['q', 'limit', 'page','fields'];

    /**
     * @var array
     */
    protected $availableIncludes = [];

     /**
      * @var array
      */
    protected $defaultIncludes = [];


    public function transform(User $user)
    {
        $data= [
					"id" => $user->id,
					"region_id" => $user->region_id,
					"fullname" => $user->fullname,
					"username" => $user->email,
          "img" => $user->img,
          "firebase_uid" => $user->firebase_uid,
					"role" => $user->role_id,
					"is_active" => $user->is_active,
					"phonenumber" => $user->phonenumber,
					"email" => $user->email,
					"created_at" => $user->created_at,
					"updated_at" => $user->updated_at,
					"deleted_at" => $user->deleted_at,
        ];
        return $this->filterFields($data);

	}
    
}