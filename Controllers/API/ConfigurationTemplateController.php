<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use App\Models\ConfigurationTemplate;
use App\Transformers\ConfigurationTemplateTransformer;
use App\Http\Requests\Api\ConfigurationTemplates\Index;
use App\Http\Requests\Api\ConfigurationTemplates\Show;
use App\Http\Requests\Api\ConfigurationTemplates\Create;
use App\Http\Requests\Api\ConfigurationTemplates\Store;
use App\Http\Requests\Api\ConfigurationTemplates\Edit;
use App\Http\Requests\Api\ConfigurationTemplates\Update;
use App\Http\Requests\Api\ConfigurationTemplates\Destroy;
use Auth;


class ConfigurationTemplateController extends ApiController
{
    
    public function index(Index $request)
    {
        $perPage = 10;
        $offset = '0';
        if($request->has('per_page')){
            $perPage = $request->per_page;
        }

        $data = ConfigurationTemplate::where('region_id', Auth::user()->region_id);

        /****** Search *******/
        if($request->has('q')){
             $data->where('ConfigName', 'LIKE', '%' . $request->q . '%');
        }
        $data = $data->orderBy('ID','desc');
        $data =  $data->paginate($perPage);
        return $this->response->paginator($data, new ConfigurationTemplateTransformer());
    }

    public function show(Show $request, $configurationtemplate)
    {
        $configuration_template = ConfigurationTemplate::find($configurationtemplate);
        if( $configuration_template && is_null($configuration_template)==false ){
            return $this->response->item($configuration_template, new ConfigurationTemplateTransformer());
        }
        return $this->response->errorNotFound('Template Not Found', 404);
    }

    public function store(Store $request)
    {
        $model=new ConfigurationTemplate;
        $model->fill($request->all());
        if ($model->save()) {
            return $this->response->item($model, new ConfigurationTemplateTransformer());
        } else {
            return response()->json(['error' => 'Error occurred while saving ConfigurationTemplate.'], 422);
        }
    }
 
    public function update(Update $request, $configurationtemplate)
    {
        $ConfigurationTemplate = ConfigurationTemplate::findOrFail($configurationtemplate);
        $ConfigurationTemplate->fill($request->all());
        if ($ConfigurationTemplate->save()) {
            return $this->response->item($ConfigurationTemplate, new ConfigurationTemplateTransformer());
        } else {
            return response()->json(['error' => 'Error occurred while update ConfigurationTemplate.'], 422);
        }

    }

    public function destroy(Destroy $request, $configurationtemplate)
    {
        $ConfigurationTemplate = ConfigurationTemplate::findOrFail($configurationtemplate);

        if ($ConfigurationTemplate->delete()) {
            return $this->response->array(['status' => 200, 'message' => 'ConfigurationTemplate successfully deleted']);
        } else {
            return response()->json(['error' => 'Error occurred while deleting ConfigurationTemplate.'], 422);
        }
    }

}
