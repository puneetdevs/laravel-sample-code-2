<?php

namespace App\Http\Controllers\Api;
use App\Http\Requests\Api\Positions\PositionRequest;
use App\Http\Requests\Api\Positions\ChangeStatus;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use App\Models\Position;
use App\Transformers\PositionTransformer;


/**
 * position
 *
 * @Resource("position", uri="/positions")
 */

class PositionController extends ApiController
{
    
    public function index(Request $request)
    {
        $perPage = 10;
        $offset = '0';
        if($request->has('per_page')){
            $perPage = $request->per_page;
        }
        $columns_search = ['Position'];

        $data = Position::where([]);

        /****** Status *******/
        if($request->has('status')){
            $data->where('status',$request->status);
        }
        
        /****** Search *******/
        if($request->has('q') && !empty($request->q)){
           $data->where('Position', 'LIKE', '%' . $request->q . '%');
        }
       $data =  $data->orderBy('PID', 'desc');
       $data =  $data->paginate($perPage);
       return $this->response->paginator($data, new PositionTransformer());
    }

    public function show(Request $request, $position)
    {
        $position = Position::find($position);
        if( $position && is_null($position)==false ){
            return $this->response->item($position, new PositionTransformer());
        }
        return $this->response->errorNotFound('Position Not Found', 404);
    }

    public function store(PositionRequest $request)
    {
        $model=new Position;
        $input = $request->all();
        $input = $this->convert_input($input);
        $model->fill($input);
        if ($model->save()) {
            return $this->response->item($model, new PositionTransformer());
        } else {
            return response()->json(['error' => 'Error occurred while saving position.'], 422);
        }
    }
 
    public function update(Request $request, $position)
    {
       
        $position = position::findOrFail($position);
        $position->fill($request->all());
        /* Save Fields */
        $position->Position = $request->name;
        $position->SortOrder = $request->pos_order;
        
        if( isset($request->pos_id) ){
            $position->pos_id = $request->pos_id;
        }
        
        /* Save Fields */
        
        if ($position->save()) {
            return $this->response->item($position, new PositionTransformer());
        } else {
            return response()->json(['error' => 'Error occurred while saving position.'], 422);
        }
    }

    public function destroy(Request $request, $position)
    {
        $position = Position::findOrFail($position);

        if ($position->delete()) {
            return $this->response->array(['status' => 200, 'message' => 'position successfully deleted']);
        } else {
            return response()->json(['error' => 'Error occurred while deleting position.'], 422);
        }
    }

    public function changePositionStatus(ChangeStatus $request, $position)
    {
        $position = Position::findOrFail($position);
        if ($position->update(['status' => $request->status])) {
            return $this->response->array(['status' => 200, 'message' => 'position status changed successfully']);
        } else {
            return response()->json(['error' => 'Error occurred while status changed of position.'], 422);
        }
    }

    protected function convert_input($input){
        $val_keys = ['name'=>'Position', 'pos_order'=>'SortOrder'];
        $out = [];
        foreach($input as $field_key=>$field_val){
            if(isset($val_keys[$field_key])){
                $out[$val_keys[$field_key]] = $field_val;
            }
        }
       return $out;
    }
}
