<?php
namespace App\Transformers;

use LaraCrud\Helpers\TransformerAbstract;
use League\Fractal\ParamBag;
use App\Models\Note;



class NoteTransformer extends TransformerAbstract
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
			"id" => $note->ID,
			"note" => $note->note,
			"object_type" => $note->object_type,
			"object_id" => $note->object_id,
			"created_at" => $note->created_at,
			"updated_at" => $note->updated_at,

        ];
        return $this->filterFields($data);

    }

    
}