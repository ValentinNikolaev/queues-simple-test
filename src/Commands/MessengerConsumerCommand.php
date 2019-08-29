<?php


namespace IWS\Queues\Commands;


use IWS\Queues\Consumers\MessengerMsg;
use IWS\Queues\Services\CommonHelper;
use IWS\Queues\Services\Config;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\RedisExt\Connection;
use Symfony\Component\Messenger\Transport\RedisExt\RedisReceiver;
use Symfony\Component\Messenger\Transport\Serialization\PhpSerializer;

class MessengerConsumerCommand extends AbstractConsumerCommand
{
    private $queueName;
    private $redisDsn;
    private $consumerName;
    /**
     * @var Connection
     */
    private $connection;
    /**
     * @var PhpSerializer
     */
    private $serializer;

    public function __construct(string $name = null)
    {
        parent::__construct($name);
        $this->queueName = Config::getInstance()->getConfigKey('queues')['messenger'];
        $this->redisDsn = Config::getInstance()->getConfigKey('redis')['dsn_messenger'];
        $this->consumerName = CommonHelper::generateConsumerName();
        $this->serializer = new PhpSerializer();
        $this->connection = Connection::fromDsn($this->redisDsn . '/' . $this->consumerName,
            ['serializer' => 2, 'auto_setup' => false, 'stream_max_entries' => 20000, 'consumer' => $this->consumerName]);
    }

    public function runCommand(InputInterface $input, OutputInterface $output)
    {

        $this->getLogger()->info('Starting messenger consumer with name ' . $this->consumerName);
        $receiver = new RedisReceiver($this->connection, $this->serializer);
        /**
         * @var $message MessengerMsg
         */
        $result = $receiver->get();
        if (!empty($result)) {
            /**
             * @var Envelope $envolve
             */
            $envelope = $receiver->get()[0];
            /**
             * @var $receivedMessage MessengerMsg
             */
            $receivedMessage = $envelope->getMessage();
            $messages = json_decode($receivedMessage->getMessage());

            foreach ($messages as $fakeMessage) {
                $this->getLogger()->info('Processing message... ' . json_encode($fakeMessage));
                sleep(2);
            }
        }

    }

    protected function configure()
    {
        $this->setDescription('Run symfony/messenger based consumer');
    }
}