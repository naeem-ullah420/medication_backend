<?php

namespace App\Http\Controllers;

use App\Models\DmAndDAmp;
use App\Models\DmAndDAmpp;
use App\Models\DmAndDBrowser;
use App\Models\DmAndDIngredient;
use App\Models\DmAndDVtm;
use App\Models\DrugsAndMedicalProduct;
use Illuminate\Http\Request;

class DrugsAndMedicalProductController extends Controller
{
    public function start_creating_responses() {
        // ssh root@54.92.83.143
        $dmd_vtms = DmAndDVtm::query()
        // ->where('_id', '653827914eecf5459417132e')
        ->get();

        foreach($dmd_vtms as $vtm) {
            $payload = [];
            self::prepare_vtm_detail($payload, $vtm);
            self::prepare_vmps_detail($payload, $vtm);
            // return $payload;
            DrugsAndMedicalProduct::create($payload);
        }
    }

    public function prepare_vtm_detail(&$payload, $vtm) {
        $payload['vtm_detail'] = [
            'vtm_initial_detail' => data_get($vtm, 'vtm_detail'),
            'names'              => data_get($vtm, 'name_details'),
            'snomed_codes'       => data_get($vtm, 'snomed_codes'),
        ];
    }

    public function prepare_vmps_detail(&$payload, $vtm) {
        $dmd_vmps = data_get($vtm, 'vmps_detail') ?: [];

        $vmps_payload = [];
        foreach($dmd_vmps as $vmp) {
            $db_vmp = DmAndDBrowser::where('full_name', data_get($vmp, 'name'))->first()?->toArray();
            self::prepare_ingredients_detail($db_vmp, $db_vmp);
            self::prepare_amps_detail($db_vmp, $db_vmp);
            array_push($vmps_payload, $db_vmp);
        }

        $payload['vtm_detail']['vmps_details'] = $vmps_payload;
    }

    public function prepare_ingredients_detail(&$payload, $vmp) {
        $vmp_ingredients = data_get($vmp, 'vmp_ingredients') ?: [];

        $vmps_ingredients_payload = [];
        foreach($vmp_ingredients as $ingredient) {
            $db_vmp_ingredient = DmAndDIngredient::where('full_name', data_get($ingredient, 'name'))->first()?->toArray();
            $db_vmp_ingredient = $db_vmp_ingredient ?: $ingredient;
            array_push($vmps_ingredients_payload, $db_vmp_ingredient);
        }

        $payload['vmp_ingredients'] = $vmps_ingredients_payload;
    }

    public function prepare_amps_detail(&$payload, $vmp) {
        // drug collection
        // {"vtm_detail.vtm_initial_detail.vtm_title":"Abatacept"}
        // $db_amps = DmAndDAmp::where('parent_products.vmp_value', data_get($vmp, 'full_name'))->get()?->toArray();
        $db_amps = DmAndDAmp::where('amps_detail.vmp_title', data_get($vmp, 'full_name'))->get()?->toArray();
        $amps_payload = [];

        foreach($db_amps as $amp) {
            self::prepare_ampps_detail($amp, $amp);
            array_push($amps_payload, $amp);
        }

        $payload['amps_detail'] = $amps_payload;
    }


    public function prepare_ampps_detail(&$payload, $amp) {
        $ampps_detail = data_get($amp, 'ampps_detail') ?: [];

        $amps_ampps_payload = [];
        foreach($ampps_detail as $ampps) {
            $db_vmp_ampps = DmAndDAmpp::where('ampps_detail.ampp_url', data_get($ampps, 'ampp_url'))->first()?->toArray();
            $db_vmp_ampps = $db_vmp_ampps ?: $ampps;
            array_push($amps_ampps_payload, $db_vmp_ampps);
        }

        $payload['ampps_detail'] = $amps_ampps_payload;
    }

}
