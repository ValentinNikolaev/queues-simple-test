<?php


namespace IWS\Queues\Commands;

use Enqueue\Redis\RedisConnectionFactory;
use IWS\Queues\Services\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\Transport\RedisExt\Connection;

class MessengerStatsCommand extends Command
{
    private $queueName;
    private $redisDsn;
    private $redisMessengerDsn;

    public function __construct(string $name = null)
    {
        parent::__construct($name);
        $this->queueName = Config::getInstance()->getConfigKey('queues')['messenger'];
        $this->redisDsn = Config::getInstance()->getConfigKey('redis')['dsn'];
        $this->redisMessengerDsn = Config::getInstance()->getConfigKey('redis')['dsn_messenger'];

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


        $connection = Connection::fromDsn($this->redisMessengerDsn,
            ['serializer' => 2, 'auto_setup' => false, 'stream_max_entries' => 20000]);


        $output->writeln('Messenger info');
        $output->writeln('dsn:' . $this->redisMessengerDsn);
        $output->writeln('queue: ' . $this->queueName);
        $output->writeln('queue type: ' . $redis->eval("return redis.pcall('type','$this->queueName').ok"));
        $output->writeln('queue len: ' . $redis->eval("return redis.call('XLEN','$this->queueName')"));
        /** GROUPS */
        $xinfoGroups = "local t = redis.pcall(\"XINFO\",\"GROUPS\",\"$this->queueName\"); local r = {type(t)}; for k,v in pairs(t) do r[#r+1] = k; r[#r+1] = v; end; return r";
        $groups = $redis->eval($xinfoGroups);
        $groupsNames = [];
        if ($groups) {
            $output->writeln('Groups: ');
            $table = new Table($output);
            $table->setHeaders([
                'name',
                'consumers',
                'pending messages (delivered but not yet acknowledged)',
                'last-delivered-id'
            ]);
            $startIx = 2;
            foreach ($groups as $key => $groupData) {
                if ($key == $startIx) {
                    $table->addRows([
                        [
                            $groupData[1],
                            $groupData[3],
                            $groupData[5],
                            $groupData[7]
                        ]
                    ]);
                    $groupsNames[] = $groupData[1];
                    $startIx += 2;
                }


            }

            $table->render();
        } else {
            $output->writeln('Groups: no information');
        }
        /** CONSUMERS */
        foreach ($groupsNames as $groupName) {
            $xinfoConsumers = "local t = redis.pcall(\"XINFO\",\"CONSUMERS\",\"$this->queueName\", \"$groupName\"); local r = {type(t)}; for k,v in pairs(t) do r[#r+1] = k; r[#r+1] = v; end; return r";
            $consumers = $redis->eval($xinfoConsumers);

            if ($consumers) {
                $output->writeln('Consumers for group ' . $groupName . ': ');
                $table = new Table($output);
                $table->setHeaders(['name', 'pending', 'iddle']);
                $startIx = 2;

                foreach ($consumers as $key => $consumerData) {

                    if ($key == $startIx) {
                        $table->addRows([
                            [
                                $consumerData[1],
                                $consumerData[3],
                                $consumerData[5] / 1000000,
                            ]
                        ]);
                        $startIx += 2;
                    }
                }
                $table->render();
            } else {
                $output->writeln('Consumers for group ' . $groupName . ': no information');
            }
        }

    }

    protected function configure()
    {
        $this->setDescription('Stats for enqueue/enqueue-bundle');
    }
}