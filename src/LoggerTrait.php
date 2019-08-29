<?php declare(strict_types=1);

namespace IWS\Queues;

use IWS\Queues\Services\Log;
use ReflectionClass;


trait LoggerTrait
{
    /***
     * @return \Monolog\Logger
     * @throws \ReflectionException
     * @throws \Exception
     */
    protected function getLogger()
    {
        $reflect = new ReflectionClass($this);
        return Log::getLogger($reflect->getShortName());
    }
}
