<?php
namespace App\Transformers\EmployeeTransformer;

use LaraCrud\Helpers\TransformerAbstract;
use League\Fractal\ParamBag;
use App\Models\People;
use App\Models\PreBooking;
use App\Models\PeopleSkillEvaluation;
use App\Models\Skill;
use App\Models\PeopleNotAvailabilities;
use App\User;




class EmployeeFilterTransformer extends TransformerAbstract
{

    protected $date_ids;

    public function __construct($dates = null, $date_ids = null) {
        $this->dates = $dates;
        $this->date_ids = $date_ids;
    }


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


    public function transform(People $People)
    {
        $user_skill = '';
        if($skill_ids = PeopleSkillEvaluation::where('PeopleID',  $People->PeopleID)->pluck('SkillEvaluationID')->toArray()){
            $user_skill = Skill::whereIn('SkID',  $skill_ids)->pluck('Skill');
        }

        $user_image = '';
        if(isset(User::select('img')->where('id',  $People->user_id)->first()->img)){
            $user_image = User::select('img')->where('id',  $People->user_id)->first()->img;
        }

        $not_available = array();
        if($this->dates){
            $not_avail_query = PeopleNotAvailabilities::where('peopleId',$People->PeopleID)->whereIn("StartDate", $this->dates)->get()->toArray();
            if(!empty($not_avail_query)){
                $not_available = $not_avail_query;
            }
        }

        $pre = 0;
        if($this->date_ids){
            $pre_book = PreBooking::where('PeopleID',$People->PeopleID)->whereIn("DID", $this->date_ids)->get()->toArray();
            if(!empty($pre_book)){
                $pre = 1;
            }
        }
        
        $data= [
			"id" => $People->PeopleID,
            "FirstName" => $People->FirstName,
			"LastName" => $People->LastName,
            "City" => $People->City,
            "Prov" => $People->Prov,
            "pre" => $pre,
            "Postal" => $People->Postal,
            "Country" => $People->Country,
            "Region" => $People->Region,
            "Sex" => $People->Sex,
            "NickName" => $People->NickName,
            "do_not_call" => $People->do_not_call,
            "user_id" => $People->user_id,
            'region_id'=>$People->region_id,
            'image'=> $user_image,
            'skill'=> $user_skill,
            'not_available' => $not_available
        ];
        return $this->filterFields($data);
    }

    
}