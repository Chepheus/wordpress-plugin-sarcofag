<?php
namespace Sarcofag\Service\SPI\Widget;

use Sarcofag\Service\API\WP\Widget as WPWidget;
use Sarcofag\Service\SPI\Widget\Params\BasicInterface;

interface WidgetInterface
{
    /**
     * @return BasicInterface
     */
    public function getParams();

    /**
     * @param array $placeholderParams Display arguments including 'before_title', 'after_title',
     *                                  'before_widget', and 'after_widget'.
     * @param array $settings The settings for the particular instance of the widget.
     *
     * @return string
     */
    public function render(array $placeholderParams = [], array $settings);
}
