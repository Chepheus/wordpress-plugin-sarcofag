<?php
namespace Sarcofag\SPI\Sidebar;

use DI\FactoryInterface;
use Sarcofag\API\WP;
use Sarcofag\SPI\EventManager\Action\ActionInterface;
use Sarcofag\SPI\EventManager\ListenerInterface;
use Sarcofag\SPI\Sidebar\SidebarEntryInterface;

class Registry implements ActionInterface
{
    /**
     * @var WP
     */
    protected $wpService;

    /**
     * @var SidebarEntryInterface[]
     */
    protected $attached = [];

    /**
     * @var FactoryInterface
     */
    protected $factory;

    /**
     * Registry constructor.
     *
     * @param WP $wpService
     * @param FactoryInterface $factory
     */
    public function __construct(WP $wpService, FactoryInterface $factory)
    {
        $this->wpService = $wpService;
        $this->factory = $factory;
    }

    /**
     * @param SidebarEntryInterface $sidebarEntry
     *
     * @return $this
     */
    public function attach(SidebarEntryInterface $sidebarEntry)
    {
        $this->attached[] = $sidebarEntry;
        return $this;
    }

    /**
     * @param SidebarEntryAggregateInterface $sidebarEntryAggregate
     *
     * @return $this
     */
    public function attachBunch(SidebarEntryAggregateInterface $sidebarEntryAggregate)
    {
        foreach ($sidebarEntryAggregate->getSidebarEntries() as $sidebarEntry) {
            $this->attached[] = $sidebarEntry;
        }
        return $this;
    }

    /**
     * @return ListenerInterface[]
     */
    public function getActionListeners()
    {
        $sidebarsInit = function () {
            foreach ($this->attached as $attachedItem) {
                $args = array(
                    'id'            => $attachedItem->getId(),
                    'name'          => $attachedItem->getName(),
                    'description'   => $attachedItem->getDescription(),
                    'before_title'  => $attachedItem->getBeforeTitle(),
                    'after_title'   => $attachedItem->getAfterTitle(),
                    'before_widget' => $attachedItem->getBeforeWidget(),
                    'after_widget'  => $attachedItem->getAfterWidget(),
                );

                $this->wpService->register_sidebar($attachedItem->getCustomFields() + $args);
            }
        };
    
        return [$this->factory->make('ActionListener', ['names' => 'widgets_init', 'callable' => $sidebarsInit])];
    }
}
