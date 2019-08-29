<?php


namespace IWS\Queues\Commands;

use Enqueue\Redis\RedisConnectionFactory;
use IWS\Queues\Services\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EnqueueStatsCommand extends Command
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
    public function execute(InputInterface $input, OutputInterface $output)
    {

        $factory = new RedisConnectionFactory($this->redisDsn);
        $context = $factory->createContext();
        $redis = $context->getRedis();

        $output->writeln('Enqueue info');
        $output->writeln('dsn:' . $this->redisDsn);
        $output->writeln('queue: ' . $this->queueName);
        $output->writeln('redis queue type: ' . $redis->eval("return redis.call('type','$this->queueName').ok"));
        $output->writeln('redis queue len: ' . $redis->eval("return redis.call('llen','$this->queueName')"));

        $output->writeln('Queue delayed');
        $output->writeln('queue: ' . $this->queueName . ":delayed");
        $output->writeln('queue len: ' . $redis->eval("return redis.call('llen','$this->queueName:delayed'"));
        $output->writeln('Queue reserved');
        $output->writeln('queue: ' . $this->queueName . ":reserved");
        $output->writeln('queue len: ' . $redis->eval("return redis.call('llen','$this->queueName:reserved'"));;
    }

    protected function configure()
    {
        $this->setDescription('Stats for enqueue/enqueue-bundle');
    }
}