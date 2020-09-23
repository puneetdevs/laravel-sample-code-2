<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use App\Models\Note;
use App\Transformers\NoteTransformer;
use App\Http\Requests\Api\Notes\Index;
use App\Http\Requests\Api\Notes\Show;
use App\Http\Requests\Api\Notes\Create;
use App\Http\Requests\Api\Notes\Store;
use App\Http\Requests\Api\Notes\Update;
use App\Http\Requests\Api\Notes\Destroy;


/**
 * Note
 *
 * @Resource("Note", uri="/notes")
 */

class NoteController extends ApiController
{
    
    public function index(Index $request)
    {
       return $this->response->paginator(Note::paginate(10), new NoteTransformer());
    }

    public function show(Show $request, Note $note)
    {
      return $this->response->item($note, new NoteTransformer());
    }

    public function store(Store $request)
    {
        $model=new Note;
        $model->fill($request->all());
        if ($model->save()) {
            return $this->response->item($model, new NoteTransformer());
        } else {
            return response()->json(['error' => 'Error occurred while saving Note.'], 422);
        }
    }
 
    public function update(Update $request,  Note $note)
    {
        $note->fill($request->all());

        if ($note->save()) {
            return $this->response->item($note, new NoteTransformer());
        } else {
            return response()->json(['error' => 'Error occurred while saving Note.'], 422);
        }
    }

    public function destroy(Destroy $request, $note)
    {
        $note = Note::findOrFail($note);

        if ($note->delete()) {
            return $this->response->array(['status' => 200, 'message' => 'Note successfully deleted']);
        } else {
            return response()->json(['error' => 'Error occurred while deleting Note.'], 422);
        }
    }

}
