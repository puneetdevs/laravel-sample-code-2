<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use App\Models\Tblworkcategory;
use App\Transformers\TblworkcategoryTransformer;
use App\Http\Requests\Api\Tblworkcategory\Index;
use App\Http\Requests\Api\Tblworkcategory\Show;
use App\Http\Requests\Api\Tblworkcategory\Create;
use App\Http\Requests\Api\Tblworkcategory\Store;
use App\Http\Requests\Api\Tblworkcategory\Edit;
use App\Http\Requests\Api\Tblworkcategory\Update;
use App\Http\Requests\Api\Tblworkcategory\Destroy;
use App\Repositories\WorkcategoryRepository;
use Cache;

/**
 * Tblworkcategory
 *
 * @Resource("Tblworkcategory", uri="/work_categories")
 */

class TblworkcategoryController extends ApiController
{
    
    public function index(Index $request)
    {
        $perPage = 10;
        if($request->has('per_page')){
            $perPage = $request->per_page;
        }
        $data = Tblworkcategory::orderBy('SortOrder','ASC');
        
        $columns_search = ['Description','Code'];
        /****** Search *******/
        if($request->has('q')){
            foreach($columns_search as $column){
                $data->orWhere($column, 'LIKE', '%' . $request->q . '%');
            }
        }
        
        $data = $data->paginate($perPage);
        return $this->response->paginator($data, new TblworkcategoryTransformer());

    }

    public function show(Show $request, Tblworkcategory $work_categories)
    {
      return $this->response->item($work_categories, new TblworkcategoryTransformer());
    }

    public function store(Store $request)
    {
        $model=new Tblworkcategory;
        $model->fill($request->all());
        if ($model->save()) {
            return $this->response->item($model, new TblworkcategoryTransformer());
        } else {
            return response()->json(['error' => 'Error occurred while saving work category.'], 422);
        }
    }
 
    public function update(Update $request, $work_id ,WorkcategoryRepository $workcategoryRepository)
    {
        if ($work_categories =  $workcategoryRepository->updateById( $work_id, $request->all() ) ) {
            return $this->response->item($work_categories, new TblworkcategoryTransformer());
        } else {
            return response()->json(['error' => 'Error occurred while saving work category.'], 422);
        }
    }

    public function destroy(Destroy $request, $work_categories)
    {
        $work_categories = Tblworkcategory::findOrFail($work_categories);

        if ($work_categories->delete()) {
            return $this->response->array(['status' => 200, 'message' => 'Tblworkcategory successfully deleted']);
        } else {
            return response()->json(['error' => 'Error occurred while deleting work category.'], 422);
        }
    }

}
