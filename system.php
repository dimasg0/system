<?php 
//проверка полученыых данных
echo "<ul style=\"list-style-type: none;\">";
  echo "<li>Firstname: $fname</li>";
  echo "<li>Lastname: $lname</li>";
  echo "<li>Phone: $phone</li>";
  echo "<li>Email: $email</li>";
  echo "<li>IP: $ip</li>";
  echo "<li>ClickID: $click_id</li> ";
  echo "<li>UserAgent: $user_agent</li> ";
  echo "<li>URL: $URL</li>";
   echo "<li>======================</li>";
echo "</ul>";

//глобальная переменная для получения результата поиска нужнной ссылки, изначально False(не найдено)
$global_res = FALSE;

//Данные для D (данные с send.php)
//проверяем получили ли мы значения с с send.php, если нет, ставим пустое поле
if (isset($apiToken)) {
   $thisApiToken = $apiToken;  
} else $thisApiToken = "";

if (isset($flowHash)) {
   $thisFlowHash = $flowHash;  
} else $thisFlowHash = "";

if (isset($landingName)) {
   $thisLandingName = $landingName;  
} else $thisLandingName = "";


//$thisApiToken = $apiToken; 
//$thisFlowHash = $flowHash;
//$thisLandingName = $landingName;

//данные с send.php - передаем в переменную $system в send.php
$country = $system[0];
$name = $system[1];
$broker = $system[2];

//проверяем данные с send.php
//echo $country . " " . $name . " " . $broker;

//cоздаем массив ссылок
$links = [ ["D", "https://cryp.im/api/v1/web/conversion"], 
           ["O", "http://api-3580-per-day.com"], 
           ["N", "https://savage-media10.com/api/lead"]];
           
//убераем ссылку с которой пришли
$max = sizeof($links);
for ($i = 0; $i <= $max; $i++){ 
  if($URL == $links[$i][1]) { 
    unset($links[$i]);
    break;
  }
}

//создание объекта под каждую систему
$apiDataD = [
  'flow_hash' => $thisFlowHash, // обязательный
  'landing' => $thisLandingName, // обязательный
  'first_name' => $fname, // обязательный
  'last_name' => $lname, // обязательный
  'email' => $email, // обязательный
  'phone' => $phone, // обязательный
  'ip' => $ip, //$_SERVER['REMOTE_ADDR'], // обязательный
  'click_id' => $click_id,
  'user_agent' => $user_agent
];
$apiDataO = [
  "action" => "create",
  'firstName' => $fname, // обязательный
  'lastName' => $lname,
  'email' => $email,
  'phoneNumber' => $phone, 
  "countryCode" => $country,//
  'ip' => $ip, // обязательный
  "password" => "paSswD123",
  "browser_language" => $country,
  "landing_url" => $name,
  "town" => $country,
  'sub1' => $click_id,
  "sub2" => "sub2",
  "sub3" => "sub3",
  "sub4" => "sub4",
  "sub5" => "sub5",
  "pub_id" => 30942
];
$apiDataN = [
  'full_name' => $fname . ' ' . $lname,
  'email' => $email,
  'phone' => $phone,
  'ip' => $ip, //$_SERVER['REMOTE_ADDR'],
  'landing' => 'https://aminebouras.com/g7jTnv',
  'source' => 'Uniq-Team',
  'country' => $country,
  'page' => $country . " " . $broker . " " . $name,
  'vertical' => 'Forex',
  'campaign' => 'EN',
  'description' => $click_id
];

//true = 1 false = 0
//функции для проверики, есть ли пользователь в данной системе
//передаем нужный объект и нужную ссылку
function checkD($apiDataD, $URL, $thisApiToken) { 
$headers = array();
$headers[] = "Authorization: Bearer $thisApiToken";
$headers[] = 'Content-Type: application/json';

$ch = curl_init($URL);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($apiDataD));
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$jsonResult = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

$result = json_decode($jsonResult, true);

var_dump($result);
return ($result['message'] == "Registration duplicated" or $result['error']) ? 0 : 1;
}

function checkO($apiDataO, $endpoint) {
$postdata = json_encode($apiDataO);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $endpoint);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
"Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$output = curl_exec($ch);

$array = json_decode($output, true);

var_dump($array);

$urlredirect = $array['autologin']['link'];
$error = $array['status'];

file_put_contents('log.txt', var_export([
  'request' => json_encode($apiDataO, JSON_PRETTY_PRINT),
    'response' => json_encode($array, JSON_PRETTY_PRINT),
  ], true), FILE_APPEND);
  
return ($error == "success") ? 1 : 0; 
}

function checkN($apiDataN, $URL) { 
$headers = array();
$headers[] = 'Content-Type: application/x-www-form-urlencoded';

$ch = curl_init($URL);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($apiDataN));
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$jsonResult = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

$result = json_decode($jsonResult, true);
$error = $result['result'];
var_dump($result);
curl_close($ch);

return ($result['message'] == "Registration duplicated" or $result['error']) ? 0 : 1;
}
 
function linkFound($brokers) {
     $global_res = TRUE;
      $br = $brokers;
      goto next;
}

$br = null;
$max = sizeof($links);

//проверям каждую ссылку и отправляем туда пользователя
for($i = 0; $i <= $max; $i++) {
  echo " Broker:  " . $links[$i][0] . " ";
  switch($links[$i][0]) {
    case "D": 
      if(checkD($apiDataD, $links[$i][1], $thisApiToken) == 1) {
        linkFound("D");
        break;
      } else continue;
      break;
    case "O": 
      if(checkO($apiDataO, $links[$i][1]) == 1) {
        linkFound("O");
        break;
      } else continue;
      break; 
    case "N": 
      if(checkN($apiDataN, $links[$i][1]) == 1) {
        linkFound("N");
        break;
      } else continue;
      break;
    default: 
        linkFound("none");
        break;
  }
}

next: 
echo "br = " . $br . " global_res = " . $global_res;
die;
?>
