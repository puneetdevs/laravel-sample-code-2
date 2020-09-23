<?php

namespace App\Repositories;


use DB;
use App\Exceptions\Handler;
use App\Repositories\BaseRepository;
use App\Models\TbleventsInvoicelineitem;
use DateTime;
use Carbon\Carbon;
/**
 * Class LineItemRepository.
 */
class LineItemRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return TbleventsInvoicelineitem::class;
    }
    
    
}
