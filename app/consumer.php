<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exception\AMQPProtocolChannelException;

require_once __DIR__ . '/vendor/autoload.php';

echo '######### CONSUMIDOR #########' . PHP_EOL;
echo PHP_EOL;

try{

  $connection = new AMQPStreamConnection('rabbitmq', $_ENV['RABBITMQ_PORT'], $_ENV['RABBITMQ_USER'], $_ENV['RABBITMQ_PASS']);
  $channel = $connection->channel();
  
  $exchange     = 'custom.myexchange';
  $queueName    = 'custom.myqueue';
  $typeExchange = 'direct';
  
  $channel->queue_declare($queueName, false, true, false, false);

  $callback = function($msg) {
    echo ' [x] Received ', $msg->body, "\n";
  };
  
  $channel->basic_qos(null, 1, null);
  $channel->basic_consume($queueName, '', false, true, false, false, $callback);

  while ($channel->is_open()) {
    $channel->wait();
  }

  $channel->close();
  $connection->close();

}catch(AMQPProtocolChannelException $e){
  echo $e->getMessage() . PHP_EOL;
}