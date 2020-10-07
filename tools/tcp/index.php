<?php
namespace MyApp;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

require __DIR__ . '/vendor/autoload.php';
class Chat implements MessageComponentInterface {
	protected $clients;

	public function __construct() {
		$this->clients = new \SplObjectStorage;
	}

	public function onOpen(ConnectionInterface $conn) {
		print('connection established');

		var_dump($conn->remoteAddress);
	}

	public function onMessage(ConnectionInterface $from, $msg) {
		// $fp = fopen('data.txt', 'a');
		// fwrite($fp, '-------');
		// fwrite($fp, $msg);
		// fwrite($fp, '------- from ');
		// fclose($fp);

		$this->apiPOST($msg);


		$from->send('message received');


	}

	public function onClose(ConnectionInterface $conn) {
	}

	public function onError(ConnectionInterface $conn, \Exception $e) {
	}

	public function apiPOST($msg) {
		// $fp = fopen('data.txt', 'a');
		// fwrite($fp, '-------');
		// fwrite($fp, $msg);
		// fwrite($fp, '------- from ');
		// fclose($fp);
		
		$ch = curl_init();
		// $url = 'http://'. $_REQUEST['SERVER_NAME'] .':'.$_REQUEST['SERVER_PORT']. '/ADT/tools/api';
		$url = 'http://localhost:81/ADT/tools/api';
		// set the url, number of POST vars, POST data
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_POST, count($msg));
		curl_setopt($ch,CURLOPT_POSTFIELDS, $msg);

		// execute post
		$result = curl_exec($ch);

//close connection
		curl_close($ch);
	}
}



use Ratchet\Server\IoServer;
use MyApp\Chat;

    // require dirname(__DIR__) . '/vendor/autoload.php';

$server = IoServer::factory(
	new Chat(),
	6671
);
print('server started');

$server->run();


?>