<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\API\UsersController;
use App\Http\Controllers\Api\ApiController;
use App\Models\EmployeeApplication;
use App\Models\Region;
use App\Models\PeopleDocument;
use App\Models\File;
use App\User;
use App\Models\tblPeopleTraining;
use App\Transformers\EmployeeApplicationTransformer;
use App\Transformers\DocumentTransformer;
use App\Transformers\EventTransformer\NoteDetailTransformer;
use App\Transformers\TrainingTransformer;
use App\Http\Requests\Api\EmployeeApplications\Index;
use App\Http\Requests\Api\EmployeeApplications\Show;
use App\Http\Requests\Api\EmployeeApplications\Create;
use App\Http\Requests\Api\EmployeeApplications\Store;
use App\Http\Requests\Api\EmployeeApplications\Edit;
use App\Http\Requests\Api\EmployeeApplications\Update;
use App\Http\Requests\Api\EmployeeApplications\Destroy;
use App\Http\Requests\Api\EmployeeApplications\AcceptReject;
use App\Http\Requests\Api\Tblevents\Note;
use App\Repositories\EmployeeRepository;
use App\Repositories\UserRepository;
use App\Repositories\NotesRepository;
use Auth;
use DB;


/**
 * EmployeeApplication
 *
 * @Resource("EmployeeApplication", uri="/employee_applications")
 */

class EmployeeApplicationController extends ApiController
{

    public function __construct(EmployeeRepository $employeeRepository,
    UserRepository $userRepository, NotesRepository $notesRepository)
    {
        $this->userRepository = $userRepository;
        $this->employeeRepository = $employeeRepository;
        $this->notesRepository = $notesRepository;
    }
    
    /**
     * index
     *
     * @param  mixed $request
     *
     * @return void
     */
    public function index(Index $request)
    {
        if($request->has('type')){
            $perPage = 10;
            if($request->has('per_page')){
                $perPage = $request->per_page;
            }
            
            $data = EmployeeApplication::where('Status',$request->type);

            /****** Search *******/
            $columns_search = ['FirstName', 'LastName', 'Email', 'City', 'Postal_code', 'Country'];
            if($request->has('q')){
                $data->where(function ($query) use($columns_search, $request) {
                    foreach($columns_search as $column) {
                        $query->orWhere($column, 'LIKE', '%' . $request->q . '%');
                    }
                    $query->orWhere(DB::raw("CONCAT(FirstName,' ',LastName)"), 'LIKE', '%' . $request->q . '%');
                });
            }

            /****** application submited date filter *******/
            if($request->has('submit_start') && $request->has('submit_end')){
                $data->whereBetween('created_at', [$request->submit_start, $request->submit_end]);
            }
            /****** application action date filter *******/
            if($request->has('action_start') && $request->has('action_end')){
                $data->whereBetween('action_date', [$request->action_start, $request->action_end]);
            }

            $applications = $data->orderBy('created_at','desc')->paginate($perPage);;
            return $this->response->paginator($applications, new EmployeeApplicationTransformer());
        }
        return response()->json(['error' => 'Please send application type.'], 422);
    }

    /**
     * Get single Application data
     *
     * @param  mixed $request
     * @param  mixed $employeeapplication
     *
     * @return void
     */
    public function show(Show $request, $application_id)
    {
        #Single Application Query
        $employeeapplication = EmployeeApplication::where('id',$application_id)->first();
        return $this->response->item($employeeapplication, new EmployeeApplicationTransformer());
    }

    /**
     * store
     *
     * @param  mixed $request
     *
     * @return void
     */
    public function store(Store $request)
    {
        $requested_data = $request->all();
        $region_id = $this->getRegionId($requested_data);
        $requested_data['region_id'] = $region_id;
        $requested_data['Status'] = 0;

        $model=new EmployeeApplication;
        $model->fill($requested_data);
        if ($model->save()) {
           
            #update people in file table for document
            $this->updateDocRecord($model->id, $requested_data);
            
            #update people in file table for image
            $this->updateFileRecord($model->id, $requested_data);

            #update people in certificate table for
            $this->updateCertificateRecord($model->id, $requested_data);
            
            #Send email to Admin as notification for new application
            if(!empty($region_id)){
                $admin_emails = User::where('region_id', $region_id)->where('role_id',1)->get()->pluck('email')->toArray();
                if(!empty($admin_emails)){
                    $view_application_detail = getenv('FRONTEND_URL').'/applications/'.$model->id.'/view';
                    
                    try {
                        \Mail::send(['html' => 'email.new-application-notification'], array('link' => $view_application_detail), function ($message) use ($admin_emails) {
                            $message->to($admin_emails)->subject('New Application');
                        });
                    } catch (\Exception $e) {}
                }
            }
            return $this->response->item($model, new EmployeeApplicationTransformer());
        } else {
            return response()->json(['error' => 'Error occurred while saving EmployeeApplication.'], 422);
        }
    }
 
    public function update(Update $request, $application_id)
    {
        $application = EmployeeApplication::where('id',$application_id)->first();
        if($application){
            EmployeeApplication::where('id',$application_id)->update($request->all());
            $application = EmployeeApplication::where('id',$application_id)->first();
            return $this->response->item($application, new EmployeeApplicationTransformer());
        }
    }

    public function destroy(Destroy $request, $employeeapplication)
    {
        $employeeapplication = EmployeeApplication::findOrFail($employeeapplication);

        if ($employeeapplication->delete()) {
            return $this->response->array(['status' => 200, 'message' => 'EmployeeApplication successfully deleted']);
        } else {
            return response()->json(['error' => 'Error occurred while deleting EmployeeApplication.'], 422);
        }
    }

    private function getRegionId($requested_data){
        $region_id = '';
        $region = Region::where('code', $requested_data['Province'])->first();
        if($region){
            $region_id = $region->id;
        }
        return $region_id;
    }

    /**
     * Update Doc Record
     *
     * @param  mixed $application_id
     * @param  mixed $requested_data
     *
     * @return void
     */
    private function updateDocRecord($application_id, $requested_data){
        if(!empty($requested_data['Doc_ids'])){
            PeopleDocument::whereIn('id',$requested_data['Doc_ids'])
            ->where('user_type','Application')
            ->update(['people_id' => $application_id]);

            $people_doc = PeopleDocument::where('people_id',$application_id)
                    ->where('user_type','Application')->get()->pluck('file_id')->toArray();
            if(!empty($people_doc)){
                File::whereIn('id',$people_doc) ->update(['upload_by' => $application_id]);
            }
        }
        return true;
    }

    private function updateCertificateRecord($application_id, $requested_data){
        if(isset($requested_data['Certificate_ids']) && !empty($requested_data['Certificate_ids'])){
            tblPeopleTraining::whereIn('id',$requested_data['Certificate_ids'])
            ->update(['people_id' => $application_id]);

            $people_doc = tblPeopleTraining::where('people_id',$application_id)->get()->pluck('file_id')->toArray();
            if(!empty($people_doc)){
                File::whereIn('id',$people_doc) ->update(['upload_by' => $application_id]);
            }
        }
        return true;
    }

    /**
     * Update File Record
     *
     * @param  mixed $application_id
     * @param  mixed $requested_data
     *
     * @return void
     */
    private function updateFileRecord($application_id, $requested_data){
        if(isset($requested_data['image_id']) && !empty($requested_data['image_id']) && !empty($requested_data['ProfileImage'])){
            File::where('id',$requested_data['image_id'])
                    ->where('file_type','image')
                    ->where('user_type','Application')
                    ->update(['upload_by' => $application_id]);
        }
        return true;
    }

    /**
     * Application Accept Reject
     *
     * @param  mixed $request
     * @param  mixed $application_id
     *
     * @return void
     */
    public function applicationAcceptReject(AcceptReject $request, $application_id)
    {   
        #IF status is 1 and emp_code is not send then return error
        if($request->Status == '1'){
            if(!$request->has('emp_code') && empty($request->emp_code)){
                return response()->json(['error' => 'Please send emp_code.'], 422);
            }
        }
        #Get application with application_id
        $get_application = EmployeeApplication::where('id',$request->application_id)->first();
        if($get_application->Status == 0){
            $response = $this->changeStatus($request,$get_application);
            return response()->json($response, 200);
        }else{
            if($get_application->Status == 1){
                return response()->json(['error' => 'Application already accepted.'], 422);
            }else if($get_application->Status == 2){
                return response()->json(['error' => 'Application already rejected.'], 422);
            }
        }
    }

    /**
     * Change Application Status
     *
     * @param  mixed $request
     * @param  mixed $get_application
     *
     * @return void
     */
    private function changeStatus($request, $get_application){
        $input_data = $get_application->toArray();
        #update status 
        if(EmployeeApplication::where('id', $request->application_id)->update(['Status' => $request->Status])){
            //Accept Application
            if($request->Status == 1){
                $data = $this->formatedData($request, $input_data);
                $employee = $this->employeeRepository->create($data);
                if ($employee) {
                    /* **** Send Password Email *** */
                    $user = \App\Models\SysUser::where('username', $input_data['Email'])->first();
                    $UsersController = new UsersController($this->userRepository );
                    $UsersController->SendForgotPasswordEmail($user, $request, true);
                    $this->copyDocumentToEmployee($request->application_id, $employee['id']);
                    $this->copyNoteToEmployee($request->application_id, $employee['id']);
                   // $this->copyCertificateToEmployee($request->application_id, $employee['id']);
                    EmployeeApplication::where('id', $request->application_id)->update(['EmployeeID' => $employee['id'], 'action_date' => date('Y-m-d H:i:s')]);
                    return array('data' => $employee, 'message' => 'Application accepted successfully.');
                }   

            //Reject Application
            }else{
                $reason = '';
                if($request->has('reason')){
                    EmployeeApplication::where('id', $request->application_id)->update(['rejected_reason' => $request->reason]);
                    $reason = $request->reason;
                }
                EmployeeApplication::where('id', $request->application_id)->update(['action_date' => date('Y-m-d H:i:s')]);
                $email = $input_data['Email'];
                try {
                    \Mail::send(['html' => 'email.application-reject'], array('reason' => $reason, 'FirstName' => $input_data['FirstName']), function ($message) use ($email) {
                        $message->to($email)->subject('Application Under Review');
                    });
                } catch (\Exception $e) {}
                return array('data' => [], 'message' => 'Application rejected and email notification has been sent.');
            }
        }
        return response()->json(['error' => 'Status not updated, Please try again.'], 422);
    }

    /**
     * Copy Document To Employee
     *
     * @param  mixed $application_id
     * @param  mixed $employee_id
     *
     * @return void
     */
    private function copyDocumentToEmployee($application_id, $employee_id){

        $file = File::where('upload_by',$application_id)
            ->where('user_type','Application')->get()->toArray();
        if(!empty($file)){
            foreach($file as $key=>$files){
                $file_data[$key]['upload_by'] = $employee_id;
                $file_data[$key]['path'] = $files['path'];
                $file_data[$key]['user_type'] = 'Employee';
                $file_data[$key]['name'] = $files['name'];
                $file_data[$key]['file_type'] = $files['file_type'];
                $file_data[$key]['object_type'] = $files['object_type'];
                $file_data[$key]['object_id'] = $employee_id;
                $file_data[$key]['created_at'] = date('Y-m-d H:i:s');
                $file_data[$key]['updated_at'] = date('Y-m-d H:i:s');
            }
            File::insert($file_data);
        }
        
        $document = PeopleDocument::where('people_id',$application_id)
            ->where('user_type','Application')->get()->toArray();
        if(!empty($document)){
            foreach($document as $key=>$doc){
                $document[$key]['id'] = '';
                $document[$key]['people_id'] = $employee_id;
                $document[$key]['title'] = $doc['title'];
                $document[$key]['user_type'] = 'Employee';
                $document[$key]['discription'] =  isset($doc['description']) ? $doc['description'] : NULL;;
                $document[$key]['file_id'] = $doc['file_id'];
                $document[$key]['created_at'] = date('Y-m-d H:i:s');
                $document[$key]['updated_at'] = date('Y-m-d H:i:s');
            }
            PeopleDocument::insert($document);
        }
        
        return true;
    }

    private function  copyCertificateToEmployee($application_id, $employee_id){
        $traning = tblPeopleTraining::where('people_id',$application_id)->get()->toArray();
        if(!empty($traning)){
            foreach($traning as $key=>$tranings){
                $tranings_data[$key]['id'] = '';
                $tranings_data[$key]['course_id'] = $tranings['course_id'];
                $tranings_data[$key]['people_id'] = $employee_id;
                $tranings_data[$key]['completed'] = $tranings['completed'];
                $tranings_data[$key]['completed_date'] = $tranings['completed_date'];
                $tranings_data[$key]['expire_date'] = $tranings['expire_date'];
                $tranings_data[$key]['file_id'] = $tranings['file_id'];
                $tranings_data[$key]['created_at'] = date('Y-m-d H:i:s');
                $tranings_data[$key]['updated_at'] = date('Y-m-d H:i:s');
            }
            tblPeopleTraining::insert($tranings_data);
        }
    }

    private function copyNoteToEmployee($application_id, $employee_id){
        $note = $this->notesRepository->getNotesByType('APPLICATION', $application_id);
        if(!empty($note)){
            foreach($note as $key=>$notes){
                $notes_data['Note'] = $notes['Note'];
                $notes_data['ParentCode'] = 'Employee';
                $notes_data['ParentID'] =  $employee_id;
                $notes_data['AddedBy'] =  Auth::user()->id;
                $this->notesRepository->create($notes_data);
            }
        }
        return true;
    }
    
    
    private function formatedData($request, $input_data){
        $data = array();
        $data['emp_code'] = $request->emp_code;
        $data['Suite'] = $input_data['Suite'];
        $data['FirstName'] = $input_data['FirstName'];
        $data['LastName'] = $input_data['LastName'];
        $data['Initial'] = $input_data['Initial'];
        $data['AddressLine1'] = $input_data['Address1'];
        $data['AddressLine2'] = $input_data['Address2'];
        $data['City'] = $input_data['City'];
        $data['Prov'] = $input_data['Province'];
        $data['Postal'] = $input_data['Postal_code'];
        $data['Country'] = $input_data['Country'];
        $data['Region'] = $input_data['region_id'];
        $data['WorkExt'] = $input_data['Country_code'];
        $data['Cell'] = $input_data['Cell'];
        $data['Email'] = $input_data['Email'];
        $data['DateOfBirth'] = $input_data['DateOfBirth'];
        $data['image'] = $input_data['ProfileImage'];
        $data['EmployeeNumber'] = '';
        $data['user_id'] = '';
        return $data;
    }

    
    /**
     * Get Application Documents
     *
     * @param  mixed $request
     * @param  mixed $application_id
     *
     * @return void
     */
    public function getApplicationDocuments(Request $request, $application_id) {
        $application =  EmployeeApplication::where('id', $application_id)->first();
        if($application && is_null($application)==false ){
            $perPage = 10;
            if($request->has('per_page')){
                $perPage = $request->per_page;
            }
            $data = $this->employeeRepository->getAllDocuments($application_id, 'Application');
            $data =  $data->paginate($perPage);
            return $this->response->paginator($data, new DocumentTransformer());
        } else {
            return response()->json(['error' => 'Unable to Find employee.'], 422);
        }
    }

    /**
     * Post Application Note
     *
     * @param  mixed $request
     * @param  mixed $application_id
     *
     * @return void
     */
    public function postNote(Note $request, $application_id){
        $application =  EmployeeApplication::where('id', $application_id)->first();
        if($application){
            $notes_data['Note'] = $request->Note;
            $notes_data['ParentCode'] = 'APPLICATION';
            $notes_data['ParentID'] =  $application_id;
            $notes_data['AddedBy'] =  Auth::user()->id;
            $note =  $this->notesRepository->create($notes_data);
            if ($note) {
                    return response()->json(['data' => 'Note has been added successfully.'], 200);
            }
            return response()->json(['error' => 'Note has been added already for this application.'], 422);
        }
        return response()->json(['error' => 'Application not found.'], 422); 
    }

    /**
     * Get Application Notes
     *
     * @param  mixed $request
     * @param  mixed $application_id
     *
     * @return void
     */
    public function getNotes(Request $request, $application_id){
        $application =  EmployeeApplication::where('id', $application_id)->first();
        if($application){
            $perPage = 10;
            if($request->has('per_page')){
                $perPage = $request->per_page;
            }
            $notes =  $this->notesRepository->getNotesApplicationByType('APPLICATION', $application_id)->paginate($perPage);
            return $this->response->paginator($notes, new NoteDetailTransformer());
        }
        return response()->json(['error' => 'Application not found.'], 422); 
    }

    /* Get All Training Data By Application */
    public function getApplicationTrainings(Request $request, $application) {
        $application_id = $application;
        $application =  EmployeeApplication::where('id', $application_id)->first();
        if($application && is_null($application)==false ){
            
            $perPage = 10;
            if($request->has('per_page')){
                $perPage = $request->per_page;
            }
            $data = $this->employeeRepository->getAllTrainings($application_id);
            
          
            $data =  $data->paginate($perPage);
            return $this->response->paginator($data, new TrainingTransformer());
        } else {
            return response()->json(['error' => 'Unable to Find application.'], 422);
        }
    }


}
