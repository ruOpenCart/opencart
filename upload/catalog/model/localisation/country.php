<?php
namespace Opencart\Catalog\Model\Localisation;
/**
 * Class Country
 *
 * Can be called using $this->load->model('localisation/country');
 *
 * @package Opencart\Catalog\Model\Localisation
 */
class Country extends \Opencart\System\Engine\Model {
	/**
	 * Get Country
	 *
	 * @param int $country_id primary key of the country record
	 *
	 * @return array<string, mixed> country record that has country ID
	 *
	 * @example
	 *
	 * $this->load->model('localisation/country');
	 *
	 * $country_info = $this->model_localisation_country->getCountry($country_id);
	 */
	public function getCountry(int $country_id): array {
		$query = $this->db->query("SELECT `c`.*, `cd`.`name` FROM `" . DB_PREFIX . "country` `c` LEFT JOIN `" . DB_PREFIX . "country_description` `cd` ON (`c`.`country_id` = `cd`.`country_id`) WHERE `c`.`country_id` = '" . (int)$country_id . "' AND `zd`.`language_id` = '" . (int)$this->config->get('config_language_id') . "' AND `c`.`status` = '1'");

		return $query->row;
	}

	/**
	 * Get Country By Iso Code 2
	 *
	 * @param string $iso_code_2
	 *
	 * @return array<int, array<string, mixed>>
	 *
	 * @example
	 *
	 * $this->load->model('localisation/country');
	 *
	 * $country_info = $this->model_localisation_country->getCountryByIsoCode2($iso_code_2);
	 */
	public function getCountryByIsoCode2(string $iso_code_2): array {
		$query = $this->db->query("SELECT `c`.*, `cd`.`name` FROM `" . DB_PREFIX . "country` `c` LEFT JOIN `" . DB_PREFIX . "country_description` `cd` ON (`c`.`country_id` = `cd`.`country_id`) WHERE `iso_code_2` = '" . $this->db->escape($iso_code_2) . "' AND `zd`.`language_id` = '" . (int)$this->config->get('config_language_id') . "' AND `c`.`status` = '1'");

		$key = md5($sql);

		$country_data = $this->cache->get('country.' . $key);

		if (!$country_data) {
			$query = $this->db->query($sql);

			$country_data = $query->rows;

			$this->cache->set('country.' . $key, $country_data);
		}

		return $country_data;
	}

	/**
	 * Get Country By Iso Code 3
	 *
	 * @param string $iso_code_3
	 *
	 * @return array<int, array<string, mixed>>
	 *
	 * @example
	 *
	 * $this->load->model('localisation/country');
	 *
	 * $country_info = $this->model_localisation_country->getCountryByIsoCode3($iso_code_3);
	 */
	public function getCountryByIsoCode3(string $iso_code_3): array {
		$query = $this->db->query("SELECT `c`.*, `cd`.`name` FROM `" . DB_PREFIX . "country` `c` LEFT JOIN `" . DB_PREFIX . "country_description` `cd` ON (`c`.`country_id` = `cd`.`country_id`) WHERE `iso_code_3` = '" . $this->db->escape($iso_code_3) . "' AND `zd`.`language_id` = '" . (int)$this->config->get('config_language_id') . "' AND `c`.`status` = '1'");

		$key = md5($sql);

		$country_data = $this->cache->get('country.' . $key);

		if (!$country_data) {
			$query = $this->db->query($sql);

			$country_data = $query->rows;

			$this->cache->set('country.' . md5($sql), $country_data);
		}

		return $country_data;
	}

	/**
	 * Get Countries
	 *
	 * @return array<int, array<string, mixed>> country records
	 *
	 * @example
	 *
	 * $this->load->model('localisation/country');
	 *
	 * $countries = $this->model_localisation_country->getCountries();
	 */
	public function getCountries(): array {
		$sql = "SELECT `c`.*, `cd`.`name` FROM `" . DB_PREFIX . "country` `c` LEFT JOIN `" . DB_PREFIX . "country_description` `cd` ON (`c`.`country_id` = `cd`.`country_id`) WHERE `c`.`status` = '1' AND `cd`.`language_id` = '" . (int)$this->config->get('config_language_id') . "' ORDER BY `cd`.`name` ASC";

		$key = md5($sql);

		$country_data = $this->cache->get('country.' . $key);

		if (!$country_data) {
			$query = $this->db->query($sql);

			$country_data = $query->rows;

			$this->cache->set('country.' . $key, $country_data);
		}

		return $country_data;
	}
}
