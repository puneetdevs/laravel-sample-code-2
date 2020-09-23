<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use App\Models\TblpeoplePayrolladjust;
use App\Transformers\TblpeoplePayrolladjustTransformer;
use App\Http\Requests\Api\TblpeoplePayrolladjust\Index;
use App\Http\Requests\Api\TblpeoplePayrolladjust\Show;
use App\Http\Requests\Api\TblpeoplePayrolladjust\Create;
use App\Http\Requests\Api\TblpeoplePayrolladjust\Store;
use App\Http\Requests\Api\TblpeoplePayrolladjust\Edit;
use App\Http\Requests\Api\TblpeoplePayrolladjust\Update;
use App\Http\Requests\Api\TblpeoplePayrolladjust\Destroy;


/**
 * TblpeoplePayrolladjust
 *
 * @Resource("TblpeoplePayrolladjust", uri="/people_payroll_adjust")
 */

class TblpeoplePayrolladjustController extends ApiController
{
    
    /**
     * Get People Payroll Variable
     *
     * @param  mixed $request
     *
     * @return void
     */
    public function index(Index $request)
    {
        $perPage = 10;
        if($request->has('per_page')){
            $perPage = $request->per_page;
        }
        if($request->has('PeopleID')){
            $payroll_list = TblpeoplePayrolladjust::where('PeopleID', $request->PeopleID)->paginate($perPage);
            return $this->response->paginator($payroll_list, new TblpeoplePayrolladjustTransformer());
        }
        return response()->json(['error' => 'Please send People ID.'], 422);
    }

    /**
     * Get Single Payroll Variable
     *
     * @param  mixed $request
     * @param  mixed $tblpeoplepayrolladjust
     * @param  mixed $id
     *
     * @return void
     */
    public function show(Show $request, TblpeoplePayrolladjust $tblpeoplepayrolladjust, $id)
    {
        $tblpeoplepayrolladjust_data = $tblpeoplepayrolladjust->where('ID', $id)->first();
        return $this->response->item($tblpeoplepayrolladjust_data, new TblpeoplePayrolladjustTransformer());
    }

    /**
     * Save Payroll Variable for People
     *
     * @param  mixed $request
     *
     * @return void
     */
    public function store(Store $request)
    {
        $this->AdjustmentDateValidation($request);
        $model=new TblpeoplePayrolladjust;
        $model->fill($request->all());
        if ($model->save()) {
            return $this->response->item($model, new TblpeoplePayrolladjustTransformer());
        } else {
            return response()->json(['error' => 'Error occurred while saving people payroll adjust.'], 422);
        }
    }
 
    /**
     * Update Payroll Variable 
     *
     * @param  mixed $request
     * @param  mixed $tblpeoplepayrolladjust
     * @param  mixed $id
     *
     * @return void
     */
    public function update(Update $request,  TblpeoplePayrolladjust $tblpeoplepayrolladjust, $id )
    {
        $this->UpdateAdjustmentDateValidation($request);
        if ($tblpeoplepayrolladjust->where('ID', $id)->update($request->all())) {
            $tblpeoplepayrolladjust_data = $tblpeoplepayrolladjust->where('ID', $id)->first();
            return $this->response->item($tblpeoplepayrolladjust_data, new TblpeoplePayrolladjustTransformer());
        } else {
            return response()->json(['error' => 'Error occurred while saving people payroll adjust.'], 422);
        } 
    }

    /**
     * Soft Delete Payroll Variable
     *
     * @param  mixed $request
     * @param  mixed $tblpeoplepayrolladjust
     *
     * @return void
     */
    public function destroy(Destroy $request, $tblpeoplepayrolladjust)
    {
        $tblpeoplepayrolladjust = TblpeoplePayrolladjust::findOrFail($tblpeoplepayrolladjust);

        if ($tblpeoplepayrolladjust->delete()) {
            return $this->response->array(['status' => 200, 'message' => 'TblpeoplePayrolladjust successfully deleted']);
        } else {
            return response()->json(['error' => 'Error occurred while deleting people payroll adjust.'], 422);
        }
    }

    /**
     * Validate Payroll Start/End date not conflict
     *
     * @param  mixed $request
     *
     * @return void
     */
    private function AdjustmentDateValidation($request){
        $DateEffectiveStart = $request->DateEffectiveStart;
        $DateEffectiveEnd = $request->DateEffectiveEnd;
       // dd($request->PeopleID);
        $query = TblpeoplePayrolladjust::where(function ($query1) use($DateEffectiveStart,$DateEffectiveEnd){
                                            $query1->where('DateEffectiveStart','<=',$DateEffectiveStart)
                                                ->where('DateEffectiveEnd','>=',$DateEffectiveEnd);
                                        })->orWhere(function ($query2) use($DateEffectiveStart,$DateEffectiveEnd){
                                            $query2->where('DateEffectiveStart','<=',$DateEffectiveStart)
                                                ->where('DateEffectiveEnd','>=',$DateEffectiveStart);
                                        })->orWhere(function ($query3) use($DateEffectiveStart,$DateEffectiveEnd){
                                            $query3->whereBetween('DateEffectiveStart',array($DateEffectiveStart,$DateEffectiveEnd))
                                                ->orWhereBetween('DateEffectiveEnd',array($DateEffectiveStart,$DateEffectiveEnd));
                                        })->get()->pluck('PeopleID')->toArray();

                                       
        if(empty($query)){
            return true;
        }else{
            if(in_array($request->PeopleID ,$query)){
                return response()->json(['error' => 'Date already exists, Please change date period.'], 422);
            }else{
                return true; 
            }
        }
    }

    /**
     * Validate Payroll Start/End date not conflict exclude updated id
     *
     * @param  mixed $request
     *
     * @return void
     */
    private function UpdateAdjustmentDateValidation($request){
        $DateEffectiveStart = $request->DateEffectiveStart;
        $DateEffectiveEnd = $request->DateEffectiveEnd;
        $query = TblpeoplePayrolladjust::where(function ($query) use($DateEffectiveStart,$DateEffectiveEnd){
                                            $query->where('DateEffectiveStart','<=',$DateEffectiveStart)
                                                ->where('DateEffectiveEnd','>=',$DateEffectiveEnd);
                                        })->orWhere(function ($query) use($DateEffectiveStart,$DateEffectiveEnd){
                                            $query->where('DateEffectiveStart','<=',$DateEffectiveStart)
                                                ->where('DateEffectiveEnd','>=',$DateEffectiveStart);
                                        })->orWhere(function ($query) use($DateEffectiveStart,$DateEffectiveEnd){
                                            $query->whereBetween('DateEffectiveStart',array($DateEffectiveStart,$DateEffectiveEnd))
                                                ->orWhereBetween('DateEffectiveEnd',array($DateEffectiveStart,$DateEffectiveEnd));
                                        })->get()->except([$request->ID])->pluck('PeopleID')->toArray();
                    
        if(empty($query)){
            return true;
        }else{
            if(in_array($request->PeopleID ,$query)){
                return response()->json(['error' => 'Date already exists, Please change date period.'], 422);
            }else{
                return true;
            }
        }
    }

    

}
