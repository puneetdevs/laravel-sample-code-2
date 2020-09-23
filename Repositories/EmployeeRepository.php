<?php

namespace App\Repositories;


use DB;
use App\Exceptions\Handler;
use App\Repositories\BaseRepository;
use App\Models\People;
use App\Models\DataFileSettings;
use DateTime;
use Carbon\Carbon;
use App\User;
use Auth;
use App\Models\tblPeopleTraining;
use App\Models\PeopleDocument;
use App\Models\PeopleSkillEvaluation;
use App\Models\PeopleEvaluation;
use App\Models\PeopleNotAvailabilities;
use App\Models\EmployeeCounter;
use App\Models\File;

/**
 * Class NotesRepository.
 */
class EmployeeRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return People::class;
    }

    /**
     * @param $email
     *
     * @return User
    */
    public function getNotesByType($object_type, $parent_id){
       return $this->model
       ->select('ID', 'Note', 'NoteDate','AddedBy')
        ->with(['addedBy'=>function($q){
            $q->select('id', 'fullname');
        }])
       ->where(['ParentCode'=>$object_type, 'ParentID'=>$parent_id])
       ->get();
    }

    public function create(array $data)
    { 
        /* Create User */
        $data['user_id'] = $this->createUser($data);
        $data['EmployeeNumber'] = $data['emp_code'].'-'.$this->getEmployeeNumber();
        return DB::transaction(function () use ($data) {
        $people = parent::create($data);
            if ($people) {
               $people->id = $people->PeopleID;
               unset($people->PeopleID);
               $this->updateEmployeeNumber();
               return $people;
            }
            throw new GeneralException('Oops! Something went wrong while creating People.');
        });
        
    }

    protected function createUser(array $input){
        $input['img'] = '/user.png';
        $input['email'] = $input['Email'];
        $input['is_active'] = 0;
        $input['fullname'] = $input['FirstName'].' '.$input['LastName'];
        $input['username'] = $input['Email'];
        $input['region_id'] = Auth::user()->region_id;
        $input['role_id'] = \App\Role::select('id')->where('slug', 'employee')->first()->id;
        if( isset($input['image']) ) {
            $input['img'] = trim($input['image']);
        }

        $user = User::create($input);
        $user_id = $user->id;
        return $user_id;
    }

    public function updateUser(array $input_data, $employee){
      $user = User::Find($employee->user_id);
      $user->email = $input_data['Email'];
      $user->fullname = $input_data['FirstName'].' '.$input_data['LastName'];
      if( isset($input_data['image']) ) {
        $user->img = trim($input_data['image']);
      }
      $user->save();
    }
    public function getProfileImage($userId){
        $user = User::select('img')->where('id', $userId);
        return $user->img;
    }
    public function deleteEmployeeImage($user_id){
        $user = User::Find($user_id);
        $user->img = trim('/user.png');
        return $user->save();
    }

    public function createTraining(array $data, $employee_id)
    { 
        /* Create User */
        $training = new tblPeopleTraining;
        $training->people_id = $employee_id;
        $training->course_id = $data['course_id'];
        $training->completed = $data['completed'];
        $training->completed_date = isset($data['completed_date']) ? $data['completed_date'] : NULL;
        $training->expire_date = isset($data['expire_date']) ? $data['expire_date'] : NULL;
        $training->certificate_number = isset($data['certificate_number']) ? $data['certificate_number'] : NULL;
        $training->file_id = isset($data['file_id']) ? $data['file_id'] : NULL;
        $training->save();
        return $training;
    }

    public function createApplicationTraining(array $data)
    { 
        /* Create User */
        $training = new tblPeopleTraining;
        $training->course_id = $data['course_id'];
        $training->completed = $data['completed'];
        isset($data['application_id']) && !empty($data['application_id']) ? $training->people_id = $data['application_id'] :'' ;
        $training->completed_date = isset($data['completed_date']) ? $data['completed_date'] : NULL;
        $training->expire_date = isset($data['expire_date']) ? $data['expire_date'] : NULL;
        $training->certificate_number = isset($data['certificate_number']) ? $data['certificate_number'] : NULL;
        $training->file_id = isset($data['file_id']) ? $data['file_id'] : NULL;
        $training->save();
        $trainings = tblPeopleTraining::with('CourseDetails')->where('id', $training->id)->first();
        return $trainings;
    }

    
    public function getAllTrainings($employee_id){
        $trainings = tblPeopleTraining::with('FileDetails')->where('people_id', $employee_id);
        return $trainings;
    }

    public function getAllEmpTrainings($columns_search, $request){
        $trainings = tblPeopleTraining::with('FileDetails');
        $trainings->whereHas('people',function($query) use($columns_search, $request){            
            $query->where('region_id', Auth::user()->region_id);
            /****** Search *******/
            if($request->has('q')){
                foreach($columns_search as $column){
                    $query->orWhere($column, 'LIKE', '%' . $request->q . '%');
                }
            }
        });
        return $trainings;
    }

    public function SingleTraining($employee_id, $id){
        $trainings = tblPeopleTraining::with('FileDetails')
        ->where([
            'people_id'=>$employee_id,
            'id'=>$id
        ])->first();
        return $trainings;
    }

    public function deleteTraining($employee_id, $id){
       
        $delete_file = tblPeopleTraining::with('FileDetails')
                                        ->where(['people_id'=>$employee_id,'id'=>$id])->first();
        if($delete_file){
            File::where('id',$delete_file->file_id)->delete();
            tblPeopleTraining::where('id',$delete_file->id)->delete();
        }
    }

    public function updateTraining($training_id, $employee_id, $update_data){
        return tblPeopleTraining::with('FileDetails')
        ->where([
            'people_id'=>$employee_id,
            'id'=>$training_id
        ])->update($update_data);
    }

    public function createDocument(array $data, $employee_id)
    { 
        /* Create Document */
        $document = new PeopleDocument;
        $document->people_id = $employee_id;
        $document->title = $data['title'];
        $document->user_type = 'Employee';
        $document->discription = isset($data['description']) ? $data['description'] : NULL;
        $document->file_id = isset($data['file_id']) ? $data['file_id'] : NULL;
        $document->save();
        return $document;
    }

    public function createApplicationDocument(array $data)
    { 
        /* Create Document */
        $document = new PeopleDocument;
        $document->title = $data['title'];
        $document->user_type = 'Application';
        isset($data['application_id']) && !empty($data['application_id']) ? $document->people_id = $data['application_id'] :'' ;
        $document->discription = isset($data['description']) ? $data['description'] : NULL;
        $document->file_id = isset($data['file_id']) ? $data['file_id'] : NULL;
        $document->save();
        return $document;
    }

    public function getAllDocuments($employee_id, $type){
        $trainings = PeopleDocument::with('FileDetails')->where('people_id', $employee_id)->where('user_type',$type);
        return $trainings;
    }

    public function SingleDocument($employee_id, $id){
        $document = PeopleDocument::with('FileDetails')
        ->where([
            'people_id'=>$employee_id,
            'id'=>$id
        ])->first();
        return $document;
    }

    public function deleteDocument($employee_id, $id){
        return PeopleDocument::where([
            'people_id'=>$employee_id,
            'id'=>$id
        ])->delete();
    }

    public function updateDocument($employee_id, $document_id, $update_data){
        return PeopleDocument::where([
            'people_id'=>$employee_id,
            'id'=>$document_id
        ])->update($update_data);
    }


    public function createSkillEvaluation(array $data, $employee_id)
    { 
        $skillEvaluation = new PeopleSkillEvaluation;
        $skillEvaluation->PeopleID = $employee_id;
        $skillEvaluation->SkillEvaluationID = $data['SkillEvaluationID'];
        $skillEvaluation->Evaluation = $data['Evaluation'];
        $skillEvaluation->save();
        return $skillEvaluation;
    }

    public function getSkillEvaluation($employee_id){
        $skills = PeopleSkillEvaluation::where('PeopleID', $employee_id);
        return $skills;
    }
    public function SingleSkillEvaluation($employee_id, $id){
        $document =  PeopleSkillEvaluation::where([
            'PeopleID'=>$employee_id,
            'ID'=>$id
        ])->first();
        return $document;
    }

    public function deleteSkillEvaluation($employee_id, $id){
        return PeopleSkillEvaluation::where([
            'PeopleID'=>$employee_id,
            'ID'=>$id
        ])->delete();
    }

    public function updateSkill($employee_id, $id, $update_data){
        return PeopleSkillEvaluation::where([
            'PeopleID'=>$employee_id,
            'ID'=>$id
        ])->update($update_data);
    }

    public function createPeopeleEvaluation(array $data, $employee_id)
    { 
        $data['PeopleID'] = $employee_id;
        $evaluations = PeopleEvaluation::create($data);
        if ($evaluations) {
            $evaluations->PeopleID = $employee_id;
            return $evaluations;
        }
        return false;
    }

    public function getEvaluations($employee_id){
        $evaluations = PeopleEvaluation::where('PeopleID', $employee_id);
        return $evaluations;
    }

    public function SinglePeopleEvaluation($employee_id, $id){
        $evaluation =  PeopleEvaluation::where([
            'PeopleID'=>$employee_id,
            'ID'=>$id
        ])->first();
        return $evaluation;
    }

    public function deletePeopleEvaluation($employee_id, $id){
        return PeopleEvaluation::where([
            'PeopleID'=>$employee_id,
            'ID'=>$id
        ])->delete();
    }

    public function updatePeopleEvaluation($employee_id, $id, $update_data){
        return PeopleEvaluation::where([
            'PeopleID'=>$employee_id,
            'ID'=>$id
        ])->update($update_data);
    }

    public function insertPeopeleNotAvailablities(array $data, $employee_id)
    { 
        $inserted_data = $this->get_week_dates_range($data,$employee_id);
        $notAvailablities = PeopleNotAvailabilities::insert($inserted_data);
        if ($notAvailablities) {
            return $notAvailablities;
        }
        return false;
    }

    private function get_week_dates_range($data, $employee_id){
        $startDate = new DateTime($data['StartDate']);
        $endDate = new DateTime($data['EndDate']);
        $days = array();
        $week_day_collect = $data['Days']; // 0 = sunday, 6 = saturday 
        $i = 0;
        while ($startDate <= $endDate) {
            if(isset($data['Days']) && !empty($data['Days'])){
                if ( in_array($startDate->format('w'), $week_day_collect) ) {
                    $days[$i]['StartDate'] = $startDate->format('Y-m-d');
                    $days[$i]['EndDate'] = $startDate->format('Y-m-d');
                    $days[$i]['FromTime'] = $data['FromTime'];
                    $days[$i]['ToTime'] = $data['ToTime'];
                    $days[$i]['Days'] = implode(",",$data['Days']);
                    $days[$i]['Reason'] = $data['Reason'];
                    $days[$i]['peopleId'] = $employee_id;
                    $days[$i]['created_at'] = date('Y-m-d');
                    $days[$i]['updated_at'] = date('Y-m-d');
                    $days[$i]['created_by'] = Auth::user()->id;
                }
            }else{
                $days[$i]['StartDate'] = $startDate->format('Y-m-d');
                $days[$i]['EndDate'] = $startDate->format('Y-m-d');
                $days[$i]['FromTime'] = $data['FromTime'];
                $days[$i]['ToTime'] = $data['ToTime'];
                $days[$i]['Days'] = implode(",",$data['Days']);
                $days[$i]['Reason'] = $data['Reason'];
                $days[$i]['peopleId'] = $employee_id;
                $days[$i]['created_at'] = date('Y-m-d');
                $days[$i]['updated_at'] = date('Y-m-d');
                $days[$i]['created_by'] = Auth::user()->id; 
            }
            
            $startDate->modify('+1 day');
            $i++;
        }
        return $days;
    }

    public function getPeopeleNotAvailablities($employee_id){
        $notAvailablities = PeopleNotAvailabilities::where('peopleId', $employee_id);
        return $notAvailablities;
    }

    public function SingleNotAvailablity($employee_id, $id){
        $notAvailablity =  PeopleNotAvailabilities::where([
            'peopleId'=>$employee_id,
            'ID'=>$id
        ])->first();
        return $notAvailablity;
    }

    public function deletePeopleUnavailability($employee_id, $id){
        return PeopleNotAvailabilities::where([
            'peopleId'=>$employee_id,
            'ID'=>$id
        ])->delete();
    }

    public function updatePeopleUnavailability($employee_id, $id, $update_data){
        return PeopleNotAvailabilities::where([
            'peopleId'=>$employee_id,
            'ID'=>$id
        ])->update($update_data);
    }

    function getEmployeeNumber() {
        $emp_counter = EmployeeCounter::select('Value')->first();
        if($emp_counter){
            return $emp_counter->Value;
        }
        
        $value = 1;
        $region_setting = DataFileSettings::where('region_id',Auth::user()->region_id)->first();
        if($region_setting){
            $value = $region_setting->EmployeeCounter;
        }
        EmployeeCounter::insert(['Value'=>$value,'region_id'=> Auth::user()->region_id]);
        return $value;
    }

    function updateEmployeeNumber(){
        $last_number = $this->getEmployeeNumber();
        $new_Number = $last_number+1;
        EmployeeCounter::where([])->update(['Value'=>$new_Number]);
        return $new_Number;
    }
}
