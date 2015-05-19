<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category    Bullfrogchampion
 * @package     Bullfrogchampion_Checkout
 * @copyright   Copyright (c) 2015 Jeremy Champion (http://www.jeremychampion.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Bullfrogchampion_Checkout_Block_Checkout_Links extends Mage_Checkout_Block_Links
{
	/**
     * Add link on checkout page to parent block
     *
     * @return Mage_Checkout_Block_Links
     */
    public function addCheckoutLink()
    {
        if (!$this->helper('checkout')->canOnepageCheckout()) {
            return $this;
        }

        $parentBlock = $this->getParentBlock();
        if ($parentBlock && Mage::helper('core')->isModuleOutputEnabled('Mage_Checkout')) {
            $text = $this->__('Checkout');
            $parentBlock->addLink(
                $text, (Mage::helper('bfc_checkout')->isEnabled()) ? 'directcheckout' : 'checkout', $text,
                true, array('_secure' => true), 60, null,
                'class="top-link-checkout"'
            );
        }
        return $this;
    }
}
