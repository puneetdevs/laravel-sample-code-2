<?php

namespace App\Repositories;


use DB;
use App\Exceptions\Handler;
use App\Repositories\BaseRepository;
use App\Models\Note;
use DateTime;
use Carbon\Carbon;
/**
 * Class NotesRepository.
 */
class NotesRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return Note::class;
    }

    /**
     * @param $email
     *
     * @return User
    */
    public function getNotesByType($object_type, $parent_id){
       return $this->model
       ->select('ID', 'Note', 'NoteDate','AddedBy','ParentID')
        ->with(['addedBy'=>function($q){
            $q->select('id', 'fullname');
        }])
       ->where(['ParentCode'=>$object_type, 'ParentID'=>$parent_id])
       ->get();
    }

    /**
     * @param $email
     *
     * @return User
    */
    public function getNotesApplicationByType($object_type, $parent_id){
        return $this->model
        ->select('ID', 'Note', 'NoteDate','AddedBy','ParentID')
         ->with(['addedBy'=>function($q){
             $q->select('id', 'fullname');
         }])
        ->where(['ParentCode'=>$object_type, 'ParentID'=>$parent_id]);
     }

    public function create(array $data) : bool
    { 
        
        return DB::transaction(function () use ($data) {
         $note = parent::create([
                'ParentID' => isset($data['ParentID']) ? $data['ParentID'] :'0',
                'ParentCode' => isset($data['ParentCode']) ? $data['ParentCode'] : 0,
                'Note' => isset($data['Note']) ? $data['Note'] : '',
                'AddedBy' => $data['AddedBy'],
                'NoteDate'  => Carbon::now()
            ]);
            if ($note) {
               return true;
            }
            throw new GeneralException('Oops! Something went wrong while creating note.');
        });
    }
}
