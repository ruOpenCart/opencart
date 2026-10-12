<?php
namespace Opencart\Catalog\Controller\Extension\OcnSlider\Module;
/**
 * Class Slider
 *
 * @package Opencart\Catalog\Controller\Extension\OcnSlider\Module
 */
class Slider extends \Opencart\System\Engine\Controller {
	/**
	 * @var bool
	 */
	private static bool $assets = false;

	/**
	 * Index
	 *
	 * @param array<string, mixed> $setting
	 *
	 * @return string
	 */
	public function index(array $setting): string {
		$this->load->language('extension/ocn_slider/module/slider');
		$this->load->model('tool/image');

		static $module = 0;

		$width = (int)($setting['width'] ?? 0);
		$height = (int)($setting['height'] ?? 0);

		if ($width < 1 || $height < 1) {
			return '';
		}

		$language_id = (int)$this->config->get('config_language_id');
		$rows = $setting['slide'] ?? [];

		if (!is_array($rows)) {
			$rows = [];
		}

		$rows = array_values(array_filter($rows, 'is_array'));

		usort($rows, function (array $a, array $b): int {
			return ((int)($a['sort_order'] ?? 0)) <=> ((int)($b['sort_order'] ?? 0));
		});

		$data['slides'] = [];

		foreach ($rows as $row) {
			if (!is_array($row)) {
				continue;
			}

			$image = html_entity_decode((string)($row['image'] ?? ''), ENT_QUOTES, 'UTF-8');

			if ($image === '' || !is_file(DIR_IMAGE . $image)) {
				continue;
			}

			$resized = $this->model_tool_image->resize($image, $width, $height);

			if ($resized === '') {
				continue;
			}

			$descriptions = $row['description'] ?? [];
			$description = is_array($descriptions) ? ($descriptions[$language_id] ?? []) : [];

			if (!is_array($description)) {
				$description = [];
			}

			$data['slides'][] = [
				'image' => $resized,
				'title' => (string)($description['title'] ?? ''),
				'text'  => (string)($description['text'] ?? ''),
				'link'  => $this->link((string)($row['link'] ?? ''))
			];
		}

		if (!$data['slides']) {
			return '';
		}

		if (!self::$assets) {
			$this->document->addStyle('extension/ocn_slider/catalog/view/stylesheet/slider.css');
			$this->document->addScript('extension/ocn_slider/catalog/view/javascript/slider.js');
			self::$assets = true;
		}

		$data['module'] = $module++;
		$data['effect'] = (($setting['effect'] ?? '') === 'fade') ? 'fade' : 'slide';
		$data['interval'] = max(0, (int)($setting['interval'] ?? 5000));
		$data['autoplay'] = !empty($setting['autoplay']) ? 1 : 0;
		$data['pause'] = !empty($setting['pause']) ? 1 : 0;
		$data['arrows'] = !empty($setting['arrows']) ? 1 : 0;
		$data['dots'] = !empty($setting['dots']) ? 1 : 0;
		$data['width'] = $width;
		$data['height'] = $height;
		$data['text_slider'] = $this->language->get('text_slider');
		$data['text_prev'] = $this->language->get('text_prev');
		$data['text_next'] = $this->language->get('text_next');
		$data['text_dot'] = $this->language->get('text_dot');

		return $this->load->view('extension/ocn_slider/module/slider', $data);
	}

	/**
	 * Keep http(s) and relative links. Drop javascript: and other schemes.
	 */
	private function link(string $link): string {
		$link = trim($link);

		if ($link === '') {
			return '';
		}

		if (preg_match('#^\s*([a-z][a-z0-9+.-]*):#i', $link, $match)) {
			$scheme = strtolower($match[1]);

			if ($scheme !== 'http' && $scheme !== 'https') {
				return '';
			}
		}

		return $link;
	}
}
