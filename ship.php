<?php

$token      = "token.token";
$input      = file_get_contents("php://input");
$request    = json_decode($input, true);

if (isset($request['event'])) {
    if ($request['event'] === 'order.shipment.creating') {

        $shipment_id = $request['data']['shipments'][0]['id'];
        createShipment($shipment_id,$token);
    } else if ($request['event'] === 'order.shipment.return.creating') {
        
        $shipment_id = $request['data']['shipments'][0]['id'];
        //createReturnShipment($shipment_id,$token);
    } else if ($request['event'] === 'order.shipment.cancelled') {
        
        $shipment_id = $request['data']['shipping_number'];
        //cancelShipment($shipment_id,$token);
    }
} else {
    header("Content-Type: application/json");
    http_response_code(400);
    echo json_encode(['error' => 'Invalid event type']);
}

function createShipment ($shipment_id,$token){
    $awb        = rand(10000000000, 99999999999);
    $payload    = [
        'tracking_link' => "https://labels.com/labels/".$awb."/track",
        'shipment_number' => "$awb",
        'tracking_number' => "$awb",
        'status' => "created",
        'pdf_label' => "",
        'cost' => 25
    ];
        
    $url = "https://api.salla.dev/admin/v2/shipments/".$shipment_id;

    $server_output = sendCurlRequest("PUT", $url, $payload);
    echo $server_output;
}

function sendCurlRequest($method, $url, $data) {
    $headers = array(
            "Authorization: Bearer ".$token,
            "Content-Type: application/json"
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $server_output = curl_exec($ch);
    curl_close($ch);
    return $server_output;
}
?>




