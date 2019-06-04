<?php

namespace AppBundle\Rabbit\Producers;

use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;

class MyProducer implements ProducerInterface
{
    public function publish($msgBody, $routingKey = '', $additionalProperties = [])
    {
        echo 'hello';
    }
}