<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use App\Models\Client;
use App\Transformers\ClientTransformer;
use App\Http\Requests\Api\Clients\Index;
use App\Http\Requests\Api\Clients\Show;
use App\Http\Requests\Api\Clients\Create;
use App\Http\Requests\Api\Clients\Store;
use App\Http\Requests\Api\Clients\Update;
use App\Http\Requests\Api\Clients\Destroy;
use App\Http\Requests\Api\Clients\ChangeStatus;

/* Client Contacts */
use App\Models\ClientContact;
use App\Transformers\ClientContactTransformer;
use App\Http\Requests\Api\ClientContacts\CreateContact;
use App\Http\Requests\Api\ClientContacts\UpdateContact;

/**
 * Client
 *
 * @Resource("Client", uri="/clients")
 */

class ClientController extends ApiController
{
    
    public function index(Index $request)
    {
        $perPage = 10;
        $offset = '0';
        if($request->has('per_page')){
            $perPage = $request->per_page;
        }
        
        $columns_search = ['Name', 'ClientPhone', 'email', 'abbrevation','City'];

        $data = Client::where([]);

        /****** Status *******/
        if($request->has('status')){
            $data->where('status',$request->status);
        }

        /****** Search *******/
        if($request->has('q')) {
            $data->where(function ($query) use($columns_search, $request) {
               foreach($columns_search as $column) {
                  $query->orWhere($column, 'LIKE', '%' . $request->q . '%');
               }
            });
        }

       $data = $data->orderBy('ID','desc');
       $data =  $data->paginate($perPage);
       return $this->response->paginator($data, new ClientTransformer());
    }

    public function show(Show $request, $client)
    {
        $client = Client::find($client);
        if( $client && is_null($client)==false ){
            return $this->response->item($client, new ClientTransformer());
        }
        return $this->response->errorNotFound('Client Not Found', 404);
    }

    public function store(Store $request)
    {
        $model=new Client;
        $input = $this->convert_input($request->all());
        $model->fill($input);
        if ($model->save()) {
            return $this->response->item($model, new ClientTransformer());
        } else {
            return response()->json(['error' => 'Error occurred while saving Client'], 422);
        }
    }
 
    public function update(Update $request,  $client)
    {
        $client = Client::findOrFail($client);

        $input = $this->convert_input($request->all());
        $client->fill($input);

        if ($client->save()) {
            return $this->response->item($client, new ClientTransformer());
        } else {
            return response()->json(['error' => 'Error occurred while saving Client'], 422);
        }
    }

    public function destroy(Destroy $request, $client)
    {
        $client = Client::findOrFail($client);
        if ($client->delete()) {
            return $this->response->array(['status' => 200, 'message' => 'Client successfully deleted']);
        } else {
            return response()->json(['error' => 'Error occurred while deleting Client'], 422);
        }
    }

    public function changeClientStatus(ChangeStatus $request, $client)
    {
        $client = Client::findOrFail($client);
        if ($client->update(['status' => $request->status])) {
            return $this->response->array(['status' => 200, 'message' => 'Client status changed successfully']);
        } else {
            return response()->json(['error' => 'Error occurred while changed Client status'], 422);
        }
    }
    
    public function getContacts(Request $request, $client){
        $client_id = $client;
        $client = Client::find($client);
        
        if($client && is_null($client)==false ) {
            
            $perPage = 10;
            $offset = '0';
            if($request->has('per_page')){
                $perPage = $request->per_page;
            }
            
            $columns_search = ['name', 'title', 'email'];

            $data = ClientContact::Where('ClientId', $client_id);

            /****** Search *******/
            if($request->has('q')) {
                foreach($columns_search as $column){
                    $data->orWhere($column, 'LIKE', '%' . $request->q . '%');
                }
            }

            $data =  $data->paginate($perPage);
            return $this->response->paginator($data, new ClientContactTransformer());
        }
        return $this->response->errorNotFound('Client Not Found', 404);
    }

    public function postContact(CreateContact $request, $client) {
        $client_id = $client;
        $client = Client::find($client);
        if($client && is_null($client)==false ) {
            $model = new ClientContact;
            $input_data = $this->convert_input_contact($request->all());
          
            $model->fill($input_data);
            $model->ClientId = $client_id;
            if ($model->save()) {
                return $this->response->item($model, new ClientContactTransformer());
            } else {
                return response()->json(['error' => 'Error occurred while saving Client'], 422);
            }
        }
        return $this->response->errorNotFound('Client Not Found', 404);
    }

    public function getShowContact($client, $contact){
        $client = Client::find($client);
        if( $client && is_null($client)==false ){
            $contact = ClientContact::find($contact);
            if( $contact && is_null($contact)==false ){
                return $this->response->item($contact, new ClientContactTransformer());
            }
            return $this->response->errorNotFound('Contact Not Found', 404);
        }
        return $this->response->errorNotFound('Client Not Found', 404);
    }


    public function putContact(UpdateContact $request, $client, $contact) {
        $client_id = $client;
        $client = Client::find($client);
        if($client && is_null($client)==false ) {
            $contact = ClientContact::findOrFail($contact);
            
            $data_update = $this->convert_input_contact($request->all());
            $contact->fill($data_update);
            if ($contact->save()) {
                return $this->response->item($contact, new ClientContactTransformer());
            } else {
                return response()->json(['error' => 'Error occurred while saving Contact'], 422);
            }
        }
        return response()->json(['error' => 'Client Not Found'], 422);
    }

    public function deleteContacts($client, $contact){
        $client_id = $client;
        $client = Client::find($client);
        if($client && is_null($client)==false ) {
            $contact = ClientContact::findOrFail($contact);
            if ($contact->delete()) {
                return $this->response->array(['status' => 200, 'message' => 'Contact Successfully Deleted']);
            } else {
                return response()->json(['error' => 'Error occurred while deleting Client'], 422);
            }
        }
        return response()->json(['error' => 'Client Not Found'], 422);
    }


    protected function convert_input($input){
        $val_keys = ['name'=>'Name', 
            'abbrevation'=>'abbrevation', 
            'address'=>'AddressLine1', 
            'city'=>'City', 
            'providance'=>'Prov', 
            'postal_code'=>'Postal',
            'country'=>'Country',
            'notes'=>'ClientNotes',
            'fax'=>'ClientFax',
            'phone'=>'ClientPhone',
            'PhoneExt'=>'PhoneExt',
            'gst_number'=>'GSTNumber',
            'code'=>'Code',
            'email'=>'email'
        ];
        $out = [];
        foreach($input as $field_key=>$field_val){
            if(isset($val_keys[$field_key])){
                $out[$val_keys[$field_key]] = $field_val;
            }
        }
       return $out;
    }

    public function convert_input_contact($input) {
        $val_keys = [
            'client_id'=>'ClientId',
            'name'=>'ContactName',
            'title'=>'ContactTitle',
            'home_phone'=>'Home',
            'email'=>'Email',
            'work_phone'=>'Work',
            'cell_phone'=>'Cell',
            'ext'=>'Ext'
        ];
        $out = [];
        foreach($input as $field_key=>$field_val){
            if(isset($val_keys[$field_key])){
                $out[$val_keys[$field_key]] = $field_val;
            }
        }
       return $out;
    }

}
