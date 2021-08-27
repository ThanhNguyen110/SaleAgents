<?php

namespace AHT\SaleAgents\Block\Customer;

class Saleagents extends \Magento\Framework\View\Element\Template
{
    protected $_productCollectionFactory;
    protected $_customerSession;
    protected $_resource;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\ResourceConnection $Resource,


        array $data = []
    ) {
        $this->_customerSession = $customerSession;
        $this->_resource = $Resource;
        $this->_productCollectionFactory = $productCollectionFactory;
        parent::__construct($context, $data);
    }

    public function getProductCollection()
    {
        $customerId = $this->_customerSession->getCustomer()->getId();

        //SELECT `e`.* FROM `catalog_product_entity` AS `e`
        $collection = $this->_productCollectionFactory->create();

        //SELECT `e`.*, `at_sale_agent_id`.`value` AS `sale_agent_id` FROM `catalog_product_entity` AS `e` LEFT JOIN `catalog_product_entity_text` AS `at_sale_agent_id` ON (`at_sale_agent_id`.`entity_id` = `e`.`entity_id`) AND (`at_sale_agent_id`.`attribute_id` = '159') AND (`at_sale_agent_id`.`store_id` = 0) WHERE (at_sale_agent_id.value = '3')
        $collection->addAttributeToSelect('*')->addFieldToFilter('sale_agent_id', $customerId);

        $aht_sales_agent = $this->_resource->getTableName('aht_sales_agent');

        //SELECT `e`.*, `at_sale_agent_id`.`value` AS `sale_agent_id`, `order_sa`.* FROM `catalog_product_entity` AS `e` LEFT JOIN `catalog_product_entity_text` AS `at_sale_agent_id` ON (`at_sale_agent_id`.`entity_id` = `e`.`entity_id`) AND (`at_sale_agent_id`.`attribute_id` = '159') AND (`at_sale_agent_id`.`store_id` = 0) INNER JOIN `aht_sales_agent` AS `order_sa` ON e.entity_id = order_sa.order_item_id WHERE (at_sale_agent_id.value = '3')
        $collection->getSelect()->join(
            /* ['order_sa' => $aht_sales_agent],
            'e.entity_id = order_sa.order_item_id' */
            ['test' => 'catalog_product_entity_varchar'],
            'test.entity_id = e.entity_id and test.attribute_id = 73'
        );
        $collection->setPageSize(5);
        var_dump($collection->getSelect()->__toString());
        return $collection;
    }
}
