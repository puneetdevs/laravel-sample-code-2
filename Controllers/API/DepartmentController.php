<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use App\Models\Department;
use App\Models\TbleventsShifthour;
use App\Transformers\DepartmentTransformer;
use App\Http\Requests\Api\Departments\Index;
use App\Http\Requests\Api\Departments\Show;
use App\Http\Requests\Api\Departments\Create;
use App\Http\Requests\Api\Departments\Store;
use App\Http\Requests\Api\Departments\Update;
use App\Http\Requests\Api\Departments\Destroy;
use App\Http\Requests\Api\Departments\ChangeStatus;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

/**
 * Department
 *
 * @Resource("Department", uri="/departments")
 */

class DepartmentController extends ApiController
{
    
    public function index(Index $request)
    {
        $perPage = 10;
        $offset = '0';
        if($request->has('per_page')){
            $perPage = $request->per_page;
        }
        $columns_search = ['Departments'];

        $data = Department::where([]);

        /****** Status *******/
        if($request->has('status')){
            $data->where('status',$request->status);
        }

        /****** Search *******/
        if($request->has('q')){
            foreach($columns_search as $column){
                $data->orWhere($column, 'LIKE', '%' . $request->q . '%');
            }
        }
       $data = $data->orderBy('ID', 'desc');
       $data =  $data->paginate($perPage);
       return $this->response->paginator($data, new DepartmentTransformer());
    }

    public function show(Show $request, $department)
    {
        $department = Department::find($department);
        if( $department && is_null($department)==false ){
            return $this->response->item($department, new DepartmentTransformer());
        }
        return $this->response->errorNotFound('Department Not Found', 404);
    }

    public function store(Create $request)
    {
        $model=new Department;
        $model->Departments = $request->name;
        if ($model->save()) {
            return $this->response->item($model, new DepartmentTransformer());
        } else {
            return response()->json(['error' => 'Error occurred while saving Department.'], 422);
        }
    }
 
    public function update(Update $request, $department)
    {
    
        $department = Department::findOrFail($department);
        $department->fill($request->all());
        $department->Departments = $request->name;

        if ($department->save()) {
            return $this->response->item($department, new DepartmentTransformer());
        } else {
            return response()->json(['error' => 'Error occurred while update Department.'], 422);
        }
    }

    public function destroy(Destroy $request, $department)
    {
        $department = Department::findOrFail($department);
        if ($department->delete()) {
            return $this->response->array(['status' => 200, 'message' => 'Department successfully deleted']);
        } else {
            return response()->json(['error' => 'Error occurred while deleting Department.'], 422);
        }
    }

    public function changeDepartmentStatus(ChangeStatus $request, $department)
    {
        $department = Department::findOrFail($department);
        if ($department->update(['status' => $request->status])) {
            return $this->response->array(['status' => 200, 'message' => 'Department status changed successfully']);
        } else {
            return response()->json(['error' => 'Error occurred while change status of Department.'], 422);
        }
    }


    /**
     * getEventDepartment
     *
     * @param  mixed $request
     *
     * @return void
     */
    public function getEventDepartment(Request $request)
    {
        if($request->has('date_ids') && $request->date_ids != '' && $request->has('event_id')){
            $date_ids =  explode(",",$request->date_ids);
            $date_query =  TbleventsShifthour::where('EID',$request->event_id)
                                            ->whereIn('DID',$date_ids)
                                            ->with([
                                                'department','date',
                                                'people' => function($people_query){
                                                    $people_query->select('PeopleID', 'FirstName', 'LastName');
                                                },'event' => function($event_query){
                                                    $event_query->select('EID', 'EventName');
                                                }]);
            $date_query->whereHas('people'); 
            $date_query->whereHas('date');
            $date_query->whereHas('event');
            $dept_data = $date_query->whereHas('department')->get()->toArray();
            if(!empty($dept_data)){
                $department_data['event']['dates'] = $this->formatDepartmentData($dept_data);
                $department_data['event']['EID'] = $dept_data[0]['event']['EID'];
                $department_data['event']['EventName'] = $dept_data[0]['event']['EventName'];
                return $this->response->array(['status' => 200, 'data' => $department_data]);
            }
            return response()->json(['error' => 'Shift not perpare yet.'], 422);
        }
        return response()->json(['error' => 'Please select date first.'], 422);
    }
    private function formatDepartmentData($dept_data){
        $department = array();
        foreach($dept_data as $key=>$dept){
            
            if(in_array($dept['DID'], array_column($department, 'DID'))){
                $search_date_key = array_search($dept['DID'], array_column($department, 'DID'));
                if(in_array($dept['Department'], array_column($department[$search_date_key]['dept'], 'ID'))){
                    $search_key = array_search($dept['Department'], array_column($department[$search_date_key]['dept'], 'ID'));
                    $people_key = count($department[$search_date_key]['dept'][$search_key]['people']);
                   
                    $department[$search_date_key]['dept'][$search_key]['people'][$people_key]['PeopleID'] = $dept['people']['PeopleID'];
                    $department[$search_date_key]['dept'][$search_key]['people'][$people_key]['FirstName'] = $dept['people']['FirstName'];
                    $department[$search_date_key]['dept'][$search_key]['people'][$people_key]['LastName'] = $dept['people']['LastName'];
                }else{
                    $dept_key = count($department[$search_date_key]['dept']);
                    $department[$search_date_key]['dept'][$dept_key]['ID'] = $dept['department']['ID'];
                    $department[$search_date_key]['dept'][$dept_key]['Departments'] = $dept['department']['Departments'];
                    $department[$search_date_key]['dept'][$dept_key]['people'][0]['PeopleID'] = $dept['people']['PeopleID'];
                    $department[$search_date_key]['dept'][$dept_key]['people'][0]['FirstName'] = $dept['people']['FirstName'];
                    $department[$search_date_key]['dept'][$dept_key]['people'][0]['LastName'] = $dept['people']['LastName'];
                }
            }else{
                $date_key = count($department);
                $department[$date_key]['DID'] = $dept['date']['DID'];
                $department[$date_key]['Eventdate'] = $dept['date']['Eventdate'];
                $department[$date_key]['EventDescription'] = $dept['date']['EventDescription'];
                $department[$date_key]['dept'][0]['ID'] = $dept['department']['ID'];
                $department[$date_key]['dept'][0]['Departments'] = $dept['department']['Departments'];
                $department[$date_key]['dept'][0]['people'][0]['PeopleID'] = $dept['people']['PeopleID'];
                $department[$date_key]['dept'][0]['people'][0]['FirstName'] = $dept['people']['FirstName'];
                $department[$date_key]['dept'][0]['people'][0]['LastName'] = $dept['people']['LastName'];
            }
        }
        return $department;
    }

}
