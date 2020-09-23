<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use App\Models\Salutation;
use App\Transformers\SalutationTransformer;
use App\Http\Requests\Api\Salutations\Index;
use App\Http\Requests\Api\Salutations\Show;
use App\Http\Requests\Api\Salutations\Create;
use App\Http\Requests\Api\Salutations\Store;
use App\Http\Requests\Api\Salutations\Update;
use App\Http\Requests\Api\Salutations\Destroy;


/**
 * Salutation
 *
 * @Resource("Salutation", uri="/salutations")
 */

class SalutationController extends ApiController
{
    
    public function index(Index $request)
    {
        $perPage = 10;
        $offset = '0';
        if($request->has('per_page')){
            $perPage = $request->per_page;
        }
        $columns_search = ['Option'];

        $data = Salutation::where([]);

        /****** Search *******/
        if($request->has('q')){
            foreach($columns_search as $column){
                $data->orWhere($column, 'LIKE', '%' . $request->q . '%');
            }
        }
       $data =  $data->orderBy('id', 'desc');
       $data =  $data->paginate($perPage);
       return $this->response->paginator($data, new SalutationTransformer());
    }

    public function show(Show $request, $salutation)
    {
        $salutation = Salutation::find($salutation);
        if( $salutation && is_null($salutation)==false ){
            return $this->response->item($salutation, new SalutationTransformer());
        }
        return $this->response->errorNotFound('Salutation Not Found', 404);
    }

    public function store(Create $request)
    {
        $model=new Salutation;
        $model->fill($request->all());
        $model->Option = $request->name;

        if ($model->save()) {
            return $this->response->item($model, new SalutationTransformer());
        } else {
            return response()->json(['error' => 'Error occurred while saving Salutation.'], 422);
        }
    }
 
    public function update(Update $request, $salutation)
    {
        $salutation = Salutation::findOrFail($salutation);
        $salutation->fill($request->all());
        $salutation->Option = $request->name;
        
        if ($salutation->save()) {
            return $this->response->item($salutation, new SalutationTransformer());
        } else {
            return response()->json(['error' => 'Error occurred while saving Salutation.'], 422);
        }
    }

    public function destroy(Destroy $request, $salutation)
    {
        $salutation = Salutation::findOrFail($salutation);

        if ($salutation->delete()) {
            return $this->response->array(['status' => 200, 'message' => 'Salutation successfully deleted']);
        } else {
            return response()->json(['error' => 'Error occurred while deleting Salutation.'], 422);
        }
    }

}
