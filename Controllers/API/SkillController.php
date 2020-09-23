<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use App\Models\Skill;
use App\Transformers\SkillTransformer;
use App\Http\Requests\Api\Skills\Create;
use App\Http\Requests\Api\Skills\Update;

/**
 * Skill
 *
 * @Resource("Skill", uri="/Skills")
 */

class SkillController extends ApiController
{
    
    public function index(Request $request)
    {
        $perPage = 10;
        $offset = '0';
        if($request->has('per_page')){
            $perPage = $request->per_page;
        }
        $columns_search = ['Skill'];

        $data = Skill::where([]);

        /****** Search *******/
        if($request->has('q')){
            foreach($columns_search as $column){
                $data->orWhere($column, 'LIKE', '%' . $request->q . '%');
            }
        }
       $data =  $data->orderBy('SkID', 'desc');
       $data =  $data->paginate($perPage);
       return $this->response->paginator($data, new SkillTransformer());
       
    }

    public function show(Request $request, $Skill)
    {
        $Skill = Skill::find($Skill);
        if( $Skill && is_null($Skill)==false ){
            return $this->response->item($Skill, new SkillTransformer());
        }
        return $this->response->errorNotFound('Skill Not Found', 404);
    }

    public function store(Create $request)
    {
        $model=new Skill;
        $model->Skill = $request->name;
        $model->fill($request->all());
        if ($model->save()) {
            
            return $this->response->item($model, new SkillTransformer());
        } else {
            return response()->json(['error' => 'Error occurred while saving Skill.'], 422);
        }
    }
 
    public function update(Update $request, $Skill)
    {   
        $Skill = Skill::findOrFail($Skill);
        $Skill->fill($request->all());
        $Skill->Skill = $request->name;

        if ($Skill->save()) {
            return $this->response->item($Skill, new SkillTransformer());
        } else {
            return response()->json(['error' => 'Error occurred while saving Skill.'], 422);
        }
    }

    public function destroy(Request $request, $Skill)
    {
        $Skill = Skill::findOrFail($Skill);

        if ($Skill->delete()) {
            return $this->response->array(['status' => 200, 'message' => 'Skill successfully deleted']);
        } else {
            return response()->json(['error' => 'Error occurred while deleting Skill.'], 422);
        }
    }

}
