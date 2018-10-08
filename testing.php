<?php
require __DIR__.'/vendor/autoload.php';

$client = new \GuzzleHttp\Client([
	'base_uri' => 'http://localhost',
	'http_errors' => false
]);

$nickname = 'ObjectOrienter'.rand(0,999);
$data = array(
	'nickname' => $nickname,
	'avatarNumber' => 5,
	'tagLine' => '<?php'
);

//1) Post to create the programmer

$response = $client->post('/api/programmers', [
	'body' => json_encode($data)
]);

echo 'StatusCode: ' . $response->getStatusCode();
echo "\n";
echo 'Content-Type: ' . json_encode($response->getHeader('content-type'));
echo "\n";
echo 'Location: ' . $response->getHeader('Location')[0];
echo "\n\n\n\n";
echo $response->getBody();
echo "\n\n";
$programmerUrl = $response->getHeader('Location')[0];
//2) Get to fetch that programmer

$response = $client->get($programmerUrl);

echo 'StatusCode: ' . $response->getStatusCode();
echo "\n";
echo 'Content-Type: ' . json_encode($response->getHeader('content-type'));
echo "\n";
echo $response->getBody();
echo "\n\n";

//3) Get a collection
$response = $client->get('api/programmers');

echo 'StatusCode: ' . $response->getStatusCode();
echo "\n";
echo 'Content-Type: ' . json_encode($response->getHeader('content-type'));
echo "\n";
echo $response->getBody();
echo "\n\n";
