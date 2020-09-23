<?php

namespace App\Http\Requests\Api\Tblevents;

use Dingo\Api\Http\FormRequest;

class Update extends FormRequest 
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() 
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() 
    {
        return [
			'EventName' => 'nullable|max:255',
			'VID' => 'nullable|numeric',
			'CfgID' => 'nullable|numeric',
		//	'EventID' => 'nullable|max:15|unique:events,EventID'.'MB06-13488',
			'ClientId' => 'nullable|numeric',
			'InternalNotes' => 'nullable|max:255',
			'ExternalNotes' => 'nullable|max:255',
			'EventDateCreated' => 'nullable|date',
			'EventCreatedBy' => 'nullable|max:30',
			'EventDateLastUpdated' => 'nullable|date',
			'EventUpdatedBy' => 'nullable|max:30',
			'Schedule' => 'nullable|numeric',
			'ShiftSort' => 'nullable|numeric',
			'Status' => 'nullable|numeric',
			'JobNumber' => 'nullable|max:5',
			'UnitNumber' => 'nullable|max:10',
			'ItemNumber' => 'nullable|max:10',
			'PONumber' => 'nullable|max:15',
			'POCap' => 'nullable|numeric',
			'ShortList' => 'nullable|boolean',
			'Booked' => 'nullable|boolean',
			'Invoiced' => 'nullable|boolean',
			'Filter' => 'nullable|boolean',
			'Tax1_Name' => 'nullable|max:20',
			'Tax1_Rate' => 'nullable|numeric',
			'Tax1_RegNum' => 'nullable|max:25',
			'Tax1_ApplyLabour' => 'nullable|boolean',
			'Tax2_Name' => 'nullable|max:20',
			'Tax2_Rate' => 'nullable|numeric',
			'Tax2_RegNum' => 'nullable|max:25',
			'Tax2_ApplyLabour' => 'nullable|boolean',
			'AS_Amount' => 'nullable|numeric',
			'AS_Tax1' => 'nullable|boolean',
			'AS_Tax2' => 'nullable|boolean',
			'WorkCategoryID' => 'nullable|numeric',
			'AccountRep' => 'nullable|max:50',
			'SalesRep' => 'nullable|max:50',
			'EventNameShort' => 'nullable|max:255',
        ];
    }

    /**
    * Get the error messages for the defined validation rules.
    *
    * @return array
    */
    public function messages()
    {
        return [
     
        ];
    }

}
