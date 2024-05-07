<?php 

/**
 Requires libcurl
 Replace $client_id, $client_secret, and $shipping_number with your's generated form the UPS API @ developer.ups.com
 
 */

$client_id = '<YOUR CLIENT ID>';
$client_secret = '<YOUR CLIENT SECRET>';
$shipping_number = '<YOUR SHIPPER NUMBER>';
$Combineuserandpassword = $client_id . ':' . $client_secret;


$curl = curl_init();

$payload = "grant_type=client_credentials";

curl_setopt_array($curl, [
  CURLOPT_HTTPHEADER => [
    "Content-Type: application/x-www-form-urlencoded",
    "Authorization: Basic " . base64_encode($Combineuserandpassword)
  ],
  CURLOPT_POSTFIELDS => $payload,
  CURLOPT_URL => "https://wwwcie.ups.com/security/v1/oauth/token", // change wwwcie to onlinetools when ready to switch to production
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_CUSTOMREQUEST => "POST",
]);

$response = curl_exec($curl);
$error = curl_error($curl);

curl_close($curl);

if ($error) {
  echo "cURL Error #:" . $error;
} else {
  // echo $response;
}

$responseArray = json_decode($response, true);

$accessToken = $responseArray['access_token'];

const version = "v2403";
$query = array(
  "additionaladdressvalidation" => "string"
);

$curl = curl_init();

$payload = array(
  "ShipmentRequest" => array(
    "Request" => array(
      "SubVersion" => "1801",
      "RequestOption" => "nonvalidate",
      "TransactionReference" => array(
        "CustomerContext" => ""
      )
    ),
    "Shipment" => array(
      "Description" => "Test",
      "Shipper" => array(
        "Name" => "Test",
        "AttentionName" => "Test",
        "TaxIdentificationNumber" => "",
        "Phone" => array(
          "Number" => "5555555555",
          "Extension" => " "
        ),
        "ShipperNumber" => "$shipping_number",
        "FaxNumber" => "",
        "Address" => array(
          "AddressLine" => array(
			  "123 Main St."
		  ),
          "City" => "Austin",
          "StateProvinceCode" => "TX",
          "PostalCode" => "78746",
          "CountryCode" => "US"
        )
      ),
      "ShipTo" => array(
        "Name" => "Test",
        "AttentionName" => "ATTN: Test",
        "Phone" => array(
          "Number" => "5555555555"
        ),
        "Address" => array(
          "AddressLine" => array(
            "Line 1", "Line 2"
          ),
          "City" => "Austin",
          "StateProvinceCode" => "TX",
          "PostalCode" => "78746",
          "CountryCode" => "US"
        ),
        "Residential" => " "
      ),
      "ShipFrom" => array(
        "Name" => "Test",
        "AttentionName" => "ATTN: Test",
        "Phone" => array(
          "Number" => "5555555555"
        ),
        "FaxNumber" => "1234567890",
        "Address" => array(
          "AddressLine" => array(
			  "123 Main St."
		  ),
          "City" => "Austin",
          "StateProvinceCode" => "TX",
          "PostalCode" => "78746",
          "CountryCode" => "US"
        )
      ),
      "PaymentInformation" => array(
        "ShipmentCharge" => array(
          "Type" => "01",
          "BillShipper" => array(
            "AccountNumber" => "$shipping_number"
          )
        )
      ),
      "Service" => array(
        "Code" => "03", // See other shipping code options in UPS Developer portal - '03' = Ground
        "Description" => "Ground"
      ),
      "Package" => array(
        "Description" => "Stuff",
        "Packaging" => array(
          "Code" => "02",
          "Description" => "Stuff"
        ),
        "Dimensions" => array(
          "UnitOfMeasurement" => array(
            "Code" => "IN",
            "Description" => "Inches"
          ),
          "Length" => "8",
          "Width" => "8",
          "Height" => "8"
        ),
        "PackageWeight" => array(
          "UnitOfMeasurement" => array(
            "Code" => "LBS",
            "Description" => "Pounds"
          ),
          "Weight" => "3"
        )
      )
    ),
    "LabelSpecification" => array(
      "LabelImageFormat" => array(
        "Code" => "GIF",
        "Description" => "GIF"
      ),
      "HTTPUserAgent" => "Mozilla/4.5"
    )
  )
);

curl_setopt_array($curl, [
  CURLOPT_HTTPHEADER => [
    "Authorization: Bearer " . $accessToken,
    "Content-Type: application/json",
    "transId: string",
    "transactionSrc: testing"
  ],
  CURLOPT_POSTFIELDS => json_encode($payload),
  CURLOPT_URL => "https://wwwcie.ups.com/api/shipments/" . version . "/ship?" . http_build_query($query), // change wwwcie to onlinetools when ready to switch to production
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_CUSTOMREQUEST => "POST",
]);

$response = curl_exec($curl);
$error = curl_error($curl);

curl_close($curl);

if ($error) {
  echo "cURL Error #:" . $error;
} else {
  // echo $response;
}

$shipResponseArray = json_decode($response, true);

$label = $shipResponseArray['ShipmentResponse']['ShipmentResults']['PackageResults'][0]['ShippingLabel']['GraphicImage'];

echo("<img src='data:image/gif;base64,$label' height='392' width='651' />");

?>
