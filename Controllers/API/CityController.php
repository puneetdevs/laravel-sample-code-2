<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use App\Models\City;
use App\Transformers\CityTransformer;
use App\Http\Requests\Api\Cities\Create;
use App\Http\Requests\Api\Cities\Update;
/**
 * city
 *
 * @Resource("city", uri="/cities")
 */

class CityController extends ApiController
{
    
    public function index(Request $request)
    {
        $perPage = 10;
        $offset = '0';
        if($request->has('per_page')){
            $perPage = $request->per_page;
        }
        $columns_search = ['City'];

        $data = City::where([]);

        /****** Search *******/
        if($request->has('q')){
            foreach($columns_search as $column){
                $data->orWhere($column, 'LIKE', '%' . $request->q . '%');
            }
        }
       $data = $data->orderBy('ID', 'desc');
       $data =  $data->paginate($perPage);
       return $this->response->paginator($data, new CityTransformer());
    }

    public function show(Request $request, $city)
    {
        $city = City::find($city);
        if( $city && is_null($city)==false ){
            return $this->response->item($city, new CityTransformer());
        }
        return $this->response->errorNotFound('City Not Found', 404);
    }

    public function store(Create $request)
    {
        $model=new City;
        $model->fill($request->all());
        $model->City = $request->name;

        if ($model->save()) {
            return $this->response->item($model, new CityTransformer());
        } else {
            return response()->json(['error' => 'Error occurred while saving city'], 422);
        }
    }
 
    public function update(Update $request, $city)
    {
        
        $city = City::findOrFail($city);
        $city->fill($request->all());
        $city->City = $request->name;
        if ($city->save()) {
            return $this->response->item($city, new CityTransformer());
        } else {
            return response()->json(['error' => 'Error occurred while saving city'], 422);
        }
    }

    public function destroy(Request $request, $city)
    {
        $city = City::findOrFail($city);

        if ($city->delete()) {
            return $this->response->array(['status' => 200, 'message' => 'city successfully deleted']);
        } else {
            return response()->json(['error' => 'Error occurred while deleting city'], 422);
        }
    }

}
