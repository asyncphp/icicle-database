<?php

namespace AsyncPHP\Icicle\Database\Connector;

use AsyncPHP\Doorman\Task;

final class DoormanConnectorTask implements Task
{
    /**
     * @var array
     */
    private $config;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @inheritdoc
     *
     * @return string
     */
    public function serialize()
    {
        return serialize($this->config);
    }

    /**
     * @inheritdoc
     *
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        $this->config = unserialize($serialized);
    }

    /**
     * @inheritdoc
     *
     * @return string
     */
    public function getHandler()
    {
        return DoormanConnectorHandler::class;
    }

    /**
     * @inheritdoc
     *
     * @return array
     */
    public function getData()
    {
        return $this->config;
    }

    /**
     * @inheritdoc
     *
     * @return bool
     */
    public function ignoresRules()
    {
        return false;
    }

    /**
     * @inheritdoc
     *
     * @return bool
     */
    public function stopsSiblings()
    {
        return false;
    }

    /**
     * @inheritdoc
     *
     * @return bool
     */
    public function canRunTask()
    {
        return true;
    }
}
