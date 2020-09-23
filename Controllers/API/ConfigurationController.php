<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use App\Models\Configuration;
use App\Models\ConfigurationRates;
use App\Models\Tblprogramsetting;
use App\Models\ConfigurationTemplate;
use App\Transformers\ConfigurationsTransformer;
use App\Http\Requests\Api\Configurations\Create;
use App\Http\Requests\Api\Configurations\Update;
use App\Http\Requests\Api\Configurations\ErrorRequest;
use App\Repositories\ConfigurationRepository;
use App\Http\Requests\Api\Configurations\RateConfig\StoreRateConfig;
use App\Transformers\RateConfigurationsTransformer;
use Auth;

/**
 * 
 * configurations
 *
 * @Resource("city", uri="/cities")
 */

class ConfigurationController extends ApiController
{
    public function __construct(ConfigurationRepository $ConfigurationRepository)
    {
        $this->ConfigurationRepository = $ConfigurationRepository;
    }
    public function index(Request $request)
    {
        $perPage = 10;
        $offset = '0';
        if($request->has('per_page')){
            $perPage = $request->per_page;
        }

        $data = Configuration::where('region_id', Auth::user()->region_id);

        /****** Search *******/
        if($request->has('q')){
             $data->where('ConfigName', 'LIKE', '%' . $request->q . '%');
        }
        $data = $data->orderBy('CfgID','desc');
        $data =  $data->paginate($perPage);
        return $this->response->paginator($data, new ConfigurationsTransformer());
    }

    public function show(Request $request, $configuration)
    {
        $configuration = Configuration::find($configuration);
        if( $configuration && is_null($configuration)==false ){
            return $this->response->item($configuration, new ConfigurationsTransformer());
        }
        return $this->response->errorNotFound('City Not Found', 404);
    }

    public function store(Create $request)
    {
        $model=new Configuration;
        if($request->has('ConfigTemplateID') && !empty($request->ConfigTemplateID)){
            $config_template = ConfigurationTemplate::where('ID',$request->ConfigTemplateID)->first();
            $config_template = $config_template->toArray();
            unset($config_template['ID']);
            $model->fill($config_template->toArray());
        }else{
            $requested_data = $request->all();
            if(isset($requested_data['ConfigTemplateID'])){
                unset($requested_data['ConfigTemplateID']);
            }
            $model->fill($request->all());
        }
       
        if ($model->save()) {
            return $this->response->item($model, new ConfigurationsTransformer());
        } else {
            return response()->json(['error' => 'Error occurred while saving configuration'], 422);
        }
    }
 
    public function update(Update $request, $configuration)
    {
        $configuration = Configuration::findOrFail($configuration);
        $configuration->fill($request->all());
        if ($configuration->save()) {
            return $this->response->item($configuration, new ConfigurationsTransformer());
        } else {
            return response()->json(['error' => 'Error occurred while update configuration'], 422);
        }
    }

    public function destroy(Request $request, $configuration)
    {
        $configuration = Configuration::findOrFail($configuration);

        if ($configuration->delete()) {
            return $this->response->array(['status' => 200, 'message' => 'configurations successfully deleted']);
        } else {
            return response()->json(['error' => 'Error occurred while deleting configuration'], 422);
        }
    }


    public function AddPositionConfig(StoreRateConfig $request, $configuration){
        $insert_data = $request->only('PID', 'CfgID', 'BasePay', 'BaseCharge', 'OtherCharge', 'Flat', 'EnablePayrollAdj');
        $configuration_id = $configuration;
        $configuration = $this->ConfigurationRepository->getById($configuration_id);
        if($configuration && is_null($configuration)==false ){
            $chk_PID_already = ConfigurationRates::where('CfgID',$configuration_id)->where('PID',$request->PID)->first();
            if(!$chk_PID_already){
                $PositionConfig = $this->ConfigurationRepository->addRateConfiguration($insert_data, $configuration_id);
                return $this->response->array(['status' => 200, 'message' => 'Rate Configuration has been Added']);
            }
            return response()->json(['error' => 'Position already exists in same Configuration.'], 422);
        } else {
            return response()->json(['error' => 'Unable to Find Configuration.'], 422);
        }
    }

    public function getPositionConfig(Request $request, $configuration) {
        $configuration_id = $configuration;
        $configuration = $this->ConfigurationRepository->getById($configuration_id);
        if($configuration && is_null($configuration)==false ){
            
            $perPage = 10;
            $offset = '0';
            if($request->has('per_page')){
                $perPage = $request->per_page;
            }
            $columns_search = [  ];
            $data = $this->ConfigurationRepository->getRateConfigurations($configuration_id);
            
            /****** Search *******/
            if($request->has('q')){
                foreach($columns_search as $column){
                    $data->orWhere($column, 'LIKE', '%' . $request->q . '%');
                }
            }
            $data =  $data->paginate($perPage);
            return $this->response->paginator($data, new RateConfigurationsTransformer());
        } else {
            return response()->json(['error' => 'Unable to Find Configuration.'], 422);
        }
    }

    public function showPositionConfig(Request $request,$configuration, $rate_id)
    {
        try{
            $rateConfiguration = $this->ConfigurationRepository->SingleRateConfiguration($configuration, $rate_id);
            if($rateConfiguration && is_null($rateConfiguration)==false ){
                return $this->response->item($rateConfiguration, new RateConfigurationsTransformer());
            }
            return response()->json(['error' => 'Rate Configuration Not Found.'], 422);
        }
        catch(Exception $e) {
            return response()->json(['error' => 'Rate Configuration Not Found.'], 422);
        }
    }
    function updatePositionConfig(StoreRateConfig $request,$configuration, $rate_id) {
        $update_data = $request->only('PID', 'CfgID', 'BasePay', 'BaseCharge', 'OtherCharge', 'Flat', 'EnablePayrollAdj');
        $rateConfiguration =$this->ConfigurationRepository->SingleRateConfiguration($configuration, $rate_id);
        if($rateConfiguration && is_null($rateConfiguration)==false ){
            $output_data = $this->ConfigurationRepository->updateRateConfiguration($configuration, $rate_id,  $update_data);
            return $this->response->array(['status' => 200, 'message' => 'Rate configuration successfully Updated']);
        }
        return response()->json(['error' => 'Rate Configuration Not Found.'], 422);
    }

    function deletePositionConfig(Request  $request,$configuration, $rate_id) {
        try{
            $rateConfiguration =$this->ConfigurationRepository->SingleRateConfiguration($configuration, $rate_id);
            if($rateConfiguration && is_null($rateConfiguration)==false ){
                $this->ConfigurationRepository->deleteRateConfiguration($configuration, $rate_id);
                return $this->response->array(['status' => 200, 'message' => 'Rate Configuration Successfully deleted']);
            }
            return response()->json(['error' => 'Rate Configuration Not Found.'], 422);
        }
        catch(Exception $e) {
            return response()->json(['error' => 'Rate Configuration Not Found.'], 422);
        }
    }

    /**
     * Update Error Setting
     *
     * @param  mixed $request
     *
     * @return void
     */
    public function updateErrorSetting(ErrorRequest $request){
        $setting = Tblprogramsetting::first();
        if($setting){
            if(Tblprogramsetting::where('ID', $setting->ID)->update($request->all())){
                return $this->response->array(['status' => 200, 'message' => 'Error Setting has been updated']);
            }
            return response()->json(['error' => 'Error setting not updated, Please try again.'], 422);
        }
        return response()->json(['error' => 'Setting not created yet.'], 422);
    }

    /**
     * Get Error Setting
     *
     * @param  mixed $request
     *
     * @return void
     */
    public function getErrorSetting(Request $request){
        $setting = Tblprogramsetting::first();
        if($setting){
            return $this->response->array(['status' => 200, 'data' => $setting->toArray()]);
        }
        return $this->response->array(['status' => 200, 'data' => []]);
    }


}
