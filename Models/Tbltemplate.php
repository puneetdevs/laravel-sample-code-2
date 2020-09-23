<?php
namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use HipsterJazzbo\Landlord\BelongsToTenants;
use Illuminate\Database\Eloquent\Model;
/**
   @property varchar $TemplateName TemplateName
@property datetime $DateCreated DateCreated
@property int $RateConfiguration RateConfiguration
@property int $Venue Venue
@property int $Client Client
@property varchar $DefaultName DefaultName
@property smallint $Schedule Schedule
@property datetime $created_at created at
@property datetime $updated_at updated at
@property datetime $deleted_at deleted at
   
 */
class Tbltemplate extends Model 
{
    use SoftDeletes;
    use BelongsToTenants;
    
    public $tenantColumns = ['region_id'];
    /**
    * Database table name
    */
    protected $table = 'templates';

    /**
    * Mass assignable columns
    */
    protected $fillable=[
        'TemplateName',
        'RateConfiguration',
        'Venue',
        'Client',
        'region_id',
        'DefaultName',
        'Schedule'
    ];

    protected $primaryKey = 'TemplateID';

    /**
    * Date time columns.
    */
    protected $dates=['DateCreated'];

    /*Relation with Location*/
    public function location()
    {
        return $this->hasOne(Location::class, 'VID', 'Venue');
    }

    //client relation with Template
    public function client(){
        return $this->hasOne(Client::class,'ID','Client');
    }

    //note relation with Template
    public function note(){
        return $this->hasMany(Note::class,'ParentId','TemplateID');
    }

    //Rate relation with Template
    public function rate(){
        return $this->hasOne(Configuration::class,'CfgID','RateConfiguration');
    }

}