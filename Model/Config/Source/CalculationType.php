<?php
/*
 * Copyright (c) 2020 Zengliwei
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the
 * Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFINGEMENT. IN NO EVENT SHALL THE AUTHORS
 * OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
 * OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace Common\CustomsDuty\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * @package Common\CustomsDuty
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_customs_duty
 */
class CalculationType implements OptionSourceInterface
{
    public const TYPE_FROM_AMOUNT = 'from_amount';
    public const TYPE_FROM_QUANTITY = 'from_quantity';
    public const TYPE_COMPOSITE = 'composite';
    public const TYPE_DYNAMIC = 'dynamic';
    public const TYPE_SPECIAL = 'special';

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        return [
            ['label' => __('From Amount'), 'value' => self::TYPE_FROM_AMOUNT],
            ['label' => __('From Quantity'), 'value' => self::TYPE_FROM_QUANTITY],
            ['label' => __('Composite'), 'value' => self::TYPE_COMPOSITE],
            ['label' => __('Dynamic'), 'value' => self::TYPE_DYNAMIC],
            ['label' => __('Special'), 'value' => self::TYPE_SPECIAL]
        ];
    }
}
