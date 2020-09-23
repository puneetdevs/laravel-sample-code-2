<?php
namespace App\Transformers;

use LaraCrud\Helpers\TransformerAbstract;
use League\Fractal\ParamBag;
use App\Models\EmployeeApplication;
use App\Models\File;
use Illuminate\Support\Facades\Config;



class EmployeeApplicationTransformer extends TransformerAbstract
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


    public function transform(EmployeeApplication $employeeApplication)
    {
		#Add site url with image
		if (\File::exists(storage_path($employeeApplication->ProfileImage)) && $employeeApplication->ProfileImage != '' && $employeeApplication->ProfileImage != null) {
			$image = Config::get('app.url') .'/storage/'. $employeeApplication->ProfileImage;
		} else {
			$image = Config::get('app.url') . '/storage/user.png';
		}

        $data= [
			"id" => $employeeApplication->id,
			"FirstName" => $employeeApplication->FirstName,
			"LastName" => $employeeApplication->LastName,
			"Initial" => $employeeApplication->Initial,
			"Address1" => $employeeApplication->Address1,
			"Address2" => $employeeApplication->Address2,
			"Suite" => $employeeApplication->Suite,
			"Country" => $employeeApplication->Country,
			"Email" => $employeeApplication->Email,
			"Province" => $employeeApplication->Province,
			"City" => $employeeApplication->City,
			"Postal_code" => $employeeApplication->Postal_code,
			"Country_code" => $employeeApplication->Country_code,
			"Cell" => $employeeApplication->Cell,
			"DateOfBirth" => $employeeApplication->DateOfBirth,
			"Status" => $employeeApplication->Status,
			"rejected_reason" => $employeeApplication->rejected_reason,
			"EmployeeID" => $employeeApplication->EmployeeID,
			"ProfileImage" => $image,
			"region_id" => $employeeApplication->region_id,
			"action_date" => $employeeApplication->action_date,
			//"timezone_completed_at" => $employeeApplication->timezone_completed_at, 
			"created_at" => $employeeApplication->created_at, 
			"updated_at" => $employeeApplication->updated_at,
			"deleted_at" => $employeeApplication->deleted_at,

        ];
        return $this->filterFields($data);

	}
    
}