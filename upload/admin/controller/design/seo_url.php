<?php
namespace Opencart\Admin\Controller\Design;
use \Opencart\System\Helper AS Helper;
class SeoUrl extends \Opencart\System\Engine\Controller {
	public function index(): void {
		$this->load->language('design/seo_url');

		$this->document->setTitle($this->language->get('heading_title'));

		if (isset($this->request->get['filter_keyword'])) {
			$filter_keyword = (string)$this->request->get['filter_keyword'];
		} else {
			$filter_keyword = '';
		}

		if (isset($this->request->get['filter_key'])) {
			$filter_key = (string)$this->request->get['filter_key'];
		} else {
			$filter_key = '';
		}

		if (isset($this->request->get['filter_value'])) {
			$filter_value = (string)$this->request->get['filter_value'];
		} else {
			$filter_value = '';
		}

		if (isset($this->request->get['filter_store_id'])) {
			$filter_store_id = (int)$this->request->get['filter_store_id'];
		} else {
			$filter_store_id = '';
		}

		if (isset($this->request->get['filter_language_id'])) {
			$filter_language_id = (int)$this->request->get['filter_language_id'];
		} else {
			$filter_language_id = 0;
		}

		$url = '';

		if (isset($this->request->get['filter_keyword'])) {
			$url .= '&filter_keyword=' . urlencode(html_entity_decode((string)$this->request->get['filter_keyword'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_key'])) {
			$url .= '&filter_key=' . urlencode(html_entity_decode((string)$this->request->get['filter_key'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_value'])) {
			$url .= '&filter_value=' . urlencode(html_entity_decode((string)$this->request->get['filter_value'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_store_id'])) {
			$url .= '&filter_store_id=' . (int)$this->request->get['filter_store_id'];
		}

		if (isset($this->request->get['filter_language_id'])) {
			$url .= '&filter_language_id=' . (int)$this->request->get['filter_language_id'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . (string)$this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . (string)$this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . (int)$this->request->get['page'];
		}

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('design/seo_url', 'user_token=' . $this->session->data['user_token'] . $url)
		];

		$data['add'] = $this->url->link('design/seo_url|form', 'user_token=' . $this->session->data['user_token'] . $url);
		$data['delete'] = $this->url->link('design/seo_url|delete', 'user_token=' . $this->session->data['user_token']);

		$data['list'] = $this->getList();

		$this->load->model('setting/store');

		$data['stores'] = $this->model_setting_store->getStores();

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		$data['filter_keyword'] = $filter_keyword;
		$data['filter_key'] = $filter_key;
		$data['filter_value'] = $filter_value;
		$data['filter_store_id'] = $filter_store_id;
		$data['filter_language_id'] = $filter_language_id;

		$data['user_token'] = $this->session->data['user_token'];

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('design/seo_url', $data));
	}

	public function list(): void {
		$this->load->language('design/seo_url');

		$this->response->setOutput($this->getList());
	}
	
	protected function getList(): string {
		if (isset($this->request->get['filter_keyword'])) {
			$filter_keyword = (string)$this->request->get['filter_keyword'];
		} else {
			$filter_keyword = '';
		}

		if (isset($this->request->get['filter_key'])) {
			$filter_key = (string)$this->request->get['filter_key'];
		} else {
			$filter_key = '';
		}

		if (isset($this->request->get['filter_value'])) {
			$filter_value = (string)$this->request->get['filter_value'];
		} else {
			$filter_value = '';
		}

		if (isset($this->request->get['filter_store_id'])) {
			$filter_store_id = (int)$this->request->get['filter_store_id'];
		} else {
			$filter_store_id = '';
		}

		if (isset($this->request->get['filter_language_id'])) {
			$filter_language_id = (int)$this->request->get['filter_language_id'];
		} else {
			$filter_language_id = 0;
		}

		if (isset($this->request->get['sort'])) {
			$sort = (string)$this->request->get['sort'];
		} else {
			$sort = 'key';
		}

		if (isset($this->request->get['order'])) {
			$order = (string)$this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_keyword'])) {
			$url .= '&filter_keyword=' . urlencode(html_entity_decode((string)$this->request->get['filter_keyword'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_key'])) {
			$url .= '&filter_key=' . urlencode(html_entity_decode((string)$this->request->get['filter_key'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_value'])) {
			$url .= '&filter_value=' . urlencode(html_entity_decode((string)$this->request->get['filter_value'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_store_id'])) {
			$url .= '&filter_store_id=' . (int)$this->request->get['filter_store_id'];
		}

		if (isset($this->request->get['filter_language_id'])) {
			$url .= '&filter_language_id=' . (int)$this->request->get['filter_language_id'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . (string)$this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . (string)$this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . (int)$this->request->get['page'];
		}

		$data['action'] = $this->url->link('design/seo_url|list', 'user_token=' . $this->session->data['user_token'] . $url);

		$data['seo_urls'] = [];

		$filter_data = [
			'filter_keyword'     => $filter_keyword,
			'filter_key'         => $filter_key,
			'filter_value'       => $filter_value,
			'filter_store_id'    => $filter_store_id,
			'filter_language_id' => $filter_language_id,
			'sort'               => $sort,
			'order'              => $order,
			'start'              => ($page - 1) * $this->config->get('config_pagination_admin'),
			'limit'              => $this->config->get('config_pagination_admin')
		];

		$this->load->model('design/seo_url');
		$this->load->model('localisation/language');

		$seo_url_total = $this->model_design_seo_url->getTotalSeoUrls($filter_data);

		$results = $this->model_design_seo_url->getSeoUrls($filter_data);

		foreach ($results as $result) {
			$language_info = $this->model_localisation_language->getLanguage($result['language_id']);

			if ($language_info) {
				$code = $language_info['code'];
			} else {
				$code = '';
			}

			$data['seo_urls'][] = [
				'seo_url_id' => $result['seo_url_id'],
				'keyword'    => $result['keyword'],
				'key'        => $result['key'],
				'value'      => $result['value'],
				'store'      => $result['store_id'] ? $result['store'] : $this->language->get('text_default'),
				'language'   => $code,
				'edit'       => $this->url->link('design/seo_url|form', 'user_token=' . $this->session->data['user_token'] . '&seo_url_id=' . $result['seo_url_id'] . $url)
			];
		}

		$url = '';

		if (isset($this->request->get['filter_keyword'])) {
			$url .= '&filter_keyword=' . urlencode(html_entity_decode((string)$this->request->get['filter_keyword'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_key'])) {
			$url .= '&filter_key=' . urlencode(html_entity_decode((string)$this->request->get['filter_key'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_value'])) {
			$url .= '&filter_value=' . urlencode(html_entity_decode((string)$this->request->get['filter_value'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_store_id'])) {
			$url .= '&filter_store_id=' . (int)$this->request->get['filter_store_id'];
		}

		if (isset($this->request->get['filter_language_id'])) {
			$url .= '&filter_language_id=' . (int)$this->request->get['filter_language_id'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		$data['sort_keyword'] = $this->url->link('design/seo_url|list', 'user_token=' . $this->session->data['user_token'] . '&sort=keyword' . $url);
		$data['sort_key'] = $this->url->link('design/seo_url|list', 'user_token=' . $this->session->data['user_token'] . '&sort=key' . $url);
		$data['sort_value'] = $this->url->link('design/seo_url|list', 'user_token=' . $this->session->data['user_token'] . '&sort=value' . $url);
		$data['sort_store'] = $this->url->link('design/seo_url|list', 'user_token=' . $this->session->data['user_token'] . '&sort=store' . $url);
		$data['sort_language'] = $this->url->link('design/seo_url|list', 'user_token=' . $this->session->data['user_token'] . '&sort=language' . $url);

		$url = '';

		if (isset($this->request->get['filter_keyword'])) {
			$url .= '&filter_keyword=' . urlencode(html_entity_decode((string)$this->request->get['filter_keyword'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_key'])) {
			$url .= '&filter_key=' . urlencode(html_entity_decode((string)$this->request->get['filter_key'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_value'])) {
			$url .= '&filter_value=' . urlencode(html_entity_decode((string)$this->request->get['filter_value'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_store_id'])) {
			$url .= '&filter_store_id=' . (int)$this->request->get['filter_store_id'];
		}

		if (isset($this->request->get['filter_language_id'])) {
			$url .= '&filter_language_id=' . (int)$this->request->get['filter_language_id'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . (string)$this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . (string)$this->request->get['order'];
		}

		$data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $seo_url_total,
			'page'  => $page,
			'limit' => $this->config->get('config_pagination_admin'),
			'url'   => $this->url->link('design/seo_url|list', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}')
		]);

		$data['results'] = sprintf($this->language->get('text_pagination'), ($seo_url_total) ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_pagination_admin')) > ($seo_url_total - $this->config->get('config_pagination_admin'))) ? $seo_url_total : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')), $seo_url_total, ceil($seo_url_total / $this->config->get('config_pagination_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		return $this->load->view('design/seo_url_list', $data);
	}

	public function form(): void {
		$this->load->language('design/seo_url');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['text_form'] = !isset($this->request->get['seo_url_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		$url = '';

		if (isset($this->request->get['filter_keyword'])) {
			$url .= '&filter_keyword=' . urlencode(html_entity_decode((string)$this->request->get['filter_keyword'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_key'])) {
			$url .= '&filter_key=' . urlencode(html_entity_decode((string)$this->request->get['filter_key'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_value'])) {
			$url .= '&filter_value=' . urlencode(html_entity_decode((string)$this->request->get['filter_value'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_store_id'])) {
			$url .= '&filter_store_id=' . (int)$this->request->get['filter_store_id'];
		}

		if (isset($this->request->get['filter_language_id'])) {
			$url .= '&filter_language_id=' . (int)$this->request->get['filter_language_id'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . (string)$this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . (string)$this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . (int)$this->request->get['page'];
		}

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('design/seo_url', 'user_token=' . $this->session->data['user_token'] . $url)
		];

		$data['save'] = $this->url->link('design/seo_url|save', 'user_token=' . $this->session->data['user_token']);
		$data['back'] = $this->url->link('design/seo_url', 'user_token=' . $this->session->data['user_token'] . $url);

		if (isset($this->request->get['seo_url_id'])) {
			$this->load->model('design/seo_url');

			$seo_url_info = $this->model_design_seo_url->getSeoUrl($this->request->get['seo_url_id']);
		}

		if (isset($this->request->get['seo_url_id'])) {
			$data['seo_url_id'] = (int)$this->request->get['seo_url_id'];
		} else {
			$data['seo_url_id'] = 0;
		}

		$data['stores'] = [];

		$data['stores'][] = [
			'store_id' => 0,
			'name'     => $this->language->get('text_default')
		];

		$this->load->model('setting/store');

		$stores = $this->model_setting_store->getStores();

		foreach ($stores as $store) {
			$data['stores'][] = [
				'store_id' => $store['store_id'],
				'name'     => $store['name']
			];
		}

		if (!empty($seo_url_info)) {
			$data['store_id'] = $seo_url_info['store_id'];
		} else {
			$data['store_id'] = '';
		}

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (!empty($seo_url_info)) {
			$data['language_id'] = $seo_url_info['language_id'];
		} else {
			$data['language_id'] = '';
		}

		if (!empty($seo_url_info)) {
			$data['key'] = $seo_url_info['key'];
		} else {
			$data['key'] = '';
		}

		if (!empty($seo_url_info)) {
			$data['value'] = $seo_url_info['value'];
		} else {
			$data['value'] = '';
		}

		if (!empty($seo_url_info)) {
			$data['keyword'] = $seo_url_info['keyword'];
		} else {
			$data['keyword'] = '';
		}

		if (!empty($seo_url_info)) {
			$data['sort_order'] = $seo_url_info['sort_order'];
		} else {
			$data['sort_order'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('design/seo_url_form', $data));
	}

	public function save(): void {
		$this->load->language('design/seo_url');

		$json = [];

		if (!$this->user->hasPermission('modify', 'design/seo_url')) {
			$json['error']['warning'] = $this->language->get('error_permission');
		}

		if ((Helper\Utf8\strlen($this->request->post['key']) < 1) || (Helper\Utf8\strlen($this->request->post['key']) > 64)) {
			$json['error']['key'] = $this->language->get('error_key');
		}

		if ((Helper\Utf8\strlen($this->request->post['value']) < 1) || (Helper\Utf8\strlen($this->request->post['value']) > 255)) {
			$json['error']['value'] = $this->language->get('error_value');
		}

		$this->load->model('design/seo_url');

		// Check if there is already a key value pair on the same store using the same language
		$seo_url_info = $this->model_design_seo_url->getSeoUrlByKeyValue($this->request->post['key'], $this->request->post['value'], $this->request->post['store_id'], $this->request->post['language_id']);

		if ($seo_url_info && (!isset($this->request->post['seo_url_id']) || $seo_url_info['seo_url_id'] != (int)$this->request->post['seo_url_id'])) {
			$json['error']['value'] = $this->language->get('error_value_exists');
		}

		if (preg_match('/[^a-zA-Z0-9_-]|[\p{Cyrillic}]+/u', $this->request->post['keyword'])) {
			$json['error']['keyword'] = $this->language->get('error_keyword');
		}

		// Check if keyword already exists and on the same store using the same language
		$seo_url_info = $this->model_design_seo_url->getSeoUrlByKeyword($this->request->post['keyword'], $this->request->post['store_id'], $this->request->post['language_id']);

		if ($seo_url_info && (!isset($this->request->post['seo_url_id']) || $seo_url_info['seo_url_id'] != $this->request->post['seo_url_id'])) {
			$json['error']['keyword'] = $this->language->get('error_keyword_exists');
		}

		if (!$json) {
			if (!$this->request->post['seo_url_id']) {
				$json['seo_url_id'] = $this->model_design_seo_url->addSeoUrl($this->request->post);
			} else {
				$this->model_design_seo_url->editSeoUrl($this->request->post['seo_url_id'], $this->request->post);
			}

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function delete(): void {
		$this->load->language('design/seo_url');

		$json = [];

		if (isset($this->request->post['selected'])) {
			$selected = $this->request->post['selected'];
		} else {
			$selected = [];
		}

		if (!$this->user->hasPermission('modify', 'design/seo_url')) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (!$json) {
			$this->load->model('design/seo_url');

			foreach ($selected as $seo_url_id) {
				$this->model_design_seo_url->deleteSeoUrl($seo_url_id);
			}

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
