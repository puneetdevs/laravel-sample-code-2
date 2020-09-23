<?php
namespace App\Transformers\EmployeeTransformer;

use LaraCrud\Helpers\TransformerAbstract;
use League\Fractal\ParamBag;
use App\Models\People;
use App\User;



class EmployeeTransformer extends TransformerAbstract
{
     /**
     * @var array
     */
    private $validParams = ['q', 'limit', 'page','fields'];

    /**
     * @var array
     */
    protected $availableIncludes = [];

     /**
      * @var array
      */
    protected $defaultIncludes = [];


    public function transform(People $People)
    {
        $user_image = '';
        if(isset(User::select('img')->where('id',  $People->user_id)->first()->img)){
            $user_image = User::select('img')->where('id',  $People->user_id)->first()->img;
        }
       
        $data= [
			"id" => $People->PeopleID,
            "Salutation" => $People->Salutation,
            "FirstName" => $People->FirstName,
			"Initial" => $People->Initial,
			"LastName" => $People->LastName,
            "AddressLine1" => $People->AddressLine1,
            "AddressLine2" => $People->AddressLine2,
            "City" => $People->City,
            "Prov" => $People->Prov,
            "Postal" => $People->Postal,
            "Country" => $People->Country,
            "Region" => $People->Region,
            "Company" => $People->Company,
            //"[Union]" => $People->[Union],
            "Home" => $People->Home,
            "WorkExt" => $People->WorkExt,
            "Cell" => $People->Cell,
            "Pager" => $People->Pager,
            "Fax" => $People->Fax,
            "Email" => $People->Email,
            "Notes" => $People->Notes,
            "DateOfHire" => $People->DateOfHire,
            "MailingList" => $People->MailingList,
            "DateOfBirth" => $People->DateOfBirth,
            "DateCreated" => $People->DateCreated,
            "DateLastUpdated" => $People->DateLastUpdated,
            "UpdatedBy" => $People->UpdatedBy,
            "EmployeeNumber" => $People->EmployeeNumber,
            "SIN" => $People->SIN,
            "ContactType" => $People->ContactType,
            "EmergencyContact" => $People->EmergencyContact,
            "EmergencyPhone" => $People->EmergencyPhone,
            "EmergencyExt" => $People->EmergencyExt,
            "SpecialCondition" => $People->SpecialCondition,
            "ShirtSize" => $People->ShirtSize,
            "Rating" => $People->Rating,
            "Filter" => $People->Filter,
            "Availability" => $People->Availability,
            "AvailableAnytime" => $People->AvailableAnytime,
            "PhotoFile" => $People->PhotoFile,
            "MobileInfo" => $People->MobileInfo,
            "Sex" => $People->Sex,
            "Suite" => $People->Suite,
            "CallFlag" => $People->CallFlag,
            "NickName" => $People->NickName,
            "EvalEffectiveDate" => $People->EvalEffectiveDate,
            "YearsNotWorked" => $People->YearsNotWorked,
            "TotalHiringFactor" => $People->TotalHiringFactor,
            "user_id" => $People->user_id,
            'region_id'=>$People->region_id,
            'Lat'=>$People->Lat,
            'Lng'=>$People->Lng,
            'do_not_call'=>$People->do_not_call,
            'do_not_call_reason'=>$People->do_not_call_reason,
            'is_archive' => $People->is_archive,
            'image'=> $user_image
        ];
        return $this->filterFields($data);
    }

    
}