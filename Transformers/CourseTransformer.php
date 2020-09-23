<?php
namespace App\Transformers;

use LaraCrud\Helpers\TransformerAbstract;
use League\Fractal\ParamBag;
use App\Models\Course;



class CourseTransformer extends TransformerAbstract
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


    public function transform(Course $course)
    {
        $data= [
			"id" => $course->id,
			"name" => $course->name,
			"created_at" => $course->created_at,
			"updated_at" => $course->updated_at,
			"deleted_at" => $course->deleted_at,

        ];
        return $this->filterFields($data);

    }

    
}