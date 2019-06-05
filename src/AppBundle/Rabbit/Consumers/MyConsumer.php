<?php

namespace AppBundle\Rabbit\Consumers;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Class MyConsumer
 * @package App\Rabbit\Consumers
 */
class MyConsumer implements ConsumerInterface
{
    /**
     * @param AMQPMessage $msg
     * @return mixed|void
     */
    public function execute(AMQPMessage $msg)
    {
        echo $msg->body;
    }
}