<?php
namespace Opencart\Admin\Controller\Extension\OcnSlider\Module;
/**
 * Class Slider
 *
 * @package Opencart\Admin\Controller\Extension\OcnSlider\Module
 */
class Slider extends \Opencart\System\Engine\Controller {
	/**
	 * Index
	 *
	 * @return void
	 */
	public function index(): void {
		$this->load->language('extension/ocn_slider/module/slider');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module')
		];

		if (!isset($this->request->get['module_id'])) {
			$data['breadcrumbs'][] = [
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/ocn_slider/module/slider', 'user_token=' . $this->session->data['user_token'])
			];
		} else {
			$data['breadcrumbs'][] = [
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/ocn_slider/module/slider', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'])
			];
		}

		if (!isset($this->request->get['module_id'])) {
			$data['save'] = $this->url->link('extension/ocn_slider/module/slider.save', 'user_token=' . $this->session->data['user_token']);
		} else {
			$data['save'] = $this->url->link('extension/ocn_slider/module/slider.save', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id']);
		}

		$data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module');

		$module_info = [];

		if (isset($this->request->get['module_id'])) {
			$this->load->model('setting/module');

			$module_info = $this->model_setting_module->getModule((int)$this->request->get['module_id']);

			if (!is_array($module_info)) {
				$module_info = [];
			}
		}

		$data['name'] = $module_info['name'] ?? '';
		$data['effect'] = (($module_info['effect'] ?? '') === 'fade') ? 'fade' : 'slide';
		$data['interval'] = (int)($module_info['interval'] ?? 5000);
		$data['autoplay'] = array_key_exists('autoplay', $module_info) ? (int)$module_info['autoplay'] : 1;
		$data['pause'] = array_key_exists('pause', $module_info) ? (int)$module_info['pause'] : 1;
		$data['arrows'] = array_key_exists('arrows', $module_info) ? (int)$module_info['arrows'] : 1;
		$data['dots'] = array_key_exists('dots', $module_info) ? (int)$module_info['dots'] : 1;
		$data['width'] = (int)($module_info['width'] ?? 1140);
		$data['height'] = (int)($module_info['height'] ?? 380);
		$data['status'] = (int)($module_info['status'] ?? 1);

		if (isset($this->request->get['module_id'])) {
			$data['module_id'] = (int)$this->request->get['module_id'];
		} else {
			$data['module_id'] = 0;
		}

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		$this->load->model('tool/image');

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		$data['slides'] = [];

		if (isset($module_info['slide']) && is_array($module_info['slide'])) {
			$rows = array_values(array_filter($module_info['slide'], 'is_array'));

			usort($rows, function (array $a, array $b): int {
				return ((int)($a['sort_order'] ?? 0)) <=> ((int)($b['sort_order'] ?? 0));
			});

			foreach ($rows as $row) {
				$image = (string)($row['image'] ?? '');
				$decoded = html_entity_decode($image, ENT_QUOTES, 'UTF-8');
				$thumb = $data['placeholder'];

				if ($decoded !== '' && is_file(DIR_IMAGE . $decoded)) {
					$thumb = $this->model_tool_image->resize($decoded, 100, 100);
				}

				$data['slides'][] = [
					'image'       => $image,
					'thumb'       => $thumb,
					'link'        => (string)($row['link'] ?? ''),
					'sort_order'  => (int)($row['sort_order'] ?? 0),
					'description' => is_array($row['description'] ?? null) ? $row['description'] : []
				];
			}
		}

		$data['slide_next'] = count($data['slides']);

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/ocn_slider/module/slider', $data));
	}

	/**
	 * Save
	 *
	 * @return void
	 */
	public function save(): void {
		$this->load->language('extension/ocn_slider/module/slider');

		$json = [];

		if (!$this->user->hasPermission('modify', 'extension/ocn_slider/module/slider')) {
			$json['error']['warning'] = $this->language->get('error_permission');
		}

		$post_info = $this->request->post + [
			'module_id' => 0,
			'name'      => '',
			'interval'  => 0,
			'width'     => 0,
			'height'    => 0
		];

		if (!oc_validate_length((string)$post_info['name'], 3, 64)) {
			$json['error']['name'] = $this->language->get('error_name');
		}

		$width = (int)$post_info['width'];
		$height = (int)$post_info['height'];
		$interval = (int)$post_info['interval'];
		$autoplay = empty($post_info['autoplay']) ? 0 : 1;

		if ($width < 1 || $width > 4000) {
			$json['error']['width'] = $this->language->get('error_width');
		}

		if ($height < 1 || $height > 4000) {
			$json['error']['height'] = $this->language->get('error_height');
		}

		if ($autoplay && ($interval < 1000 || $interval > 60000)) {
			$json['error']['interval'] = $this->language->get('error_interval');
		}

		$slides = [];

		if (isset($post_info['slide']) && is_array($post_info['slide'])) {
			foreach ($post_info['slide'] as $slide) {
				if (!is_array($slide)) {
					continue;
				}

				$image = trim((string)($slide['image'] ?? ''));
				$link = str_replace(["\r", "\n"], '', trim((string)($slide['link'] ?? '')));
				$descriptions = [];
				$has_text = false;

				if (isset($slide['description']) && is_array($slide['description'])) {
					foreach ($slide['description'] as $language_id => $description) {
						if (!is_array($description)) {
							continue;
						}

						$title = trim((string)($description['title'] ?? ''));
						$text = trim((string)($description['text'] ?? ''));

						if (oc_strlen($title) > 255) {
							$json['error']['warning'] = $this->language->get('error_title');
						}

						if (oc_strlen($text) > 2000) {
							$json['error']['warning'] = $this->language->get('error_text');
						}

						if ($title !== '' || $text !== '') {
							$has_text = true;
						}

						$descriptions[(int)$language_id] = [
							'title' => $title,
							'text'  => $text
						];
					}
				}

				if (oc_strlen($link) > 1024) {
					$json['error']['warning'] = $this->language->get('error_link');
				}

				if ($image === '' && $link === '' && !$has_text) {
					continue;
				}

				$slides[] = [
					'image'       => $image,
					'link'        => $link,
					'sort_order'  => (int)($slide['sort_order'] ?? 0),
					'description' => $descriptions
				];
			}
		}

		usort($slides, function (array $a, array $b): int {
			return $a['sort_order'] <=> $b['sort_order'];
		});

		if (!$json) {
			$post_info['name'] = (string)$post_info['name'];
			$post_info['width'] = $width;
			$post_info['height'] = $height;
			$post_info['interval'] = $interval > 0 ? $interval : 5000;
			$post_info['autoplay'] = $autoplay;
			$post_info['pause'] = empty($post_info['pause']) ? 0 : 1;
			$post_info['arrows'] = empty($post_info['arrows']) ? 0 : 1;
			$post_info['dots'] = empty($post_info['dots']) ? 0 : 1;
			$post_info['effect'] = (($post_info['effect'] ?? '') === 'fade') ? 'fade' : 'slide';
			$post_info['status'] = empty($post_info['status']) ? 0 : 1;
			$post_info['slide'] = $slides;
			$post_info['module_id'] = (int)$post_info['module_id'];

			$this->load->model('setting/module');

			if (!$post_info['module_id']) {
				$json['module_id'] = $this->model_setting_module->addModule('ocn_slider.slider', $post_info);
			} else {
				$this->model_setting_module->editModule($post_info['module_id'], $post_info);
			}

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
