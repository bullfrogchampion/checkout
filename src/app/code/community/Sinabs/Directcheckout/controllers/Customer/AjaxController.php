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
 * @category    Sinabs
 * @package     Sinabs_Directcheckout
 * @copyright   Copyright (c) 2014 Sinabs (http://www.sinabs.fr)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Sinabs_Directcheckout_Customer_AjaxController extends Mage_Core_Controller_Front_Action
{
	/**
	 * Authentication form
	 *
	 */
	public function loginAction()
    {
    	$this->loadLayout(false);
    	$this->renderLayout();
    }
    
    /**
     * Authentication form post action
     *
     */
    public function loginPostAction()
    {
        $login = $this->getRequest()->getParam('login');
        $password = $this->getRequest()->getParam('password');
        $response = array('success' => false);
        
        if ($login && $password) {
            try {
                Mage::getsingleton('customer/session')->login($login, $password);
            } catch (Exception $e) {
            	$response['success'] = false;
                $response['message'] = $e->getMessage();
                $this->getResponse()->setBody(Zend_Json::encode($response));
                return;
            }
            
            $response['success'] = true;
            $response['message'] = $this->__('Successful authentication, you will be redirected in two seconds');
            $response['redirect'] = Mage::getUrl('directcheckout');
            $response['timeout'] = 2000;
        } else {
        	$response['success'] = false;
        	$response['message'] = $this->__('All fields are required');
        }
        
        $this->getResponse()->setBody(Zend_Json::encode($response));
    }
    
    /**
     * Forgot password form
     *
     */
    public function forgetAction()
    {
    	$this->loadLayout(false);
    	$this->renderLayout();
    }
    
    /**
     * Forgot password form post action
     *
     */
    public function forgetPostAction()
    {
    	$email = (string) $this->getRequest()->getPost('customer-email');
    	
    	if ($email) {
    		$result = array();
    		if (!Zend_Validate::is($email, 'EmailAddress')) {
    			$result['success'] = false;
    			$result['message'] = $this->__('Invalid email address.');
    		}
    		
    		$customer = Mage::getModel('customer/customer')
    			->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
    			->loadByEmail($email);
    			
    		if ($customer->getId()) {
    			try {
    				$newResetPasswordLinkToken = Mage::helper('customer')->generateResetPasswordLinkToken();
                    $customer->changeResetPasswordLinkToken($newResetPasswordLinkToken);
                    $customer->sendPasswordResetConfirmationEmail();
    			} catch (Exception $e) {
					$result['error'] = 1;
					$result['message'] = $e->getMessage();
    			}
    		}
    		$result['success'] = true;
    		$result['message'] = Mage::helper('customer')->__('If there is an account associated with %s you will receive an email with a link to reset your password.', Mage::helper('customer')->htmlEscape($email));
    	} else {
    		$result['success'] = false;
    		$result['message'] = $this->__('Please enter your email.');
    	}
    	
    	$this->getResponse()->setBody(Zend_Json::encode($result));
    }
}