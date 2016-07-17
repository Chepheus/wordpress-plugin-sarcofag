<?php
namespace Sarcofag\Service\SPI\EventManager;

interface ListenerInterface
{
    /**
     * It is basic event to execute in
     * wordpress context.
     *
     * @param array $arguments [OPTIONAL]
     * 
     * @return void
     */
    public function __invoke($arguments = []);

    /**
     * @return string[]
     */
    public function getNames();

    /**
     * @return int
     */
    public function getPriority();

    /**
     * @return int
     */
    public function getArgc();
}
