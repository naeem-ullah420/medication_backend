<?php

use App\Models\BnfDetail;
use App\Models\DmAndDBrowser;
use App\Models\DmAndDIngredient;
use App\Models\MedicationResponse;
use App\Models\NafdacProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


function get_sample_payload() {
    return [
        'resourceType' => 'Medication',
        'id' => null,
        'contained' => [],
        'code' => [],
        'marketingAuthorizationHolder' => [],
        'doseForm' => [],
        'ingredient' => [],
        'definition' => [],
    ];
}

function change_type_and_lower_case($string, $case = "_") {
    return strtolower(str_replace(" ", $case, $string));
}

Route::get('/test', function (Request $request) {

    $nafdact_products = NafdacProduct::get();

    foreach($nafdact_products as $product) {
        $sample_payload = get_sample_payload();
        $detail_object  = data_get($product, 'detail_data');
        $atc_code       = data_get($product, 'detail_data.atc_code');

        $dm_and_d_detail = DmAndDBrowser::where('atc_codes.ATC Code', $atc_code)->get();

        // id
        $sample_payload['id'] = change_type_and_lower_case(Str::random(25) . '_' . data_get($product, 'product_name'));

        // contained
        $medicine_manufacturer = data_get($detail_object, 'manufacturer_name');
        $sample_payload['contained'] = $medicine_manufacturer ? [
            [
                'id'           => Str::random(25),
                'name'         => $medicine_manufacturer,
                'resourceType' => 'Organization',
            ]
        ] : [];

        // code
        $sample_payload['code'] = [
            'coding' => get_all_type_of_codes($product, $dm_and_d_detail),
        ];


        // marketingAuthorizationHolder
        $sample_payload['marketingAuthorizationHolder'] = $sample_payload['contained'] ? [
            'reference' => '#' . data_get($sample_payload['contained'], '0.id')
            ] : (object) [];

        // doseForm
        $sample_payload['doseForm'] = [
            'coding' => get_dose_form($dm_and_d_detail),
        ];

        // ingredient
        $sample_payload['ingredient'] = get_all_ingredients_data($product, $dm_and_d_detail);

        // definition
        $sample_payload['definition'] = [
            'reference' => 'MedicationKnowledge/medicationknowledge-'.change_type_and_lower_case(data_get($product, 'product_name'), '-'),
        ];

        // return $sample_payload;
        MedicationResponse::insert($sample_payload);
    }
});

function get_all_ingredients_data($product, $dm_and_d_detail) {
    $response = [];

    foreach($dm_and_d_detail as $detail) {
        $ingredients = data_get($detail, 'vmp_ingredients') ?? [];
        if($ingredients) {
            foreach($ingredients as $ingredient) {
                $ingredient_name = data_get($ingredient, 'name');
                $numerator       = explode(' ', data_get($ingredient, 'strength_and_uom_(nmr)'));
                $denominator     = explode(' ', data_get($ingredient, 'strength_and_uom_(dmr)'));

                $data['item'] = [
                    'concept' => (object) [
                        'coding' => get_ingredient_codings($detail, $ingredient),
                    ]
                ];
                $data['strengthRatio'] = [

                    'numerator' => [
                        'value' => @$numerator[0],
                        'system' => data_get($detail, 'vmp_detail.vmp_url'),
                        'code' => @$numerator[1],
                    ],
                    'denominator' => [
                        'value' => @$denominator[0],
                        'system' => data_get($detail, 'vmp_detail.vmp_url'),
                        'code' => @$denominator[1],
                    ],
                ];

                array_push($response, $data);
            }
        }
    }

    return $response;
}

function get_ingredient_codings($detail, $ingredient) {
    $response = [];
    $ingredient_name = data_get($ingredient, 'name');
    $codes = DmAndDIngredient::where('full_name', $ingredient_name)->first();

    foreach(data_get($codes, 'snomed_codes') ?: [] as $code) {
        array_push($response, [
            "system"  => data_get($detail, 'vmp_detail.vmp_url'),
            "display" => $ingredient_name,
            "code"    => data_get($code, 'code'),
        ]);
    }

    return $response;
}

function get_dose_form($dm_and_d_detail) {

    $response = [];

    foreach($dm_and_d_detail as $detail) {
        array_push($response, [
            'display' => data_get($detail, 'product_summary.Form') . '(' . data_get($detail, 'product_summary.Route') . ')',
        ]);
    }

    return $response;
}




function get_all_type_of_codes($product, $dm_and_d_detail) {
    $response = [];
    $product_name = data_get($product, 'product_name');

    if(data_get($product, 'detail_data.atc_code')) {
        array_push($response, [
            "system"  => data_get($product, 'nrn_url'),
            "type"    => "atc_code",
            "display" => $product_name,
            "code"    => data_get($product, 'detail_data.atc_code'),
        ]);
    }

    foreach($dm_and_d_detail as $detail) {
        if(data_get($detail, 'snomed_codes')) {
            foreach(data_get($detail, 'snomed_codes') ?? [] as $code_detail) {
                array_push($response, [
                    "system"  => data_get($detail, 'vmp_detail.vmp_url'),
                    "type"    => "snomed_code",
                    "display" => $product_name,
                    "code"    => data_get($code_detail, 'code'),
                ]);
            }
        }

        if(data_get($detail, 'bnf_codes')) {
            foreach(data_get($detail, 'bnf_codes') ?? [] as $code_detail) {
                array_push($response, [
                    "system"  => data_get($detail, 'vmp_detail.vmp_url'),
                    "type"    => "bnf_code",
                    "display" => $product_name,
                    "code"    => data_get($code_detail, 'bnf'),
                ]);
            }
        }
    }



    return $response;
}
