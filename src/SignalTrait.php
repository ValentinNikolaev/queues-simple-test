<?php declare(strict_types = 1);

namespace IWS\Queues;

use AlexS\SignalHelper;

trait SignalTrait
{
    protected $defaultStopSignals = [
        SIGKILL,
        SIGINT,
        SIGHUP,
        SIGTERM
    ];

    /**
     * @return SignalHelper
     */
    protected function getSignalHelper()
    {
        return  $this->getHelper('signal');
    }

    /**
     * @return array
     */
    protected function takeSignals()
    {
        return $this->getSignalHelper()->takeSignals();
    }

    /**
     * @return bool
     */
    protected function wasReceivedStopSignal()
    {
        return count(array_intersect($this->defaultStopSignals, $this->takeSignals())) > 0;
    }
}
