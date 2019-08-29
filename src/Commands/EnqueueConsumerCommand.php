<?php


namespace IWS\Queues\Commands;


use Enqueue\Redis\RedisConnectionFactory;
use IWS\Queues\Services\Config;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EnqueueConsumerCommand extends AbstractConsumerCommand
{
    private $queueName;
    private $redisDsn;

    public function __construct(string $name = null)
    {
        parent::__construct($name);
        $this->queueName = Config::getInstance()->getConfigKey('queues')['enqueue'];
        $this->redisDsn = Config::getInstance()->getConfigKey('redis')['dsn'];
    }

    protected function runCommand(InputInterface $input, OutputInterface $output)
    {
        $this->getLogger()->info('Starting enqueue consumer');
        $factory = new RedisConnectionFactory($this->redisDsn);
        $context = $factory->createContext();

        $fooQueue = $context->createQueue($this->queueName);
        $consumer = $context->createConsumer($fooQueue);


        $message = $consumer->receive();
        $consumer->acknowledge($message);

        $messages = json_decode($message->getBody());
        foreach ($messages as $fakeMessage) {
            $this->getLogger()->info('Processing message... ' . json_encode($fakeMessage));
            sleep(2);
        }

        $this->getLogger()->info('Waiting for new one message');
    }

    protected function configure()
    {
        $this->setDescription('Run enqueue/enqueue-bundle based consumer');
    }
}