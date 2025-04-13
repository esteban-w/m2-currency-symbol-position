<?php declare(strict_types=1);

namespace EW\CurrencySymbolPosition\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class CurrencySymbolPosition implements OptionSourceInterface
{

    const CURRENCY_SYMBOL_POSITION_DEFAULT = 0;

    const CURRENCY_SYMBOL_POSITION_RIGHT = 1;

    const CURRENCY_SYMBOL_POSITION_LEFT = 2;


    /**
     * @inheritDoc
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value' => self::CURRENCY_SYMBOL_POSITION_DEFAULT,
                'label' => __('Default'),
            ],
            [
                'value' => self::CURRENCY_SYMBOL_POSITION_LEFT,
                'label' => __('Left'),
            ],
            [
                'value' => self::CURRENCY_SYMBOL_POSITION_RIGHT,
                'label' => __('Right'),
            ],
        ];
    }
}
