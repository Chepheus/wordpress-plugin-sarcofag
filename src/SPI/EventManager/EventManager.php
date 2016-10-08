<?php
namespace Sarcofag\SPI\EventManager;

use DI\FactoryInterface;
use Sarcofag\Exception\RuntimeException;
use Sarcofag\API\WP;
use Sarcofag\SPI\EventManager\Action\ActionInterface;
use Sarcofag\SPI\EventManager\DataFilter\DataFilterInterface;

class EventManager implements EventManagerInterface
{
    /**
     * @var WP
     */
    protected $wpService;

    /**
     * @var FactoryInterface
     */
    protected $factory;

    /**
     * ActionRegistrationService constructor.
     *
     * @param WP $wpService
     */
    public function __construct(WP $wpService, FactoryInterface $factory)
    {
        $this->wpService = $wpService;
        $this->factory = $factory;
    }

    /**
     * Facade method to detect functionality of the
     * action and pass it to correct attacher.
     *
     * @param ActionInterface | DataFilterInterface $listenersAggregate
     * @throws RuntimeException
     */
    public function attachListeners($listenersAggregate)
    {
        if ($listenersAggregate instanceOf ActionInterface) {
            $this->register($listenersAggregate->getActionListeners(), WP::EVENT_TYPE_ACTION);
        }

        if ($listenersAggregate instanceOf DataFilterInterface) {
            $this->register($listenersAggregate->getDataFilterListeners(), WP::EVENT_TYPE_FILTER);
        }

        if (!$listenersAggregate instanceof ActionInterface && !$listenersAggregate instanceof DataFilterInterface) {
            throw new RuntimeException('Incorect handler passed to register, 
                                            action must implement Filter or Actor Interface');
        }
    }

    /**
     * @param ListenerInterface[] | array $listeners
     * @param string $type one of filter or action types
     */
    protected function register(array $listeners, $type)
    {
        if (!in_array($type, [WP::EVENT_TYPE_FILTER, WP::EVENT_TYPE_ACTION])) {
            throw new RuntimeException
                            ('Unsupported type of the event ['.$type.'], now supports only filter or action types');
        }

        foreach ($listeners as $listener) {
            if (is_array($listener)) {
                $listener = $this->factory->make('ActionListener', $listener);
            }

            if (!$listener instanceof ListenerInterface) {
                throw new RuntimeException('Listener must be type of ListenerInterface');
            }

            foreach ($listener->getNames() as $name) {
                /**
                 * @see WP::add_action
                 * @see WP::add_filter
                 */
                $this->wpService->__call('add_'.strtolower($type),
                                            [$name,
                                             $listener->getCallable(),
                                             $listener->getPriority(),
                                             $listener->getArgc()]);
            }
        }
    }
}
