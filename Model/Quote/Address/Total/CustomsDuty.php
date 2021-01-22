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

namespace Common\CustomsDuty\Model\Quote\Address\Total;

use Common\CustomsDuty\Api\DutyRepositoryInterface;
use Common\CustomsDuty\Helper\Calculator;
use Common\CustomsDuty\Model\Config\Source\CalculationType;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use Magento\Quote\Model\Quote\Item;
use Magento\Store\Model\ScopeInterface;

/**
 * @package Common\CustomsDuty
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_customs_duty
 */
class CustomsDuty extends AbstractTotal
{
    /**
     * @var Calculator
     */
    private Calculator $calculator;

    /**
     * @var DutyRepositoryInterface
     */
    private DutyRepositoryInterface $dutyRepository;

    /**
     * @var PriceCurrencyInterface
     */
    private PriceCurrencyInterface $priceCurrency;

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @param Calculator              $calculator
     * @param DutyRepositoryInterface $dutyRepository
     * @param PriceCurrencyInterface  $priceCurrency
     * @param ScopeConfigInterface    $scopeConfig
     */
    public function __construct(
        Calculator $calculator,
        DutyRepositoryInterface $dutyRepository,
        PriceCurrencyInterface $priceCurrency,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->calculator = $calculator;
        $this->dutyRepository = $dutyRepository;
        $this->priceCurrency = $priceCurrency;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param Quote                       $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total                       $total
     * @return CustomsDuty
     * @throws NoSuchEntityException
     */
    public function collect(
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);

        $address = $shippingAssignment->getShipping()->getAddress();
        $countryId = $address->getCountryId();

        $calculationType = $this->scopeConfig->getValue(
            'customs_duty/general/calculation_type',
            ScopeInterface::SCOPE_WEBSITE,
            $quote->getStore()->getWebsite()
        );

        $baseCustomsDuty = 0;
        foreach ($shippingAssignment->getItems() as $item) {
            /* @var Item $item */
            $hsCode = $item->getBuyRequest()->getDataByKey('hs_code');
            if (!$hsCode) {
                continue;
            }

            $duty = $this->dutyRepository->getByHsCode($hsCode, $countryId);
            switch ($calculationType) {
                case CalculationType::TYPE_FROM_AMOUNT:
                    $baseCustomsDuty += $this->calculator->calculateFromAmount(
                        $item->getBasePrice(),
                        $duty->getRate(),
                        $item->getQty()
                    );
                    break;

                case CalculationType::TYPE_FROM_QUANTITY:
                    $baseCustomsDuty += $this->calculator->calculateFromQuantity(
                        $duty->getFee(),
                        $item->getQty()
                    );
                    break;

                case CalculationType::TYPE_COMPOSITE:
                    $baseCustomsDuty += $this->calculator->calculateComposite(
                        $item->getBasePrice(),
                        $duty->getRate(),
                        $duty->getFee(),
                        $item->getQty()
                    );
                    break;
            }
        }

        $customsDuty = $this->priceCurrency->convert($baseCustomsDuty);
        $total->setData($this->getCode(), $customsDuty);

        $this->_addBaseAmount($baseCustomsDuty);
        $this->_addAmount($customsDuty);

        return $this;
    }

    /**
     * @param Quote $quote
     * @param Total $total
     * @return array
     */
    public function fetch(Quote $quote, Total $total)
    {
        return [
            'code'  => $this->getCode(),
            'title' => $this->getLabel(),
            'value' => $total->getData($this->getCode())
        ];
    }

    /**
     * @return Phrase
     */
    public function getLabel()
    {
        return __('Customs Duty');
    }
}
