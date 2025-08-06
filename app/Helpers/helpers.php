<?php

use App\Models\Attendance;
use Carbon\Carbon;
use App\Models\Notification;
use App\Models\User; // Ensure you're importing the User model
use Illuminate\Support\Facades\Http;
use App\Models\City;

if (!function_exists('returnSuccess')) {
    function returnSuccess($message, $data = null)
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data
        ], 200);
    }
}
if (!function_exists('returnError')) {
    function returnError($message)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
        ], 200);
    }
}
if (!function_exists('returnErrorWithData')) {
    function returnErrorWithData($message, $data = null, $custom_code = null)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'custom_code' => $message,
            'data' => $data
        ], 200);
    }
}






function sendOtpEmail($email, $name, $otp, $url = null, $link_text = 'Confirm Email')
{
    $verifyUrl = $url;
    return Http::withHeaders([
        'api-key' => env('BREVO_API_KEY'),
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
    ])->post('https://api.brevo.com/v3/smtp/email', [
        'sender' => [
            'name' => 'Dress It',
            'email' => 'no-reply@dressitnow.com'
        ],
        'to' => [
            [
                'email' => $email,
                'name' => $name
            ]
        ],
        'templateId' => 1, // Replace with your template ID
        'params' => [
            'code' => $otp,
            'link_url' => $verifyUrl,
            'link_text' => $link_text
        ]
    ]);
}


function geoNearByPlaceName($lat, $lng)
{

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://api.geonames.org/findNearbyPlaceNameJSON?lat=' . $lat . '&lng=' . $lng . '&username=kulbir7485',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    $data = json_decode($response, true);
    if (isset($data['geonames'])) {
        foreach ($data['geonames'] as $item) {
            // Check if city already exists based on geoname_id
            $exists = City::where('geoname_id', $item['geonameId'])->exists();

            if (!$exists) {
                City::create([
                    'admin_code1'     => $item['adminCode1'],
                    'lng'             => $item['lng'],
                    'distance'        => $item['distance'],
                    'geoname_id'      => $item['geonameId'],
                    'toponym_name'    => $item['toponymName'],
                    'country_id'      => $item['countryId'],
                    'fcl'             => $item['fcl'],
                    'population'      => $item['population'],
                    'country_code'    => $item['countryCode'],
                    'name'            => $item['name'],
                    'fcl_name'        => $item['fclName'],
                    'admin_code_iso'  => $item['adminCodes1']['ISO3166_2'] ?? null,
                    'country_name'    => $item['countryName'],
                    'fcode_name'      => $item['fcodeName'],
                    'admin_name1'     => $item['adminName1'],
                    'lat'             => $item['lat'],
                    'fcode'           => $item['fcode'],
                ]);
            }
        }
    }
}

function geoNearByCities($lat, $lng)
{

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://api.geonames.org/findNearbyJSON?lat=' . $lat . '&lng=' . $lng . '&radius=80&username=kulbir7485',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
    ));

    $response = curl_exec($curl);

    curl_close($curl);

    $data = json_decode($response, true);
    if (isset($data['geonames'])) {
        foreach ($data['geonames'] as $item) {
            // Check if city already exists based on geoname_id
            $exists = City::where('geoname_id', $item['geonameId'])->exists();

            if (!$exists) {
                City::create([
                    'admin_code1'     => $item['adminCode1'],
                    'lng'             => $item['lng'],
                    'distance'        => $item['distance'],
                    'geoname_id'      => $item['geonameId'],
                    'toponym_name'    => $item['toponymName'],
                    'country_id'      => $item['countryId'],
                    'fcl'             => $item['fcl'],
                    'population'      => $item['population'],
                    'country_code'    => $item['countryCode'],
                    'name'            => $item['name'],
                    'fcl_name'        => $item['fclName'],
                    'admin_code_iso'  => $item['adminCodes1']['ISO3166_2'] ?? null,
                    'country_name'    => $item['countryName'],
                    'fcode_name'      => $item['fcodeName'],
                    'admin_name1'     => $item['adminName1'],
                    'lat'             => $item['lat'],
                    'fcode'           => $item['fcode'],
                ]);
            }
        }
    }
}
