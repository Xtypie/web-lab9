<?php
namespace App;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class QueueManager
{
    private $channel;
    private $connection;
    private $queueName = 'student_queue';

    public function __construct()
    {
        $this->connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
        $this->channel = $this->connection->channel();
        $this->channel->queue_declare($this->queueName, false, true, false, false);
    }

    public function publish(array $data)
    {
        $msgBody = json_encode($data);
        $msg = new AMQPMessage($msgBody, [
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
        ]);
        $this->channel->basic_publish($msg, '', $this->queueName);
    }

    public function consume(callable $callback)
    {
        echo " [*] Waiting for messages. To exit press CTRL+C\n";
        $this->channel->basic_consume($this->queueName, '', false, true, false, false, function($msg) use ($callback) {
            $data = json_decode($msg->body, true);
            if ($data) {
                $callback($data);
            }
        });
        while($this->channel->is_consuming()) {
            $this->channel->wait();
        }
    }

    public function __destruct()
    {
        $this->channel->close();
        $this->connection->close();
    }
}