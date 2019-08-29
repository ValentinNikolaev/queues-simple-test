<?php
require_once __DIR__ . '/vendor/autoload.php';

use AlexS\SignalHelper;
use IWS\Queues\Commands\{EnqueueConsumerCommand,
    EnqueueProducerCommand,
    EnqueueStatsCommand,
    MessengerConsumerCommand,
    MessengerProducerCommand};
use Symfony\Component\Console\Application;

$application = new Application();
$application->getHelperSet()->set(new SignalHelper());

$application->add(new EnqueueConsumerCommand('consumer:enqueue'));
$application->add(new EnqueueProducerCommand('producer:enqueue'));
$application->add(new EnqueueStatsCommand('stats:enqueue'));
$application->add(new MessengerConsumerCommand('consumer:messenger'));
$application->add(new MessengerProducerCommand('producer:messenger'));
$application->add(new \IWS\Queues\Commands\MessengerStatsCommand('stats:messenger'));

$application->run();