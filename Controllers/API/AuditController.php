<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use App\Models\Audit;
use App\User;
use App\Transformers\AuditTransformer;


/**
 * audit
 *
 * @Resource("audit", uri="/audits")
 */

class AuditController extends ApiController
{
    
    public function index(Request $request)
    {
        $perPage = 10;
        if($request->has('per_page')){
            $perPage = $request->per_page;
        }
        $Audit = Audit::with(['user' =>function($q){
                                $q->select('id','fullname');
                            }]);
        #Date filter
        if($request->has('start_date') && $request->has('end_date')){
            $Audit->whereBetween('updated_at',[$request->start_date,$request->end_date]);
        }   
        #Action filter 
        if($request->has('action')){
            $Audit->where('event',$request->action);
        }
        #Type filter  
        if($request->has('type') ){
            $Audit->where('auditable_type','like','%'.$request->type.'%');
        }                  
                            
        $Audit = $Audit->orderBy('id','desc')->paginate($perPage)->toArray();
                            
        $final_record = array();
        if($Audit['total'] > 0){
            foreach($Audit['data'] as $key=>$audit_value){
                #Get updated model data here
                $updated_audit_model = array();
                $model_type = $audit_value['auditable_type'];
                #make auditable model name here
                $value1 = str_replace('App','',$audit_value['auditable_type']);
                $value2 = str_replace('\Models','',$value1);
                $type = substr($value2, 1);
                $type1 = str_replace('tbl','',substr($value2, 1));
                $type2 = str_replace('Tbl','',$type1);
                $audit_value['auditable_type'] = ucfirst($type2);
                if($audit_value['auditable_id'] && !empty($audit_value['auditable_id'])){
                    if($type == 'Tblevent'){
                        $updated_audit_model = $model_type::where('EID',$audit_value['auditable_id']);
                    }elseif($type == 'Tbleventdate'){
                        $updated_audit_model = $model_type::where('DID',$audit_value['auditable_id']);
                    }elseif($type == 'Position'){
                        $updated_audit_model = $model_type::where('PID',$audit_value['auditable_id']);
                    }elseif($type == 'Location'){
                        $updated_audit_model = $model_type::where('VID',$audit_value['auditable_id']);
                    }elseif($type == 'Configuration'){
                        $updated_audit_model = $model_type::where('CfgID',$audit_value['auditable_id']);
                    }elseif($type == 'People'){
                        $updated_audit_model = $model_type::where('PeopleID',$audit_value['auditable_id']);
                    }elseif($type == 'Skill'){
                        $updated_audit_model = $model_type::where('SkID',$audit_value['auditable_id']);
                    }else{
                        $updated_audit_model = $model_type::where('id',$audit_value['auditable_id'])
                        ->orWhere('ID',$audit_value['auditable_id']);
                    }
                    $updated_audit_model = $updated_audit_model->get()->toArray();
                }

                #add updated audit relational model data here
                $final_record[$key] = $audit_value;
                $final_record[$key]['updated_audit_model'] = $updated_audit_model;
            }
        }
        return $this->response->array(['data' => $final_record, 
                                        'current_page' => $Audit['current_page'],
                                        'first_page_url' => $Audit['first_page_url'],
                                        'last_page' => $Audit['last_page'],
                                        'last_page_url' => $Audit['last_page_url'],
                                        'next_page_url' => $Audit['next_page_url'],
                                        'path' => $Audit['path'],
                                        'per_page' => $Audit['per_page'],
                                        'prev_page_url' => $Audit['prev_page_url'],
                                        'to' => $Audit['to'],
                                        'total' => $Audit['total'],
                                    ]);
        
    }

    public function show(Request $request,$audit)
    {
        $audit = Audit::where('id',$audit)->first();
        return $this->response->item($audit, new AuditTransformer());
    }

    public function store(Request $request)
    {
        $model=new Audit;
        $model->fill($request->all());
        if ($model->save()) {
            return $this->response->item($model, new AuditTransformer());
        } else {
            return response()->json(['error' => 'Error occurred while saving audit'], 422);
        }
    }
 
    public function update(Request $request,  Audit $audit)
    {
        $audit->fill($request->all());

        if ($audit->save()) {
            return $this->response->item($audit, new AuditTransformer());
        } else {
            return response()->json(['error' => 'Error occurred while saving audit'], 422);
        }
    }

    public function destroy(Request $request, $audit)
    {
        $audit = Audit::findOrFail($audit);

        if ($audit->delete()) {
            return $this->response->array(['status' => 200, 'message' => 'audit successfully deleted']);
        } else {
            return response()->json(['error' => 'Error occurred while deleting audit'], 422);
        }
    }

    public function getModelList(Request $request){
        $model_list = ["BulkMessageTemplate","ChatGroup","Client","Configuration","ConfigurationRates",
        "DataFileSettings","Department","EmailTemplate","EmployeeCounterPrefix","EventSchedule",
        "ExternalNote","ExternalNoteDetail","File","HolidayList","Location","Note","OfferEmailTemplate",
        "Payrollcorrection","People","PeopleDocument","PeopleNotAvailabilities","PeopleSkillEvaluation",
        "Position","Region","Role","ShiftApproval","Skill","SysEmailTemplate","Tblevent","Tbleventdate",
        "TbleventsInvoicelineitem","TbleventsShifthour","Tblworkcategory","tblPeopleTraining"];
        return $this->response->array(['status' => 200, 'data' => $model_list]);
    }

}
