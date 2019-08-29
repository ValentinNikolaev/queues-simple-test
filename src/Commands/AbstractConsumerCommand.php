<?php


namespace IWS\Queues\Commands;


use Exception;
use IWS\Queues\LoggerTrait;
use IWS\Queues\SignalTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractConsumerCommand extends Command
{
    const LOOP_MAX = 10000;

    use SignalTrait, LoggerTrait;

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \ReflectionException
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getSignalHelper()->listen();
        $loopCount = 0;

        try {
            while (!$this->wasReceivedStopSignal()) {
                // Restart script
                if ($loopCount > self::LOOP_MAX) {
                    break;
                }

                $this->runCommand(
                    $input,
                    $output
                );
            }

            $this->getLogger()->info('Got stop signal(s). Exit...');
        } catch (Exception $e) {
            $this->getLogger()->error($e->getMessage(), [
                'scope' => get_class($this),
                'class' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            // Prevent too fast restart
            sleep(5);
        }
    }

    abstract protected function runCommand(InputInterface $input, OutputInterface $output);
}