<?php


namespace IWS\Queues\Commands;

use Enqueue\Redis\RedisConnectionFactory;
use IWS\Queues\Services\CommonHelper;
use IWS\Queues\Services\Config;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EnqueueProducerCommand extends AbstractProducerCommand
{
    private $queueName;
    private $redisDsn;

    public function __construct(string $name = null)
    {
        parent::__construct($name);
        $this->queueName = Config::getInstance()->getConfigKey('queues')['enqueue'];
        $this->redisDsn = Config::getInstance()->getConfigKey('redis')['dsn'];

    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \Interop\Queue\Exception
     * @throws \Interop\Queue\Exception\InvalidDestinationException
     * @throws \Interop\Queue\Exception\InvalidMessageException
     * @throws \ReflectionException
     */
    public function runCommand(InputInterface $input, OutputInterface $output)
    {
        $messagesCount = 100/*$input->getArgument('count')*/;
        $delay = 1/*$input->getArgument('delay')*/;
        $factory = new RedisConnectionFactory($this->redisDsn);

        $this->getLogger()->info("Start enqueue producer. Loop: $messagesCount. Delay: $delay");

        for ($i = 0; $i <= $messagesCount; $i++) {
            $fakeMessage = CommonHelper::generateBunchFakeMessages(mt_rand(1, 5));

            $context = $factory->createContext();

            $queue = $context->createQueue($this->queueName);
            $message = $context->createMessage(json_encode($fakeMessage));

            $this->getLogger()->info('Sent message (size: ' . count($fakeMessage) . ') to queue');
            $context->createProducer()->send($queue, $message);

            sleep($delay);
        }

        $this->getLogger()->info('Finished');
        exit(1);
    }

    protected function configure()
    {
        $this->setDescription('Run enqueue/enqueue-bundle based producer')
//            ->addArgument(
//                'count',
//                InputArgument::OPTIONAL,
//                'Messages count to produce (default: 1000)',
//                1000
//            )->addArgument(
//                'delay',
//                InputArgument::OPTIONAL,
//                'Pause between messages, seconds (default 1)',
//                1
//            )
        ;
    }
}