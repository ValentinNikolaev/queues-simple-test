<?php

namespace IWS\Queues\Services;


class Config
{
    private static $instance = null;
    protected $config = [];

    private function __construct($configPath)
    {
        $this->config = parse_ini_file($configPath, true);
    }

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new Config(__DIR__ . '/../../config/config.ini');
        }

        return self::$instance;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getConfigKey($key)
    {
        return isset($this->config[$key]) ? $this->config[$key] : null;
    }

}