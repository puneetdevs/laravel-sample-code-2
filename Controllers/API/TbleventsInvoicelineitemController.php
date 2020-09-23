<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use App\Models\TbleventsInvoicelineitem;
use App\Transformers\TbleventsInvoicelineitemTransformer;
use App\Http\Requests\Api\TbleventsInvoicelineitems\Index;
use App\Http\Requests\Api\TbleventsInvoicelineitems\Show;
use App\Http\Requests\Api\TbleventsInvoicelineitems\Create;
use App\Http\Requests\Api\TbleventsInvoicelineitems\Store;
use App\Http\Requests\Api\TbleventsInvoicelineitems\Edit;
use App\Http\Requests\Api\TbleventsInvoicelineitems\Update;
use App\Http\Requests\Api\TbleventsInvoicelineitems\Destroy;
use App\Repositories\LineItemRepository;
use Auth;

/**
 * TbleventsInvoicelineitem
 *
 * @Resource("TbleventsInvoicelineitem", uri="/events_invoice_line_items")
 */

class TbleventsInvoicelineitemController extends ApiController
{   
    public function __construct(LineItemRepository $lineItemRepository){
      $this->lineItemRepository = $lineItemRepository;
    }
    
    public function index(Index $request)
    {
       
        $perPage = 10;
        if($request->has('per_page')){
            $perPage = $request->per_page;
        }
        if($request->has('event_id')){
            $line_item = $this->lineItemRepository
            ->where('EID',$request->event_id)
            ->where('region_id',Auth::user()->region_id)
            ->paginate($perPage);
            return $this->response->paginator($line_item, new TbleventsInvoicelineitemTransformer());
        }
        return response()->json(['error' => 'Please send valid event id.'], 422);
    }

    public function show(Show $request, TbleventsInvoicelineitem $tbleventsinvoicelineitem)
    {
      return $this->response->item($tbleventsinvoicelineitem, new TbleventsInvoicelineitemTransformer());
    }

    public function store(Store $request)
    {
        $model=new TbleventsInvoicelineitem;
        $model->fill($request->all());
        if ($model->save()) {
            return $this->response->item($model, new TbleventsInvoicelineitemTransformer());
        } else {
            return response()->json(['error' => 'Error occurred while saving events invoice line item.'], 422);
        }
    }
 
    public function update(Update $request, $line_item_id)
    {
        if ($line_item =  $this->lineItemRepository->updateById( $line_item_id, $request->all() ) ) {
            return $this->response->item($line_item, new TbleventsInvoicelineitemTransformer());
        } else {
            return response()->json(['error' => 'Error occurred while update line item.'], 422);
        }
    }

    public function destroy(Destroy $request, $tbleventsinvoicelineitem)
    {
        if ( $this->lineItemRepository->deleteById($tbleventsinvoicelineitem) ) {
            return $this->response->array(['status' => 200, 'message' => 'Line item successfully deleted']);
        } else {
            return response()->json(['error' => 'Error occurred while deleting Line item.'], 422);
        }
    }

}
