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
 * @author      Sinabs <http://www.sinabs.fr>
 * @author      Jeremy Champion <bullfrogchampion@gmail.com>
 */
class Bullfrogchampion_Checkout_AjaxController extends Mage_Core_Controller_Front_Action
{
    /**
     * Billing address
     *
     * @var array
     */
    private $_dataBillingAddress;
    
    /**
     * XML path agreements config
     *
     */
    const XML_PATH_CHECKOUT_OPTIONS_ENABLE_AGREEMENTS = 'checkout/options/enable_agreements';
    
    /**
     * Get Onepage Object
     *
     * @return Mage_Checkout_Model_Type_Onepage
     */
    protected function _getOnepage()
    {
        return Mage::getSingleton('checkout/type_onepage');
    }
    
    /**
     * Get cart
     *
     * @return Mage_Checkout_Model_Cart
     */
    protected function _getCart()
    {
    	return Mage::getSingleton('checkout/cart');
    }

    /**
     * Update Shipping, Payment and resume
     *
     * @return void
     */
    public function update_spoAction()
    {
        $data = $this->getRequest()->getPost('billing', array());
        
        if ($data['use_for_shipping'] == '1') {
            if($this->getRequest()->getParam('billing_address_id') && intval($this->getRequest()->getParam('billing_address_id')) > 0) {
                $idAddress = $this->getRequest()->getParam('billing_address_id');
                $address = Mage::getModel('customer/address')->load($idAddress);
                $data['country_id'] = $address->getCountry();
            } else {
                $data = $this->getRequest()->getPost('billing', array());
            }
        }
        
        if ($data['use_for_shipping'] == '0') {
            if ($this->getRequest()->getParam('shipping_address_id') && intval($this->getRequest()->getParam('shipping_address_id')) > 0) {
                $idAddress = $this->getRequest()->getParam('shipping_address_id');
                $address = Mage::getModel('customer/address')->load($idAddress);
                $data['country_id'] = $address->getCountry();
            } else {
                $data = $this->getRequest()->getPost('shipping', array());
            }
        }
        
        if (!isset($data['country_id']) || empty($data['country_id'])) {
            $data['country_id'] = Mage::getStoreConfig('general/country/default');
        }
        
        $region = (!isset($data['region']) || empty($data['region'])) ? "-" : $data['region'];
        $region_id = (!isset($data['region_id']) || empty($data['region_id'])) ? "-" : $data['region_id'];
        $city = (!isset($data['city']) || empty($data['city'])) ? "-" : $data['city'];
        $postcode = (!isset($data['postcode']) || empty($data['postcode'])) ? "-" : $data['postcode'];
        
        $quote = $this->_getOnepage()->getQuote();
        
        $quote->getShippingAddress()
            ->setCountryId($data['country_id'])
            ->setRegionId($region_id)
            ->setRegion($region)
            ->setCity($city)
            ->setPostcode($postcode)
            ->setCollectShippingRates(true);
            
        $quote->getBillingAddress()->setCountryId($data['country_id']);
        $quote->getShippingAddress()->collectShippingRates();
        $quote->setTotalsCollectedFlag(false);
		
        $isLoggedIn = Mage::getSingleton('customer/session')->isLoggedIn();
        if ($isLoggedIn) {
        	$customerAddressId = $this->getRequest()->getPost('shipping_address_id', false);
        	$result = $this->getOnepage()->saveShipping($data, $customerAddressId);
        } else {
        	$result = $this->getOnepage()->saveShipping($data, '');
        }
        
        $shippingMethod = $this->getRequest()->getParam('shipping_method');
        $shippingRates = $quote->getShippingAddress()->getAllShippingRates();
       	if (count($shippingRates) == 1) {
            $shippingMethod = $shippingRates[0]->getCode();
        }

        if ($shippingMethod != '') {
            $quote->getShippingAddress()->setShippingMethod($shippingMethod);
            //var_dump($quote->getShippingAddress()->getShippingMethod());die;
        }

        $quote->collectTotals();
        $quote->save();
    
    	$this->loadLayout(false);
    	$this->renderLayout();
    }
    
    /**
     * Save order
     *
     * @return void
     */
    public function save_orderAction()
    {
    	$dataBilling = $this->getRequest()->getPost('billing', array());
    	$isSubscribed = $this->getRequest()->getPost('customer_subscribed', false);
        $customerAddressId = $this->getRequest()->getPost('billing_address_id', false);
        $registered = $this->getRequest()->getParam('customer_register', false);
            
    	try {
    		$result = $this->_getOnepage()->saveBilling($dataBilling, $customerAddressId);
	        
	        if (isset($result['error'])) {
	            throw new Exception($this->__("Please check billing address information"));
	        }
	        
	        // Billing address and shipping address are different
	        if (!isset($dataBilling['use_for_shipping']) || $dataBilling['use_for_shipping'] == 0) {
	            $dataShipping = $this->getRequest()->getPost('shipping', array());
	            $customerAddressId = $this->getRequest()->getPost('shipping_address_id', false);
	            $result = $this->_getOnepage()->saveShipping($dataShipping, $customerAddressId);
	            if (isset($result['error'])) {
	               throw new Exception($this->__("Please check shipping address information"));
	            }
	        }
	        
	        // Shipping Method
            $shippingMethod = $this->getRequest()->getPost('shipping_method', '');
            $result = $this->_getOnepage()->saveShippingMethod($shippingMethod);
            if (isset($result['error'])) {
                throw new Exception($this->__('Select Shipping Method'));
            }
            
            // Section redirection Paypal
            // Save payment method
            $data = $this->getRequest()->getPost('payment', array());
            $result = $this->_getOnepage()->savePayment($data);
            if (isset($result['error'])) {
                throw new Exception($this->__('Payment method is not defined'));
            }

            // get section and redirect data
            $redirectUrl = $this->_getOnepage()->getQuote()->getPayment()->getCheckoutRedirectUrl();
            if ($redirectUrl) {
                $result['redirect'] = $redirectUrl;
                $this->_getOnepage()->saveOrder();
                $this->getResponse()->setBody(Zend_Json::encode($result));
                return;
            }
            
            // Save payment method
            $data = $this->getRequest()->getPost('payment', array());
            $result = $this->_getOnepage()->savePayment($data);
            if (isset($result['error'])) {
                throw new Exception($this->__('Payment method is not defined'));
            }
            
            // Agreements
            if (Mage::getStoreConfig(self::XML_PATH_CHECKOUT_OPTIONS_ENABLE_AGREEMENTS)) {
            	$data = $this->getRequest()->getPost('agreement', array());
	            if (!isset($data[1]) || $data[1] != '1') {
	                throw new Exception($this->__('Please agree to all the terms and conditions before placing the order.'));
	            }	
            }
            
            if ($registered !== false) {
            	$this->_createCustomer($dataBilling, $isSubscribed);
            }
            
            $this->_getOnepage()->saveOrder();
            
            $redirectUrl = $this->_getOnepage()->getCheckout()->getRedirectUrl();
            if ($redirectUrl == '') $redirectUrl = "/checkout/onepage/success";
            $result['success'] = true;
            $result['error']   = false;
            if (isset($redirectUrl)) {
                $result['redirect'] = $redirectUrl;
            }
            //Mage::getSingleton('checkout/cart')->truncate()->save();
            $this->getResponse()->setBody(Zend_Json::encode($result));
    	} catch (Exception $e) {
    		$result['error'] = true;
    		$result['title'] = $this->__('Error');
    		$result['message'] = $e->getMessage();
            $this->getResponse()->setBody(Zend_Json::encode($result));
            return;
    	}
    }
    
    /**
     * Verify and set coupon code
     *
     * @return void
     */
    public function update_couponAction()
    {
    	$couponCode = (string) $this->getRequest()->getParam('coupon');
    	$response = array();
    	
    	try {
    		$this->_getCart()
    			->getQuote()
    			->getShippingAddress()
    			->setCollectShippingRates(true);
    		$this->_getCart()
    			->getQuote()
    			->setCouponCode(strlen($couponCode) ? $couponCode : '')
    			->collectTotals()
    			->save();
    			
    		if ($couponCode != $this->_getCart()->getQuote()->getCouponCode()) {
    			$response['error']= true;
    			$response['title'] = $this->__('Error');
    			$response['message'] = $this->__('Coupon code "%s" is not valid.', Mage::helper('core')->htmlEscape($couponCode));
    			$this->getResponse()->setBody(Zend_Json::encode($response));
    			return;	
    		}
    		
    		$response['success'] = true;
    		$response['error'] = false;
    		$this->getResponse()->setBody(Zend_Json::encode($response));
    	} catch (Mage_Core_Exception $e) {
    		$response['error'] = true;
    		$response['title'] = $this->__('Error');
    		$response['message'] = $e->getMessage();
    		$this->getResponse()->setBody(Zend_Json::encode($response));
    		return;
    	} catch (Exception $e) {
    		$response['error'] = true;
    		$response['title'] = $this->__('Error');
    		$response['message'] = $e->getMessage();
    		$this->getResponse()->setBody(Zend_Json::encode($response));
    		return;
    	}
    }
    
    /**
     * Create Customer
     *
     * @param array $data
     * @return Mage_Customer_Model_Customer_Session
     */
    private function _createCustomer($data, $isSubscribed = false)
    {
    	$this->_getOnepage()->getQuote()->setCheckoutMethod(Mage_Checkout_Model_Type_Onepage::METHOD_REGISTER);
    }
    
    /**
     * Update product qty
     *
     * @return void
     */
    public function update_qtyAction() 
    {
    	$response = array();
    	$qty = (int)$this->getRequest()->getParam('qty');
    	$itemId = (int)$this->getRequest()->getParam('itemId');
    	
    	try {
    		/*$cart = $this->_getCart()->updateItem($itemId, array(
    			'qty' => $qty
    		));*/
    		$cart = $this->_getCart()->updateItems(array($itemId => array(
    			'qty' => $qty
    		)));
    		
    		$cart->save();
    		$response['success'] = true;
    		$this->getResponse()->setBody(Zend_Json::encode($response));
    		return;
    	} catch (Exception $e) {
    		$response['success'] = false;
    		$response['error'] = true;
    		$response['message'] = $e->getMessage();
    		$response['ddd'] = get_class($this->_getCart());
    		$this->getResponse()->setBody(Zend_Json::encode($response));
    		return;
    	}
    }
    
    /**
     * Product view
     *
     * @return void
     */
    public function product_viewAction()
    {
    	$product = Mage::getModel('catalog/product')->load($this->getRequest()->getParam('id', false));
    	$this->loadLayout(false);
    	$this->getLayout()->getBlock('bfc_checkout.product')->setProduct($product);
    	$this->renderLayout();
    }
    
    /**
	 * Gift Action
	 *
	 * @return void
	 */
	public function giftAction()
	{
		$this->loadLayout(false);
		$this->renderLayout();
	}
	
	/**
	 * Add gift options
	 *
	 * @return void
	 */
	public function giftPostAction()
	{
		$response = array();
		$quote = $this->_getOnepage()->getQuote();
		$data = $this->getRequest()->getParam('giftmessage');
		
		if (is_array($data)) {
			foreach ($data as $entityId => $message) {
				$giftMessage = Mage::getModel('giftmessage/message');
				
				switch ($message['type']) {
					case 'quote': 
						$entity = $quote;
						break;
					case 'quote_item':
						$entity = $quote->getItemById($entityId);
						break;
					case 'quote_address':
						$entity = $quote->getAddressById($message['address'])->getItemById($entityId);
						break;
					default:
						$entity = $quote;
						break;
				}
				
				if($entity->getGiftMessageId()) {
                    $giftMessage->load($entity->getGiftMessageId());
                }
                
                if(trim($message['message']) == '') {
                    if($giftMessage->getId()) {
                        try{
                            $giftMessage->delete();
                            $entity->setGiftMessageId(0)->save();
                        } catch (Exception $e) { 
                        	$response['success'] = false;
                        	$response['error'] = true;
                        	$response['message'] = $e->getMessage();
                        	$this->getResponse()->setBody(Zend_Json::encode($response));
                        }
                    }
                    continue;
                }
                
                try {
                    $giftMessage->setSender($message['from'])
                        ->setRecipient($message['to'])
                        ->setMessage($message['message'])
                        ->save();

                    $entity->setGiftMessageId($giftMessage->getId())->save();
                } catch (Exception $e) { 
                	$response['success'] = false;
                	$response['error'] = true;
                	$response['message'] = $e->getMessage();
                	$this->getResponse()->setBody(Zend_Json::encode($response));
                }
			}
		}
		
		$response['success'] = true;
    	$response['error'] = false;
    	$response['message'] = $this->__('Gift message was added');
    	$response['close'] = true;
    	$this->getResponse()->setBody(Zend_Json::encode($response));
	}
	
	/**
     * Get one page checkout model
     *
     * @return Mage_Checkout_Model_Type_Onepage
     */
    public function getOnepage()
    {
        return Mage::getSingleton('checkout/type_onepage');
    }
}
