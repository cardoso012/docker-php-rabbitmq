<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

require __DIR__ . '/vendor/autoload.php';

$connection = new AMQPStreamConnection('rabbitmq', $_ENV['RABBITMQ_PORT'], $_ENV['RABBITMQ_USER'], $_ENV['RABBITMQ_PASS']);
$channel = $connection->channel();

echo '######### PRODUTOR #########' . PHP_EOL;
echo PHP_EOL;

$channel->queue_declare('custom.myqueue', false, true, false, false);

for( $i=1; $i < 6; $i++ ){

  $json = [
    "msg" => "Hello World {$i}"
  ];
  
  $msg = new AMQPMessage(json_encode($json));

  $channel->basic_publish($msg, 'custom.myexchange', 'test123');
  
  echo "[x] Sent 'JSON message!'\n";

}

$channel->close();
$connection->close();