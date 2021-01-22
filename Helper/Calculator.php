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

namespace Common\CustomsDuty\Helper;

/**
 * @package Common\CustomsDuty
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_customs_duty
 */
class Calculator
{
    /**
     * @param $amount
     * @param $dutyRate
     * @param $dutyFee
     * @param $qty
     * @return float|int
     */
    public function calculateComposite($amount, $dutyRate, $dutyFee, $qty)
    {
        return $this->calculateFromQuantity($dutyFee, $qty)
            + $this->calculateFromAmount($amount, $dutyRate, $qty);
    }

    /**
     * @param $amount
     * @param $dutyRate
     * @param $qty
     * @return float|int
     */
    public function calculateFromAmount($amount, $dutyRate, $qty)
    {
        return $amount * $dutyRate * $qty * 0.01;
    }

    /**
     * @param $dutyFee
     * @param $qty
     * @return float|int
     */
    public function calculateFromQuantity($dutyFee, $qty)
    {
        return $dutyFee * $qty;
    }
}
