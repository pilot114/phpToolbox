<?php

namespace PhpToolbox;

class Chain
{
    private $chainId;
    private $wsLogger;
    private $mqClient;

    public function __construct($wsLogger, $mqClient, $debug = false)
    {
        $this->wsLogger = $wsLogger;
        $this->chainId  = $this->genUuidv4();

        // PSEVDOCODE: command to demonizer for create chain channel
        $mqClient->send('chainFactoryChannel', $this->chainId);
        $this->mqClient = $mqClient;
    }

    public function parse($commandLine)
    {
        // one(123,2,'qweq')['test'][qwe]
        $unitCommands = explode('->', $commandLine);
        foreach($unitCommands as $unitCommand) {
            $unitName     = strtok($unitCommand, '(');
            $unitArgs     = explode(',', strtok(')'));
            $unitSelector = $unitCommand;
            $this->mqClient->send($this->chainId, $unitName, $unitArgs, $unitSelector);
        }
    }

    private function genUuidv4()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
