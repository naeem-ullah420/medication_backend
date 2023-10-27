<?php

namespace App\Http\Controllers;

use App\Models\MedicationKnowledge;
use App\Models\MedicationResponse;
use App\Models\WhoccNoData;
use Illuminate\Http\Request;

class MedicationKnowledgeController extends Controller
{
    public function start_creating_responses(){
        $whocc_no_data = WhoccNoData::get();

        foreach($whocc_no_data as $detail) {
            $product_name = data_get($detail, 'product_name');
            $medication_knowledge_id = 'MedicationKnowledge/medicationknowledge-'.$this->change_type_and_lower_case($product_name, '-');

            $medication =  MedicationResponse::query()
            ->where("definition.reference", $medication_knowledge_id)
            ->where("code.coding.code", data_get($detail, 'atc_code'))
            ->first();

            $payload_to_insert = [
                'resourceType' => 'MedicationKnowledge',
                'id' => $medication_knowledge_id,
                'code' => [
                    'coding' => data_get($medication, 'code.coding') ?: [],
                    'text'   => $product_name,
                ],
                'status' => @$detail->status,
                'medicineClassification' => [
                    [
                        'classification' => [
                            'coding' => $this->prepare_medicine_classification_coding_data($detail),
                        ]
                    ]
                ]
            ];

            MedicationKnowledge::insert($payload_to_insert);
        }
    }

    public function prepare_medicine_classification_coding_data($whocc_no_data){
        $coding_details = data_get($whocc_no_data, 'codes_detail') ?: [];
        $coding_details = array_map(function ($i) {
            return [
                'system'  => data_get($i, 'link'),
                'display' => data_get($i, 'title'),
                'code'    => data_get($i, 'code'),
            ];
        }, $coding_details);
        return $coding_details;
    }

    public function change_type_and_lower_case($string, $case = "_") {
        return strtolower(str_replace(" ", $case, $string));
    }
}
