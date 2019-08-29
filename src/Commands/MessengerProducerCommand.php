<?php


namespace IWS\Queues\Commands;


use IWS\Queues\Consumers\MessengerMsg;
use IWS\Queues\Services\CommonHelper;
use IWS\Queues\Services\Config;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\RedisExt\Connection;
use Symfony\Component\Messenger\Transport\RedisExt\RedisTransport;
use Symfony\Component\Messenger\Transport\Serialization\PhpSerializer;

class MessengerProducerCommand extends AbstractProducerCommand
{
    private $queueName;
    private $redisDsn;
    /**
     * @var PhpSerializer
     */
    private $serializer;
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(string $name = null)
    {
        parent::__construct($name);
        $this->queueName = Config::getInstance()->getConfigKey('queues')['messenger'];
        $this->redisDsn = Config::getInstance()->getConfigKey('redis')['dsn_messenger'];
        $this->serializer = new PhpSerializer();
        $this->connection = Connection::fromDsn($this->redisDsn,
            ['serializer' => 2, 'auto_setup' => false, 'stream_max_entries' => 20000]);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \ReflectionException
     */
    public function runCommand(InputInterface $input, OutputInterface $output)
    {
        $messagesCount = 100/*$input->getArgument('count')*/
        ;
        $delay = 1/*$input->getArgument('delay')*/
        ;
        $this->getLogger()->info("Start messenger producer. Loop: $messagesCount. Delay: $delay");



        $transport = new RedisTransport($this->connection, $this->serializer);
        for ($i = 0; $i <= $messagesCount; $i++) {
            $fakeMessage = CommonHelper::generateBunchFakeMessages(mt_rand(1, 5));
            $envelop = new Envelope(new MessengerMsg(json_encode($fakeMessage)));

            $this->getLogger()->info('Sent message (size: ' . count($fakeMessage) . ') to queue');
            $transport->send($envelop);
            sleep($delay);
        }

        $this->getLogger()->info('Finished');
        exit(1);
    }

    protected function configure()
    {
        $this->setDescription('Run symfony/messenger based producer');
    }
}