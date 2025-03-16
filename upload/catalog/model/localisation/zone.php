<?php
namespace Opencart\Catalog\Model\Localisation;
/**
 * Class Zone
 *
 * Can be called using $this->load->model('localisation/zone');
 *
 * @package Opencart\Catalog\Model\Localisation
 */
class Zone extends \Opencart\System\Engine\Model {
	/**
	 * Get Zone
	 *
	 * @param int $zone_id primary key of the zone record
	 *
	 * @return array<string, mixed> zone record that has zone ID
	 *
	 * @example
	 *
	 * $this->load->model('localisation/zone');
	 *
	 * $zone_info = $this->model_localisation_zone->getZone($zone_id);
	 */
	public function getZone(int $zone_id): array {
		$query = $this->db->query("SELECT `z`.*, `zd`.`name` FROM `" . DB_PREFIX . "zone` `z` LEFT JOIN `" . DB_PREFIX . "zone` `z` LEFT JOIN `" . DB_PREFIX . "zone_description` `zd` ON (`z`.`zone_id` = `zd`.`zone_id`) WHERE `z`.`zone_id` = '" . (int)$zone_id . "' AND `zd`.`language_id` = '" . (int)$this->config->get('config_language_id') . "' AND `z`.`status` = '1'");

		return $query->row;
	}

	/**
	 * Get Zones By Country ID
	 *
	 * @param int $country_id primary key of the country record
	 *
	 * @return array<int, array<string, mixed>> zone records that have country ID
	 *
	 * @example
	 *
	 * $this->load->model('localisation/zone');
	 *
	 * $zones = $this->model_localisation_zone->getZonesByCountryId($country_id);
	 */
	public function getZonesByCountryId(int $country_id): array {
		$sql = "SELECT `z`.*, `zd`.`name` FROM `" . DB_PREFIX . "zone` `z` LEFT JOIN `" . DB_PREFIX . "zone_description` `zd` ON (`z`.`zone_id` = `zd`.`zone_id`) WHERE `z`.`country_id` = '" . (int)$country_id . "' AND `zd`.`language_id` = '" . (int)$this->config->get('config_language_id') . "' AND `z`.`status` = '1' ORDER BY `zd`.`name`";

		$key = md5($sql);

		$zone_data = $this->cache->get('zone.' . $key);

		if (!$zone_data) {
			$query = $this->db->query($sql);

			$zone_data = $query->rows;

			$this->cache->set('zone.' . $key, $zone_data);
		}

		return $zone_data;
	}
}
