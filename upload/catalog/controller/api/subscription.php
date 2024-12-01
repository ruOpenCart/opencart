<?php
namespace Opencart\Catalog\Controller\Api;
/**
 * Class Subscription
 *
 * Subscription API
 *
 * @package Opencart\Catalog\Controller\Api
 */
class Subscription extends \Opencart\System\Engine\Controller {
	/**
	 * Index
	 *
	 * @return void
	 */
	public function index(): void {
		$this->load->language('api/subscription');

		if (isset($this->request->get['call'])) {
			$call = $this->request->get['call'];
		} else {
			$call = '';
		}

		// Allowed calls
		switch ($call) {
			case 'cart':
				$output = $this->getCart();
				break;
			case 'product_add':
				$output = $this->addProduct();
				break;
			case 'shipping_methods':
				$output = $this->getShippingMethods();
				break;
			case 'payment_methods':
				$output = $this->getPaymentMethods();
				break;
			case 'payment_methods':
				$output = $this->getPaymentMethods();
				break;
			case 'confirm':
				$output = $this->confirm();
				break;
			case 'history_add':
				$output = $this->addHistory();
				break;
			default:
				$output = ['error' => $this->language->get('error_call')]; // JSON error message if call not found
				break;
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($output));
	}

	/**
	 * Set Customer
	 *
	 * @return array
	 */
	protected function setCustomer(): array {
		$this->load->language('api/order');

		$output = [];

		// Customer
		if (isset($this->request->post['customer_id'])) {
			$customer_id = (int)$this->request->post['customer_id'];
		} else {
			$customer_id = 0;
		}

		$this->load->model('account/customer');

		$customer_info = $this->model_account_customer->getCustomer($customer_id);

		if (!$customer_info) {
			$output['error'] = $this->language->get('error_customer');
		}

		if (!$output) {
			// Log the customer in
			$this->customer->login($customer_info['email'], '', true);

			$this->session->data['customer'] = $customer_info;

			$output['success'] = $this->language->get('text_success');
		}

		return $output;
	}

	/**
	 * Set Payment Address
	 *
	 * @return array
	 */
	protected function setPaymentAddress(): array {
		$this->load->language('api/order');

		$output = [];

		if (isset($this->request->post['payment_address_id'])) {
			$address_id = (int)$this->request->post['payment_address_id'];
		} else {
			$address_id = 0;
		}

		$this->load->model('account/address');

		$address_info = $this->model_account_address->getAddress($this->customer->getId(), $address_id);

		if (!$address_info) {
			$output['error'] = $this->language->get('error_payment_address');
		}

		if (!$output) {
			$this->session->data['payment_address'] = $address_info;

			$output['success'] = $this->language->get('text_success');
		}

		return $output;
	}

	/**
	 * Set shipping address
	 *
	 * @return array
	 */
	protected function setShippingAddress(): array {
		$this->load->language('api/order');

		$output = [];

		if (isset($this->request->post['shipping_address_id'])) {
			$address_id = (int)$this->request->post['shipping_address_id'];
		} else {
			$address_id = 0;
		}

		$this->load->model('account/address');

		$address_info = $this->model_account_address->getAddress($this->customer->getId(), $address_id);

		if (!$address_info) {
			$output['error'] = $this->language->get('error_shipping_address');
		}

		if (!$output) {
			$this->session->data['shipping_address'] = $address_info;

			$output['success'] = $this->language->get('text_success');
		}

		return $output;
	}

	/**
	 * Get shipping methods
	 *
	 * @return array
	 */
	protected function getShippingMethods(): array {
		$this->setCustomer();

		$output = $this->load->controller('api/cart');

		if (isset($output['error'])) {
			return $output;
		}

		$this->setPaymentAddress();
		$this->setShippingAddress();

		return $this->load->controller('api/shipping_method.getShippingMethods');
	}

	/**
	 * Get payment methods
	 *
	 * @return array
	 */
	protected function getPaymentMethods(): array {
		$this->setCustomer();

		$output = $this->load->controller('api/cart');

		if (isset($output['error'])) {
			return $output;
		}

		$this->setPaymentAddress();
		$this->setShippingAddress();

		$this->load->controller('api/shipping_method');

		return $this->load->controller('api/payment_method.getPaymentMethods');
	}

	/**
	 * Set payment method
	 *
	 * @return array
	 */
	protected function setPaymentMethod(): array {
		$this->setCustomer();

		$output = $this->load->controller('api/cart');

		if (isset($output['error'])) {
			return $output;
		}

		$this->setPaymentAddress();
		$this->setShippingAddress();

		$this->load->controller('api/shipping_method');

		$output = $this->load->controller('api/payment_method');

		$output['products'] = $this->load->controller('api/cart.getProducts');
		$output['shipping_required'] = $this->cart->hasShipping();

		return $output;
	}

	/**
	 * Get cart
	 *
	 * @return array
	 */
	protected function getCart(): array {
		$this->setCustomer();

		// If any errors at the cart level such as products don't exist then we want to return the error
		$output = $this->load->controller('api/cart');

		$this->setPaymentAddress();
		$this->setShippingAddress();

		$this->load->controller('api/shipping_method');
		$this->load->controller('api/payment_method');

		$output['products'] = $this->load->controller('api/cart.getProducts');
		$output['shipping_required'] = $this->cart->hasShipping();

		return $output;
	}

	/**
	 * Add product
	 *
	 * @return array
	 */
	protected function addProduct(): array {
		$this->setCustomer();

		$output = $this->load->controller('api/cart');

		if (isset($output['error'])) {
			return $output;
		}
		
		$this->setPaymentAddress();
		$this->setShippingAddress();

		$output = $this->load->controller('api/cart.addProduct');

		$output['products'] = $this->load->controller('api/cart.getProducts');
		$output['shipping_required'] = $this->cart->hasShipping();

		return $output;
	}

	/**
	 * Confirm
	 *
	 * @return array
	 */
	protected function confirm(): array {
		$this->setCustomer();

		$output = $this->load->controller('api/cart');

		if (isset($output['error'])) {
			return $output;
		}

		$output = [];

		$this->setPaymentAddress();
		$this->setShippingAddress();

		$this->load->controller('api/shipping_method');
		$this->load->controller('api/payment_method');

		$this->load->language('sale/subscription');

		// 1. Validate customer data exists
		if (!isset($this->session->data['customer'])) {
			$output['error']['customer'] = $this->language->get('error_customer');
		}

		// Subscription Plan
		$this->load->model('catalog/subscription_plan');

		$subscription_plan_info = $this->model_catalog_subscription_plan->getSubscriptionPlan($this->request->post['subscription_plan_id']);

		if (!$subscription_plan_info) {
			$output['error']['subscription_plan'] = $this->language->get('error_subscription_plan');
		}

		// 2. Validate cart has products.
		if (!$this->cart->hasProducts()) {
			$output['error']['product'] = $this->language->get('error_product');
		}

		// 3. Validate cart has products and has stock
		if ((!$this->cart->hasStock() && !$this->config->get('config_stock_checkout')) || !$this->cart->hasMinimum()) {
			$output['error']['product'] = $this->language->get('error_stock');
		}

		// 4. Validate payment address if required
		if ($this->config->get('config_checkout_payment_address') && !isset($this->session->data['payment_address'])) {
			$output['error']['payment_address'] = $this->language->get('error_payment_address');
		}

		// 5. Validate shipping address and method if required
		if ($this->cart->hasShipping()) {
			// Shipping Address
			if (!isset($this->session->data['shipping_address'])) {
				$output['error']['shipping_address'] = $this->language->get('error_shipping_address');
			}

			// Validate shipping method
			if (!isset($this->session->data['shipping_method'])) {
				$output['error']['shipping_method'] = $this->language->get('error_shipping_method');
			}
		} else {
			unset($this->session->data['shipping_address']);
			unset($this->session->data['shipping_method']);
		}

		// 6. Validate payment method
		if (!isset($this->session->data['payment_method'])) {
			$output['error']['payment_method'] = $this->language->get('error_payment_method');
		}

		if (!$output) {
			$subscription_product_data = [];

			$products = $this->cart->getSubscriptions();

			foreach ($products as $product) {
				$subscription_product_data[] = [
					'order_product_id' => 0,
					'order_id'         => 0,
					'trial_price'      => $product['subscription']['trial_price'] * $product['quantity'],
					'price'            => $product['subscription']['price'] * $product['quantity'],
				] + $product + $product['subscription'];
			}

			$subscription_data = $subscription_plan_info + [
				'subscription_product' => $subscription_product_data,
				'trial_price'          => array_sum(array_column($subscription_product_data, 'trial_price')),
			    'price'                => array_sum(array_column($subscription_product_data, 'price')),
				'store_id'             => $this->config->get('config_store_id'),
				'language_id'          => $this->config->get('config_language_id'),
				'currency_id'          => $this->currency->getId($this->session->data['currency'])
			];

			$this->load->model('checkout/subscription');

			if (!$this->request->post['subscription_id']) {
				$output['subscription_id'] = $this->model_checkout_subscription->addSubscription($this->request->post + $subscription_data);
			} else {
				$this->model_checkout_subscription->editSubscription((int)$this->request->post['subscription_id'], $this->request->post + $subscription_data);
			}

			$output['success'] = $this->language->get('text_success');
		}

		$output['products'] = $this->load->controller('api/cart.getProducts');
		$output['shipping_required'] = $this->cart->hasShipping();

		return $output;
	}

	/**
	 * Add order history
	 *
	 * @return array
	 */
	protected function addHistory(): array {
		$this->load->language('api/subscription');

		$output = [];

		// Add keys for missing post vars
		$keys = [
			'subscription_id',
			'subscription_status_id',
			'comment',
			'notify'
		];

		foreach ($keys as $key) {
			if (!isset($this->request->post[$key])) {
				$this->request->post[$key] = '';
			}
		}

		$this->load->model('checkout/subscription');

		$subscription_info = $this->model_checkout_subscription->getSubscription((int)$this->request->post['subscription_id']);

		if (!$subscription_info) {
			$output['error'] = $this->language->get('error_subscription');
		}

		if (!$output) {
			$this->model_checkout_order->addHistory((int)$this->request->post['subscription_id'], (int)$this->request->post['subscription_status_id'], (string)$this->request->post['comment'], (bool)$this->request->post['notify']);

			$output['success'] = $this->language->get('text_success');
		}

		return $output;
	}
}
