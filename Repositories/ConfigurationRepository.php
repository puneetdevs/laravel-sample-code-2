<?php

namespace App\Repositories;


use DB;
use App\Exceptions\Handler;
use App\Repositories\BaseRepository;
use App\Models\Configuration;
use App\Models\ConfigurationRates;
use DateTime;
use Carbon\Carbon;
/**
 * Class NotesRepository.
 */
class ConfigurationRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return Configuration::class;
    }

    /**
     * @param $email
     *
     * @return User
    */
    function addRateConfiguration($data, $configID){
        $data['CfgID'] = $configID;
        $ConfigurationRates = ConfigurationRates::create($data);
        if ($ConfigurationRates) {
            return $ConfigurationRates;
        }
        return false;
    }

    public function getRateConfigurations($configuration_id){
        $configurations = ConfigurationRates::with('postion')->where('CfgID', $configuration_id);
        return $configurations;
    }

    public function SingleRateConfiguration($configuration_id, $id){
        $RateConfiguration =  ConfigurationRates::with('postion')->where([
            'CfgID'=>$configuration_id,
            'ID'=>$id
        ])->first();
        return $RateConfiguration;
    }

    public function updateRateConfiguration($configuration_id, $id, $update_data){
        return ConfigurationRates::where([
            'CfgID'=>$configuration_id,
            'ID'=>$id
        ])->update($update_data);
    }
    public function deleteRateConfiguration($configuration_id, $id){
        return ConfigurationRates::where([
            'CfgID'=>$configuration_id,
            'ID'=>$id
        ])->delete();
    }
}
