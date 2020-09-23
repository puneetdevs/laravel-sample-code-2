<?php

namespace App\Http\Controllers\Api;

use App\Helpers\AppHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\PasswordRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\SysAppConfig;
use App\Models\SysEmailTemplate;
use App\Models\SysUser;
use App\Models\TeamModule;
use App\Models\Tblevent;
use App\Models\People;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use App\Transformers\UserTransformer;

use App\Http\Requests\Api\Users\Index;
use App\Http\Requests\Api\Users\Show;
use App\Http\Requests\Api\Users\Store;
use App\Http\Requests\Api\Users\Update;
use App\Http\Requests\Api\Users\Destroy;

use Validator;
use App\Repositories\UserRepository;

class UsersController extends ApiController
{
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * login : Login user
     *
     * @param  mixed $request 
     *
     * @return json
     */
    public function login(LoginRequest $request)
    {
        try {
            $email = $request->get('username');
            $password = $request->get('password');

            if (Auth::attempt(['email' => $email, 'password' => $password])) {
                
                $user = Auth::user();
                User::where('id', $user->id)->update(['last_activity' => date('Y-m-d H:i:s')]);
                $people = People::where('user_id',$user->id)->first();
                
                $success['id'] = $user->id;
                $success['fullname'] = $user->fullname;
                $success['selected_team'] = $user->selected_team;
                $success['username'] = $user->username;
                $success['email'] = $user->email;
                $success['role'] = $user->role->slug; /* User Role slug */
                $success['region_id'] = $user->region_id;
                $success['PeopleID'] = ($people) ? $people->PeopleID : '';
                $img_path = $user->img;

                if (\File::exists(storage_path($user->img)) && $user->img != '' && $user->img != null) {
                    $success['profile_pic'] = Config::get('app.url')."/storage/".$img_path;
                } else {
                    $success['profile_pic'] = Config::get('app.url') . '/storage/user.png';
                }

                $success['token'] = $user->createToken('ginger')->accessToken;
               
                $user = $user->toArray();

                
                return response()->json(['success' => true, 'message' => 'Login successfully', 'data' => $success], 200);
            } else {
                return response()->json(['success' => false, 'message' => "Invalid Username or Password", 'data' => []], 401);
            }
        } catch (\Exception $e) {
            report($e);

            return response()->json(['success' => false, 'message' => $e->getMessage(), 'data' => []], 500);
        }
    }

    
    function SendForgotPasswordEmail($user, $request, $new_account=false ){
        
        if (!$user) {
            return response()->json(['success' => true, 'message' => 'Password reset instructions have been sent to the address if it exists in our records.', 'data' => []], 200);
        }
        $token = str_random(64);

        SysUser::where('id', $user->id)->update([
            'pwresetkey' => $token,
        ]);

        $headers = $request->headers->all();
        $front_url = (isset($headers['origin'])) ? $headers['origin'][0] : \Config::get('app.front_url');

        $password_reset_link = $front_url . '/auth/resetpassword/' . $token;
        if( $new_account ) {
            $subject = 'Registration';
            $email_template = SysEmailTemplate::where('tplname', 'User:New Password Request')->first();
        } else {
            $subject = 'Forgot Password Link';
            $email_template = SysEmailTemplate::where('tplname', 'Admin:Password Change Request')->first();
        }
        
        if ($email_template) {
           // $app_config_obj = SysAppConfig::where('team_id', $team_id)->where('created_by', $user->id)->where('setting', 'CompanyName')->first();
            $logo = Config::get('app.url').'/image/logo.png';
            $email_template['message'] = str_replace("{{business_name}}", 's', $email_template['message']);
            $email_template['message'] = str_replace("{{name}}", $user->fullname, $email_template['message']);
            $email_template['message'] = str_replace("{{username}}", $user->username, $email_template['message']);
            $email_template['message'] = str_replace("{{ip_address}}", \Request::ip(), $email_template['message']);
            $email_template['message'] = str_replace("{{password_reset_link}}", $password_reset_link, $email_template['message']);
            $email_template['message'] = str_replace("{{ logo }}", $logo , $email_template['message']);


            $message = $email_template['message'];
            
            $to = $user->email;
            try {
                $email =  \Mail::send([], [], function ($mail) use ($to, $subject, $message) {
                    $mail->to($to)
                        ->subject($subject)
                        ->setBody($message, 'text/html');
                });
            } catch (\Exception $e) {
                report($e);
                return response()->json(['success' => false, 'message' => $e->getMessage(), 'data' => []], 500);
            }
        } else {
            $data = [
                "password_reset_link" => $password_reset_link,
            ];

            try {
                \Mail::send(['html' => 'forgot-email'], $data, function ($message) use ($email) {
                    $message->to($email)->subject('Forgot Password Link');
                });
            } catch (\Exception $e) {
                report($e);
                return response()->json(['success' => false, 'message' => $e->getMessage(), 'data' => []], 500);
            }
            
        }
    }

    public function forgotPassword(PasswordRequest $request)
    {
        try {
            $request_data = $request->all();
            $email = $request_data['email'];
            $user = SysUser::where('email', $email)->first();
            if($user){
                $this->SendForgotPasswordEmail($user, $request);
                return response()->json(['success' => true, 'message' => 'Password reset instructions have been sent to the address if it exists in our records.', 'data' => []], 200);
            }
            return response()->json(['error' => 'User not found please try again.'], 422);
        } catch (\Exception $e) {
            report($e);
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'data' => []], 500);
        }
    }

    public function resetPassword(PasswordRequest $request)
    {
        try {
            $data = $request->all();

            $new_password = $data['new_password'];
            $token = $data['password_reset_token'];

            $status = SysUser::where('pwresetkey', $token)->update([
                'password' => bcrypt($new_password),
                'pwresetkey' => "",
            ]);

            if ($status == 0) {
                return response()->json(['success' => false, 'message' => 'This link has already been used once, please start another forgot password request if you wish to reset your password again.', 'data' => []], 200);
            }
            return response()->json(['success' => true, 'message' => 'Password reset successfully', 'data' => []], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json(['success' => false, 'message' => $e->getMessage(), 'data' => []], 500);
        }
    }

    public function logout()
    {
        if (Auth::check()) {
            Auth::user()->OAuthAcessToken()->delete();
            return response()->json(['success' => true, 'message' => 'You have been logout successfully', 'data' => []], 500);
        }
    }

    public function changePassword(PasswordRequest $request)
    {
        try {
            $data = $request->all();
            $user = Auth::user();
            $user_id = $user->id;
            $user_password = $user->password;
            $old_password = $data['old_password'];

            if (!Hash::check($old_password, $user_password)) {
                return response()->json(['success' => false, 'message' => 'The specified password does not match the database password', 'data' => []], 422);
            }

            if ($old_password == $data['new_password']) {
                return response()->json(['success' => false, 'message' => 'New password must be different from Old Password', 'data' => []], 422);
            }

            $new_passsword = bcrypt($data['new_password']);

            $user_obj = User::where('id', $user_id)->update(['password' => $new_passsword]);

            if ($user_obj > 0) {
                return response()->json(['success' => true, 'message' => 'Your password has been change successfully', 'data' => []], 200);
            } else {
                return response()->json(['success' => false, 'message' => "Some thing was wrong.", 'data' => []], 500);
            }
        } catch (\Exception $e) {
            report($e);

            return response()->json(['success' => false, 'message' => $e->getMessage(), 'data' => []], 500);
        }
    }

    public function getProfile()
    {
        try {
            $user = Auth::user()->toArray();
            unset($user['selected_team']);
            unset($user['added_by']);
            unset($user['password']);
            unset($user['pwresetkey']);
            unset($user['keyexpire']);
            unset($user['created_at']);
            unset($user['updated_at']);
            $user['user_detail'] = [];
            $user_detail = People::where('user_id',$user['id'])
                                    ->select("PeopleID","FirstName","LastName",
                                    "AddressLine1","AddressLine2","user_id","Suite",
                                    "Prov","City","Country","Postal","EmergencyPhone",
                                    "EmergencyExt","SpecialCondition","EmergencyContact")->first();
            if($user_detail){
                $user['user_detail'] = $user_detail;
            }
            $img_path = Config::get('app.url')."/storage/".$user['img'];

            if(\File::exists(storage_path($user['img'])) && $user['img'] != '' && $user['img'] != null) {
                $user['profile_pic'] = $img_path;
            } else {
                $user['profile_pic'] = Config::get('app.url') . '/storage/user.png';
            }

            return response()->json(['success' => true, 'message' => 'Profile has been found', 'data' => $user], 200);

        } catch (\Exception $e) {
            report($e);

            return response()->json(['success' => false, 'message' => $e->getMessage(), 'data' => []], 500);
        }
    }

    /**
     * editProfile
     *
     * @param  mixed $request
     *
     * @return void
     */
    public function editProfile(RegisterRequest $request)
    {
        try {
            $user = Auth::user();
            
            $data = $request->all();
            if (isset($data['phonenumber']) && $data['phonenumber'] != '') {
                $user->phonenumber = $data['phonenumber'];
            }
            if(isset( $data['user_detail']['FirstName']) && isset( $data['user_detail']['LastName'])){
                $user->fullname = $data['user_detail']['FirstName'].' '.$data['user_detail']['LastName'];
            }elseif(isset( $data['user_detail']['FirstName']) && !isset($data['user_detail']['LastName'])){
                $user->fullname = $data['user_detail']['FirstName'];
            }elseif(!isset( $data['user_detail']['FirstName']) && isset($data['user_detail']['LastName'])){
                $user->fullname = $data['user_detail']['LastName'];
            }
           
            $user->img = $data['img'];
            $user->save();
            
            $user_data = array();
            $user_detail = People::where('user_id',$user->id)->first();
            #If role id 2(employee) send notification to all Admin for updated fields
            if($user->role_id == 2){
                $this->sendUpdatedFieldNotification($user_detail, $data['user_detail'], $user);
            }
            if(isset($data['user_detail']) && !empty($data['user_detail'])){
                if($user_detail){
                    #update user_detail
                    People::where('user_id',$user->id)->update($data['user_detail']);
                }else{
                    #create user detail
                    $data['user_detail']['user_id'] = $user->id;
                    People::create($data['user_detail']);
                }
            }

            $success['username'] = $user->fullname;
            $success['email'] = $user->username;

            $img_path = Config::get('app.url') ."/storage/". $user->img;

            if (\File::exists(storage_path($user->img)) && $user->img != '' && $user->img != null) {
                $success['profile_pic'] = $img_path;
            } else {
                $success['profile_pic'] = Config::get('app.url') . '/storage/user.png';
            }

            return response()->json(['success' => true, 'message' => 'Profile update successfully', 'data' => $success], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json(['success' => false, 'message' => $e->getMessage(), 'data' => []], 500);
        }
    }

    /**
     * sendUpdatedFieldNotification
     *
     * @param  mixed $user_detail
     * @param  mixed $request
     * @param  mixed $user
     *
     * @return void
     */
    private function sendUpdatedFieldNotification($user_detail, $request, $user){
       
        $updated_data = array();
        #Check First name is update or not
        if($user_detail['FirstName'] != $request['FirstName']){
            $updated_data['Old_FirstName'] = $user_detail['FirstName'];
            $updated_data['New_FirstName'] = $request['FirstName'];
        }

        #Check Last name is update or not
        if($user_detail['LastName'] != $request['LastName']){
            $updated_data['Old_LastName'] = $user_detail['LastName'];
            $updated_data['New_LastName'] = $request['LastName'];
        }

        #Check AddressLine1 is update or not
        if($user_detail['AddressLine1'] != $request['AddressLine1']){
            $updated_data['Old_AddressLine1'] = $user_detail['AddressLine1'];
            $updated_data['New_AddressLine1'] = $request['AddressLine1'];
        }

        #Check City is update or not
        if($user_detail['City'] != $request['City']){
            $updated_data['Old_City'] = $user_detail['City'];
            $updated_data['New_City'] = $request['City'];
        }

        #Check Postal is update or not
        if($user_detail['Postal'] != $request['Postal']){
            $updated_data['Old_Postal'] = $user_detail['Postal'];
            $updated_data['New_Postal'] = $request['Postal'];
        }

        #Check Prov is update or not
        if($user_detail['Prov'] != $request['Prov']){
            $updated_data['Old_Prov'] = $user_detail['Prov'];
            $updated_data['New_Prov'] = $request['Prov'];
        }

        #Check Country is update or not
        if($user_detail['Country'] != $request['Country']){
            $updated_data['Old_Country'] = $user_detail['Country'];
            $updated_data['New_Country'] = $request['Country'];
        }
    
        if(!empty($updated_data)){
            $updated_data['updated_by'] = $user_detail['FirstName'].' '.$user_detail['LastName'];
            #Get all Admin emails for current Region
            $admin_email = User::where('role_id',1)->get()->pluck('email')->toArray();
            try {
                \Mail::send(['html' => 'email.employee-update-notification'], array('data' => $updated_data) , function ($message) use ($admin_email) {
                    $message->to($admin_email)->subject('Profile Update');
                });
                return true;
            } catch (\Exception $e) {}
        }
    }
////////////////////////////// Create/Get/Update/Soft-Delete **USERS** /////////////////////////////////////////////
    
    /* Create User */
    public function store(Store $request)
    { 
        if ($user = $this->userRepository->create($request->all())) {
            /* **** Send Password Email *** */
            $user = \App\Models\SysUser::where('username', $request->input('email'))->first();
            $this->SendForgotPasswordEmail($user, $request, true);
            return response()->json(['data' => $user,'message'=>'User added successfully.'], 200);
        } else {
            return response()->json(['error' => 'User has been added already.'], 422);
        }
    }

    /* Get User */
    public function index(Index $request)
    {
        $perPage = 10;
        $offset = '0';
        if($request->has('per_page')){
            $perPage = $request->per_page;
        }
        $columns_search = ['fullname', 'username'];
        $data = User::where('role_id','!=',2)
                ->where('role_id','!=',0)
                ->where('id','!=',Auth::user()->id)
                ->where('region_id','=',Auth::user()->region_id);
        /****** Search *******/
        if($request->has('q')){
            $data->where(function ($query) use($columns_search, $request) {
               foreach($columns_search as $column) {
                  $query->orWhere($column, 'LIKE' , '%' . $request->q . '%');
               }
            });
        }
        /****** Role ********/
        if($request->has('role') && $request->role != ''){
            $data->Where('role_id', '=' , $request->role);
        }
        $data =  $data->orderBy('created_at','desc')->paginate($perPage);
        return $this->response->paginator($data, new UserTransformer());
    }
    
    /* Get Single User */
    public function show(Show $request, $user)
    { 
        $user = $this->userRepository->where('id', $user)->first();
        
        if( $user && is_null($user)==false ){
            return $this->response->item($user, new UserTransformer());
        }
        return $this->response->errorNotFound('User Not Found', 404);
    }

     /* Update Single User */
     public function update(Update $request,  $user)
     {  
        $user_id = $user;
        $user_data = $request->all();
        $user_data['fullname'] = $user_data['FirstName'].' '.$user_data['LastName'];
        if ($user =  $this->userRepository->updateById( $user, $user_data ) ) {
            $this->userRepository->updatePeople($user_id, $request->all());
            return $this->response->item($user, new UserTransformer());
        } else {
            return response()->json(['error' => 'Error occurred while saving User.'], 422);
        }
     }

     /* Delete Single User */
    public function destroy(Request $request, $user)
    {   
        $user_id = $user;
        $user_data = User::where('id',$user_id)->first();
        if($user_data && is_null($user_data->deleted_at)){
            $user = $this->userRepository->getById($user);
            if($user && is_null($user)==false ){
                $get_event = Tblevent::where(function ($query) use($user_id) {
                    $query->where('account_manager', '=', $user_id)
                          ->orWhere('sales_manager', '=', $user_id);
                })->first();
                if(!$get_event){
                    People::where('user_id',$user_id)->update(['deleted_at' => date('Y-m-d H:i:s')]);
                    $this->userRepository->deleteById($user_id);
                    return $this->response->array(['status' => 200, 'message' => 'User successfully deleted']);
                }else{
                    return response()->json(['error' => 'User not deleted because this user added in event.'], 422);
                }
            } else {
                return response()->json(['error' => 'Error occurred while deleting User.'], 422);
            }
        }else{
            return response()->json(['error' => 'User not found.'], 422);
        }
    }
}
