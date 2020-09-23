<?php
namespace App\Transformers;

use LaraCrud\Helpers\TransformerAbstract;
use League\Fractal\ParamBag;
use App\Models\tblPeopleTraining;
use App\User;



class TrainingTransformer extends TransformerAbstract
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
   


    public function transform(tblPeopleTraining $training)
    {
        $data= [
            "id" => $training->id,
            "course_id" => $training->course_id,
            "people_id" => $training->people_id,
            "completed" => $training->completed,
            "completed_date" => $training->completed_date,
            "expire_date" => $training->expire_date,
            "certificate_number" => $training->certificate_number,
            "file_id" => $training->file_id,
            "file_detail"=>$training->FileDetails,
            "couse_details"=>$training->CourseDetails,
            "People"=>$training->people
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
    public function includeFileDetails(tblPeopleTraining $training)
    {
        return $this->item($training->FileDetails, new TrainingFileDetailsTransformer);
    }
    
}