<?php

namespace EW\CurrencySymbolPosition\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class SymbolPosition implements ArrayInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => 'default', 'label' => __('Use locale default')],
            ['value' => 'left', 'label' => __('Left')],
            ['value' => 'right', 'label' => __('Right')],
        ];
    }
}
