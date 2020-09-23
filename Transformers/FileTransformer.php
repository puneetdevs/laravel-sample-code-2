<?php
namespace App\Transformers;

use LaraCrud\Helpers\TransformerAbstract;
use League\Fractal\ParamBag;
use App\Models\File;



class FileTransformer extends TransformerAbstract
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


    public function transform(File $file)
    {
       
        $data= [
			"id" => $file->id,
			"path" => $file->path,
			"name" => $file->name,
            "file_type" => $file->file_type,
            "object_type" => $file->object_type,
            "object_id" => $file->object_id,
            "upload_by" => $file->upload_by,
			"created_at" => $file->created_at,
			"updated_at" => $file->updated_at,

        ];
        return $this->filterFields($data);
    }

    
}