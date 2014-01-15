<?php
/**
 * User: s.zheleznitskij
 * Date: 11/27/13
 * Time: 4:28 PM
 */

class Certificate_StockNotification_Model_Observer
{
    const XML_PATH_EMAIL_SENDER     = 'stockNotification/email/sender_email_identity';

    const XML_PATH_EMAIL_TEMPLATE   = 'stockNotification/email/template';

    /**
     * Cataloginventory stock item after save after handler.
     *
     * @param Varien_Event_Observer $observer Observer object.
     */
    public function sentInStockNotification(Varien_Event_Observer $observer)
    {
        $newItemStatus = $observer->getItem()->getIsInStock();
        $oldItemStatus = $observer->getItem()->getOrigData('is_in_stock');

        /* if product is new*/
        if (!empty($oldItemStatus)) {
            return;
        }

        if ($oldItemStatus == Mage_CatalogInventory_Model_Stock::STOCK_OUT_OF_STOCK &&
            $newItemStatus == Mage_CatalogInventory_Model_Stock::STOCK_IN_STOCK) {
            $productId = $observer->getItem()->getItemId();
            $wishListCollection = $this->getWishListCollection($productId);

            if (count($wishListCollection->getData())) {
                $wishListData = $wishListCollection->getData();
                $customer = Mage::getModel("customer/customer");
                $userEmails = array();
                foreach ($wishListData as $data) {
                    $customer->load($data['customer_id'])->getEmail();
                    $userEmails[] = $customer->getEmail();
                }

                $this->sendEmailNotification($userEmails);
            }
        }
    }

    /**
     * Get wishlist collection stock item save after handler.
     *
     * @param integer $productId Product id.
     *
     * @return mixed
     */
    public function getWishListCollection($productId)
    {
        $collection = Mage::getModel('wishlist/item')->getCollection();
        $collection->getSelect()
            ->join(
                array('wishlist' => 'wishlist'),
                'main_table.wishlist_id = wishlist.wishlist_id',
                'wishlist.customer_id'
            );

        return $collection->addFieldToFilter('`main_table`.product_id', $productId);
    }

    /**
     * Send send Email Notification.
     *
     * @param array $userEmails Send emails notification to user.
     */
    public function sendEmailNotification(array $userEmails)
    {
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);
        $mailTemplate = Mage::getModel('core/email_template');

        foreach ($userEmails as $email) {
            $mailTemplate->setDesignConfig(array('area' => 'frontend'))
                ->sendTransactional(
                    Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
                    Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
                    $email,
                    null,
                    array('data' => $userEmails)
                );

            if (!$mailTemplate->getSentSuccess()) {
                Mage::logException('Email was not send by some reasons');
            }

            $translate->setTranslateInline(true);
        }
    }
}