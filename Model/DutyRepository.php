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

namespace Common\CustomsDuty\Model;

use Common\CustomsDuty\Api\DutyRepositoryInterface;
use Common\CustomsDuty\Model\Duty as Model;
use Common\CustomsDuty\Model\DutyFactory;
use Common\CustomsDuty\Model\ResourceModel\Duty as ResourceModel;
use Common\CustomsDuty\Model\ResourceModel\Duty\Collection;
use Common\CustomsDuty\Model\ResourceModel\Duty\CollectionFactory;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @package Common\CustomsDuty
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_customs_duty
 */
class DutyRepository implements DutyRepositoryInterface
{
    /**
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;

    /**
     * @var DutyFactory
     */
    private DutyFactory $dutyFactory;

    /**
     * @var ResourceModel
     */
    private ResourceModel $resourceModel;

    /**
     * @param CollectionFactory $collectionFactory
     * @param DutyFactory       $dutyFactory
     * @param ResourceModel     $resourceModel
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        DutyFactory $dutyFactory,
        ResourceModel $resourceModel
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->dutyFactory = $dutyFactory;
        $this->resourceModel = $resourceModel;
    }

    /**
     * @var array
     */
    protected array $duties = [];

    /**
     * @var array
     */
    protected array $mappings = [];

    /**
     * @inheritDoc
     */
    public function get($id)
    {
        if (!isset($this->duties[$id])) {
            /* @var $modelDuty Model */
            $modelDuty = $this->dutyFactory->create();
            $this->resourceModel->load($modelDuty, $id);
            $this->duties[$id] = $modelDuty;
            $this->mappings[$modelDuty->getCountryId() . '-' . $modelDuty->getHsCode()] = $id;
        }
        return $this->duties[$id];
    }

    /**
     * @inheritDoc
     */
    public function getByHsCode($hsCode, $countryId)
    {
        $mappingKey = $countryId . '-' . $hsCode;
        if (!isset($this->mappings[$mappingKey])) {
            /* @var $collection Collection */
            $collection = $this->collectionFactory->create();
            $collection->addFieldToFilter('country_id', ['eq' => $countryId])
                ->addFieldToFilter('hs_code', ['eq' => $hsCode]);
            $select = $collection->getSelect()
                ->reset(\Zend_Db_Select::COLUMNS)
                ->columns(['id']);
            if (($id = $collection->getConnection()->fetchOne($select))) {
                $this->get($id);
            } else {
                throw new NoSuchEntityException(__('Record of specified HS code does not exist.'));
            }
        }
        return $this->mappings[$mappingKey];
    }
}
