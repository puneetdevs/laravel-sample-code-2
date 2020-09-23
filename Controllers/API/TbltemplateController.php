<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use App\Models\Tbltemplate;
use App\Models\Tblevent;
use App\Transformers\TbltemplateTransformer;
use App\Transformers\EventTransformer\TbleventTransformer;
use App\Http\Requests\Api\Tbltemplates\Index;
use App\Http\Requests\Api\Tbltemplates\Show;
use App\Http\Requests\Api\Tbltemplates\Create;
use App\Http\Requests\Api\Tbltemplates\Store;
use App\Http\Requests\Api\Tbltemplates\Edit;
use App\Http\Requests\Api\Tbltemplates\Update;
use App\Http\Requests\Api\Tbltemplates\Destroy;
use App\Http\Requests\Api\Tbltemplates\CreateEvent;
use App\Http\Requests\Api\Tbltemplates\TemplateFromEvent;
use App\Repositories\NotesRepository;
use App\Repositories\EventRepository;
use Auth;

/**
 * Tbltemplate
 *
 * @Resource("Tbltemplate", uri="/templates")
 */

class TbltemplateController extends ApiController
{
    public function __construct(NotesRepository $notesRepository)
    {
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
        $perPage = 10;
        if($request->has('per_page')){
            $perPage = $request->per_page;
        }
        return $this->response->paginator(Tbltemplate::paginate($perPage), new TbltemplateTransformer());
    }

    /**
     * show
     *
     * @param  mixed $request
     * @param  mixed $template_id
     *
     * @return void
     */
    public function show(Show $request, $template_id)
    {
        $tbltemplate = Tbltemplate::where('TemplateID', $template_id)->first();
        return $this->response->item($tbltemplate, new TbltemplateTransformer());
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
        $model=new Tbltemplate;
        $template = $request->all();
        $template['region_id'] = Auth::user()->region_id;
        if ($template_data = Tbltemplate::create($template)) {
            $template = Tbltemplate::where('TemplateID',$template_data->TemplateID)->first();
            return $this->response->item($template, new TbltemplateTransformer());
        } else {
            return response()->json(['error' => 'Error occurred while saving template.'], 422);
        }
    }
 
    /**
     * update
     *
     * @param  mixed $request
     *
     * @return void
     */
    public function update(Update $request)
    {
        $template_data = $request->all();
        if (Tbltemplate::where('TemplateID',$template_data['TemplateID'])->update($template_data)) {
            $template = Tbltemplate::where('TemplateID',$template_data['TemplateID'])->first();
            return $this->response->item($template, new TbltemplateTransformer());
        } else {
            return response()->json(['error' => 'Error occurred while saving template.'], 422);
        }
    }

    /**
     * destroy
     *
     * @param  mixed $request
     * @param  mixed $tbltemplate
     *
     * @return void
     */
    public function destroy(Destroy $request, $tbltemplate)
    {
        $tbltemplate = Tbltemplate::findOrFail($tbltemplate);

        if ($tbltemplate->delete()) {
            return $this->response->array(['status' => 200, 'message' => 'Tbltemplate successfully deleted']);
        } else {
            return response()->json(['error' => 'Error occurred while deleting template.'], 422);
        }
    }

    /**
     * templateNote
     *
     * @param  mixed $request
     * @param  mixed $template_id
     *
     * @return void
     */
    public function templateNote(Request $request, $template_id){
        if(!empty($request->all())) {
            $request->ParentID = $template_id;
            $notes_data['Note'] = $request->Note;
            $notes_data['ParentCode'] = 'TEMPLATE';
            $notes_data['ParentID'] =  $template_id;
            $notes_data['AddedBy'] =  Auth::user()->id;

            $note =  $this->notesRepository->create($notes_data);
            if ($note) {
                    return response()->json(['data' => 'Note has been added successfully.'], 200);
            }
            return response()->json(['error' => 'Note has been added already for this Sale.'], 422);
        }
    }

    /**
     * createEventFromTemplate
     *
     * @param  mixed $request
     * @param  mixed $eventRepository
     *
     * @return void
     */
    public function createEventFromTemplate(CreateEvent $request, EventRepository $eventRepository){
        $post_request = $request->all();
        $template_data = Tbltemplate::where('TemplateID',$post_request['TemplateID'])->first();
        $event_data['EventName'] = $request->EventName;
        $event_data['WorkCategoryID'] = $request->WorkCategoryID;
        $event_data['sales_manager'] = $request->sales_manager;
        $event_data['account_manager'] = $request->account_manager;
        $event_data['Booked'] = $request->Booked;
        $event_data['Schedule'] = $template_data->Schedule;
        $event_data['CfgID'] = $template_data->RateConfiguration;
        $event_data['ClientId'] = $template_data->Client;
        $event_data['VID'] = $template_data->Venue;
        if ($event = $eventRepository->create($event_data)) {
            return $this->response->item($event, new TbleventTransformer());
        } else {
            return response()->json(['error' => 'Error occurred while saving event.'], 422);
        }
    }

    public function createTemplateFromEvent(TemplateFromEvent $request){
        $event_data = Tblevent::where('EID',$request->EID)->first();
        if($event_data){
            $template['TemplateName'] = $request->TemplateName;
            $template['RateConfiguration'] = $event_data->CfgID;
            $template['Venue'] = $event_data->VID;
            $template['Client'] = $event_data->ClientId;
            $template['Schedule'] = $event_data->Schedule;
            if ($template_data = Tbltemplate::create($template)) {
                #Copy event note to template
                $this->copyNote($template_data->TemplateID, $request->EID, 'TEMPLATE');
                $template = Tbltemplate::where('TemplateID',$template_data->TemplateID)->first();
                return $this->response->item($template, new TbltemplateTransformer());
            } else {
                return response()->json(['error' => 'Error occurred while saving template.'], 422);
            }
        }
        return response()->json(['error' => 'Invalid event id.'], 422);
    }

    private function copyNote($parent_id, $EID, $parent_code){
        $event_note = $this->notesRepository->where('ParentCode', 'EVENT')->where('ParentID',$EID)->get();
        $event_notes = $event_note->toArray();
        if(!empty($event_notes)){
            $template_event = array();
            foreach($event_notes as $key=>$note){
                $template_event[$key]['ParentID'] = $parent_id;
                $template_event[$key]['ParentCode'] = $parent_code;
                $template_event[$key]['Note'] = $note['Note'];
                $template_event[$key]['NoteDate'] = $note['NoteDate'];
                $template_event[$key]['AddedBy'] = Auth::user()->id;
                $template_event[$key]['created_at'] = date('Y-m-d H:i:s');
                $template_event[$key]['updated_at'] = date('Y-m-d H:i:s');
            }
            $this->notesRepository->createMultiple($template_event);
        }
    }

    public function createDuplicateEvent(Request $request, $event_id, EventRepository $eventRepository){
        $event_data = Tblevent::where('EID',$event_id)->first();
        if($event_data){
            $event = $event_data->toArray();
            unset($event['EID']); 
            if ($event_result = $eventRepository->create($event)) {
                #Copy event note to template
                $this->copyNote($event_result->EID, $event_id, 'EVENT');
                return $this->response->item($event_result, new TbleventTransformer());
            } else {
                return response()->json(['error' => 'Error occurred while saving event.'], 422);
            }
        }
        return response()->json(['error' => 'Invalid event id.'], 422);
    }
    
}
