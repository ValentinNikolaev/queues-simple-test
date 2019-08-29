<?php


namespace IWS\Queues\Services;

use Monolog\ErrorHandler;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Log
{
    protected static $instance;

    /**
     * @param $channel
     * @param $message
     * @param array $context
     * @throws \Exception
     */
    public static function debug($channel, $message, array $context = [])
    {
        self::getLogger($channel)->addDebug($message, $context);
    }

    /**
     * Method to return the Monolog instance
     *
     * @return \Monolog\Logger
     * @throws \Exception
     */
    static public function getLogger($channel = 'default')
    {
        if (!isset(self::$instance[$channel])) {
            self::configureInstance($channel);
        }
        return self::$instance[$channel];
    }

    /**
     * Configure Monolog to use a rotating files system.
     *
     * @param string $channel
     * @return void
     * @throws \Exception
     */
    protected static function configureInstance($channel = 'default')
    {
        $logger = new Logger($channel);

        $loggerFormat = "[%datetime%][$channel][".getmypid()."] %level_name% %message% %context% %extra%\n";
        $formatter = new LineFormatter($loggerFormat);
        $formatter->ignoreEmptyContextAndExtra(true);

        $fileHandler = new RotatingFileHandler(__DIR__ . '/../../logs/'.$channel.'.log', 0, \Monolog\Logger::INFO);
        $fileHandler->setFilenameFormat('{filename}-{date}', 'Y/m/d');
        $fileHandler->setFormatter($formatter);
        $logger->pushHandler($fileHandler);

        $consoleHandler = new StreamHandler('php://stdout', Logger::INFO);
        $consoleHandler->setFormatter($formatter);
        $logger->pushHandler($consoleHandler);

        /*
        $errorHandler = new StreamHandler('php://stderr', Logger::ERROR, $bubble = false);
        $errorHandler->setFormatter($formatter);
        $logger->pushHandler($errorHandler);
        // Register logger as default PHP error, exception and shutdown handler
        // Note: Make sure only this handler handles errors (set $callPrevious to false)
        $errorHandler = ErrorHandler::register($logger, $errorLevelMap = false, $exceptionLevelMap = false);
        $errorHandler->registerErrorHandler($levelMap = [], $callPrevious = false);
        $errorHandler->registerExceptionHandler($levelMap = [], $callPrevious = false);*/
        self::$instance[$channel] = $logger;
    }

    /**
     * @param $message
     * @param array $context
     * @throws \Exception
     */
    public static function info($channel, $message, array $context = [])
    {
        self::getLogger($channel)->addInfo($message, $context);
    }

    /**
     * @param $message
     * @param array $context
     * @throws \Exception
     */
    public static function notice($channel, $message, array $context = [])
    {
        self::getLogger($channel)->addNotice($message, $context);
    }

    /**
     * @param $message
     * @param array $context
     * @throws \Exception
     */
    public static function warning($channel, $message, array $context = [])
    {
        self::getLogger($channel)->addWarning($message, $context);
    }

    /**
     * @param $message
     * @param array $context
     * @throws \Exception
     */
    public static function error($channel, $message, array $context = [])
    {
        self::getLogger($channel)->addError($message, $context);
    }

    /**
     * @param $message
     * @param array $context
     * @throws \Exception
     */
    public static function critical($channel, $message, array $context = [])
    {
        self::getLogger($channel)->addCritical($message, $context);
    }

    /**
     * @param $message
     * @param array $context
     * @throws \Exception
     */
    public static function alert($channel, $message, array $context = [])
    {
        self::getLogger($channel)->addAlert($message, $context);
    }

    /**
     * @param $message
     * @param array $context
     * @throws \Exception
     */
    public static function emergency($channel, $message, array $context = [])
    {
        self::getLogger($channel)->addEmergency($message, $context);
    }
}