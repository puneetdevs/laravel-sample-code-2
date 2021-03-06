<?php
namespace App\Transformers\EventTransformer;

use LaraCrud\Helpers\TransformerAbstract;
use League\Fractal\ParamBag;
use App\Models\Note;

class NoteDetailTransformer extends TransformerAbstract
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


    public function transform(Note $note)
    {
        $data= [
			"ID" => $note->ID,
			"ParentID" => $note->ParentID,
			"ParentCode" => $note->ParentCode,
            "Note" => $note->Note,
            "NoteDate" => $note->NoteDate,
            "AddedBy" => $note->AddedBy,
			"created_at" => $note->created_at,
			"updated_at" => $note->updated_at

        ];
        return $this->filterFields($data);

    }

    
}