<?php
namespace App\Transformers;

use LaraCrud\Helpers\TransformerAbstract;
use League\Fractal\ParamBag;
use App\Models\PeopleEvaluation;
use App\User;



class PeopleEvaluationTransformer extends TransformerAbstract
{
     /**
     * @var array
     */
    private $validParams = ['q', 'limit', 'page','fields'];

    protected $defaultIncludes = [
      //  'FileDetails',
    ];


    /**
     * @var array
     */
    protected $availableIncludes = [];

     /**
      * @var array
      */
   


    public function transform(PeopleEvaluation $evaluation)
    {
        $data= [
            "ID" => $evaluation->ID,
            "EvaluationDate" => $evaluation->EvaluationDate,
            "YearsNotWorked" => $evaluation->YearsNotWorked,
            "PerformanceFactor" => $evaluation->PerformanceFactor,
            "LoyaltyFactor" => $evaluation->LoyaltyFactor,
            "SubTotalHiringFactor" => $evaluation->SubTotalHiringFactor,
            "TotalHiringFactor" => $evaluation->TotalHiringFactor,
            "IndustryExperience" => $evaluation->IndustryExperience,
            "Punctuality" => $evaluation->Punctuality,
            "AttentionToSafety" => $evaluation->AttentionToSafety,
            "AttentionToDetails" => $evaluation->AttentionToDetails,
            "ConductAndAttitude" => $evaluation->ConductAndAttitude,
            "Preparedness" => $evaluation->Preparedness,
            "TeamWorker" => $evaluation->TeamWorker,
            "EmployeeRelations" => $evaluation->EmployeeRelations,
            "ClientRelations" => $evaluation->ClientRelations
            
        ];

        return $this->filterFields($data);
    }

    /**
     * Include ReservationDetail
     *
     * @param  Reservation  $reservation
     *
     * @return League\Fractal\Resource\Collection
     */
    
    
}