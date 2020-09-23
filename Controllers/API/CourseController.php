<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use App\Models\Course;
use App\Transformers\CourseTransformer;
use App\Http\Requests\Api\Courses\Create;
use App\Http\Requests\Api\Courses\Update;

/**
 * Course
 *
 * @Resource("Course", uri="/courses")
 */

class CourseController extends ApiController
{
    
    public function index(Request $request)
    {
        $perPage = 10;
        $offset = '0';
        if($request->has('per_page')){
            $perPage = $request->per_page;
        }
        $columns_search = ['name'];

        $data = Course::where([]);

        /****** Search *******/
        if($request->has('q')){
            $data->where(function ($query) use($columns_search, $request) {
                foreach($columns_search as $column) {
                    $query->orWhere($column, 'LIKE', '%' . $request->q . '%');
                }
            });
        }
       $data = $data->orderBy('id', 'desc');
       $data =  $data->paginate($perPage);
       return $this->response->paginator($data, new CourseTransformer());
       
    }

    public function show(Request $request, $course)
    {
        $course = Course::find($course);
        if( $course && is_null($course)==false ){
            return $this->response->item($course, new CourseTransformer());
        }
        return $this->response->errorNotFound('Course Not Found', 404);
    }

    public function store(Create $request)
    {
        $model=new Course;
        $model->fill($request->all());
        if ($model->save()) {
            return $this->response->item($model, new CourseTransformer());
        } else {
            return response()->json(['error' => 'Error occurred while saving Course.'], 422);
        }
    }
 
    public function update(Update $request,  $course)
    {
        $course = Course::findOrFail($course);
        $course->fill($request->all());

        if ($course->save()) {
            return $this->response->item($course, new CourseTransformer());
        } else {
            return response()->json(['error' => 'Error occurred while update Course.'], 422);
        }
    }

    public function destroy(Request $request, $course)
    {
        $course = Course::findOrFail($course);

        if ($course->delete()) {
            return $this->response->array(['status' => 200, 'message' => 'Course successfully deleted']);
        } else {
            return response()->json(['error' => 'Error occurred while deleting Course.'], 422);
        }
    }

}
