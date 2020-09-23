<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use App\role;
use App\Transformers\RoleTransformer;
use App\Repositories\RoleRepository;


/**
 * role
 *
 * @Resource("role", uri="/roles")
 */

class RoleController extends ApiController
{
    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }
    
    public function index(Request $request)
    {
       return $this->response->paginator(role::whereNotIn('slug',['employee'])->paginate(10), new RoleTransformer());
    }

    public function show(Request $request, $role)
    {
        try{
            $role = $this->roleRepository->getById($role);
            if($role && is_null($role)==false ){
                return $this->response->item($role, new RoleTransformer());
            }
            return $this->response->errorNotFound('Role Not Found', 404);
        }
        catch(Exception $e) {
            return $this->response->errorNotFound('Role Not Found', 404);
        }
    }

    public function store(Request $request)
    {
        $model=new role;
        $model->fill($request->all());
        if ($model->save()) {
            return $this->response->item($model, new roleTransformer());
        } else {
            return response()->json(['error' => 'Error occurred while saving role.'], 422);
        }
    }
 
    public function update(Request $request,  role $role)
    {
        $role->fill($request->all());

        if ($role->save()) {
            return $this->response->item($role, new roleTransformer());
        } else {
            return response()->json(['error' => 'Error occurred while saving role.'], 422);
        }
    }

    public function destroy(Request $request, $role)
    {
        $role = role::findOrFail($role);

        if ($role->delete()) {
            return $this->response->array(['status' => 200, 'message' => 'role successfully deleted']);
        } else {
            return response()->json(['error' => 'Error occurred while deleting role.'], 422);
        }
    }

}
