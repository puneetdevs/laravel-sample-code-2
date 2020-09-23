<?php
namespace App\Transformers;

use LaraCrud\Helpers\TransformerAbstract;
use League\Fractal\ParamBag;
use App\Models\PeopleDocument;
use App\User;



class DocumentTransformer extends TransformerAbstract
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
   


    public function transform(PeopleDocument $document)
    {
        $data= [
            "id" => $document->id,
            "title" => $document->title,
            "description" => $document->discription,
            "file_id" => $document->file_id,
            "user_type" => $document->user_type,
            "file_detail"=>$document->FileDetails,
            "created_at"=>$document->created_at
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
    public function includeFileDetails(tblPeopleTraining $document)
    {
        return $this->item($document->FileDetails, new TrainingFileDetailsTransformer);
    }
    
}