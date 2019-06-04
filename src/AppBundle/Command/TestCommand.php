<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class TestCommand
 * @package AppBundle\Command
 */
class TestCommand extends ContainerAwareCommand
{
    /**
     * @var string
     */
    protected static $defaultName = 'app:test';

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $producer = $this->getContainer()->get('old_sound_rabbit_mq.test_producer');

        $producer->publish('test message');
    }
}