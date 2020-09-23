<?php
namespace App\Transformers\EventTransformer;

use LaraCrud\Helpers\TransformerAbstract;
use League\Fractal\ParamBag;
use App\Models\Tblevent;

class TbleventShortTransformer extends TransformerAbstract
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


	public function transform(Tblevent $tblevent)
	{
		$data= [
			"EID" => $tblevent->EID,
			"region_id" => $tblevent->region_id,
			"EventName" => $tblevent->EventName,
			"VID" => $tblevent->VID,
			"CfgID" => $tblevent->CfgID,
			"EventID" => $tblevent->EventID,
			"ClientId" => $tblevent->ClientId
		];
		return $this->filterFields($data);
	}
	
	
}