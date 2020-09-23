<?php
namespace App\Models;
use App\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
    
/**
    @property varchar $chat_group_id  chat group id
    @property int $people_id people id
    @property datetime $created_at created at
    @property datetime $updated_at updated at
    @property datetime $deleted_at deleted at
 */
class ChatGroupMemeber extends Model 
{
    use SoftDeletes;
    /**
    * Database table name
    */
    protected $table = 'chat_group_memeber';

    /**
    * Mass assignable columns
    */
    protected $fillable=[
        'chat_group_id',
        'people_id',
        'unread_message_count'
    ];

    /**
    * Date time columns.
    */
    protected $dates=[];

    //Shidt relation with event_schedule
    public function group(){
        return $this->hasOne(ChatGroup::class,'id','chat_group_id');
    }

    //Shidt relation with people
    public function people(){
        return $this->hasOne(People::class,'PeopleID','people_id');
    }

    //Shidt relation with people
    public function user(){
        return $this->hasOne(User::class,'id','people_id');
    }


}