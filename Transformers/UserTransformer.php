<?php
namespace App\Transformers;

use LaraCrud\Helpers\TransformerAbstract;
use App\Transformers\RoleTransformer;
use App\Transformers\PeopleTransformer;
use App\Transformers\FileTransformer;
use League\Fractal\ParamBag;
use App\User;

class UserTransformer extends TransformerAbstract
{
     /**
     * @var array
     */
    private $validParams = ['q', 'limit', 'page','fields'];

    /**
     * @var array
     */
    protected $availableIncludes = [
		'role','people','file'
	];

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
			"role" => $user->role_id,
			"firebase_uid" => $user->firebase_uid,
			"is_active" => $user->is_active,
			"phonenumber" => $user->phonenumber,
			"email" => $user->email,
			"created_at" => $user->created_at,
			"updated_at" => $user->updated_at,
			"deleted_at" => $user->deleted_at,

        ];
        return $this->filterFields($data);

	}
	
	/*Role Relation with User get ITEM*/
	public function includeRole(User $entity){
		if( $entity->role != NULL ){
			return $this->item($entity->role, new RoleTransformer());
		}
		return null;
	}
	
	/*People Relation with User get ITEM*/
	public function includePeople(User $entity){
		if( $entity->people != NULL ){
			return $this->item($entity->people, new PeopleTransformer());
		}
		return null;
	}
	
	/*File Relation with User get ITEM*/
	public function includeFile(User $entity){
		if( $entity->file != NULL ){
			return $this->item($entity->file, new FileTransformer());
		}
		return null;
    }
    
}