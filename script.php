<?php
$file = 'file.zip';

$path = pathinfo(realpath($file), PATHINFO_DIRNAME);

$zip = new ZipArchive;
$res = $zip->open($file);
if ($res === TRUE) {  
  $zip->extractTo($path);
  $zip->close();
  unlink($file);
}

if (!isset($_POST['firstZip']) ||!isset($_POST['secondZip'])|| $_POST['firstZip'] == ''|| $_POST['secondZip'] == '' ) {
	echo json_encode(['status' => 'error']);
	die();
}


function getLocationFromZip($zip)
{

    $fh = fopen('ip2location_db9.csv', 'r');
    while ($line = fgets($fh)) {
        $pattern = sprintf('!"%d"\s!', $zip);
        if (preg_match($pattern, $line,$result))
        {
            $result = str_replace('"', '', $line);
            $result = explode(',', $result);
            return $result;
        }
    }

    echo json_encode(['status' => 'error', 'message' =>  'No such zip']);
    die();
}

function vincentyGreatCircleDistance($firstZip, $secondZip)
{
	$to = getLocationFromZip($firstZip);
	$from = getLocationFromZip($secondZip);
	$latitudeFrom =$from[2];
	$longitudeFrom = $from[3];
	$latitudeTo = $to[2];
	
	$longitudeTo = 	$to[3];
	$earthRadius = 6371000;
	$latFrom = deg2rad($latitudeFrom);	
	$lonFrom = deg2rad($longitudeFrom);
	$latTo = deg2rad($latitudeTo);
	$lonTo = deg2rad($longitudeTo);

	$lonDelta = $lonTo - $lonFrom;
	$a = pow(cos($latTo) * sin($lonDelta), 2) +
	pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
	$b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

	$angle = atan2(sqrt($a), $b);
	return json_encode(['distance' => $angle * $earthRadius, 'firstCountry' => $to[0],'firstCity' => $to[1], 'secondCountry' => $from[0],'secondCity' => $from[1]]);
}
echo vincentyGreatCircleDistance($_POST['firstZip'],$_POST['secondZip']);

