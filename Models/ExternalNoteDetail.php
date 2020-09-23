<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use App\Models\Position;
use App\Models\Tbleventdate;

class ExternalNoteDetail extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $table = 'external_note_details';
    

    /**
    * Mass assignable columns
    */
    protected $fillable=['PID', 'EID', 'DID', 'ENID','status'];

    /**
    * Date time columns.
    */
    protected $dates=[];

    /*Relation with External detail*/
    public function positions()
    {
        return $this->hasOne(Position::class, 'PID', 'PID');
    }

    /*Relation with External detail*/
    public function date(){
        return $this->hasOne(Tbleventdate::class,'DID','DID');
    }

}
