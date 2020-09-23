<?php
namespace App\Transformers;

use LaraCrud\Helpers\TransformerAbstract;
use League\Fractal\ParamBag;
use App\Models\PeopleEvaluation;
use App\User;
use App\Models\PeopleNotAvailabilities;



class PeopleNotAvailabilitesTransformer extends TransformerAbstract
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
   


    public function transform(PeopleNotAvailabilities $notAvailablites)
    {
        $data= [
            "ID" => $notAvailablites->ID,
            "StartDate" => $notAvailablites->StartDate,
            "EndDate" => $notAvailablites->EndDate,
            "Reason" => $notAvailablites->Reason,
            "peopleId" => $notAvailablites->peopleId
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