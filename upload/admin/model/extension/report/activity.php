<?php
class ModelExtensionReportActivity extends Model {
	public function getActivities() {
		$query = $this->db->query("SELECT a.key, a.data, a.date_added FROM (SELECT CONCAT('customer_', ca.key) AS `key`, ca.data, ca.date_added FROM `" . DB_PREFIX . "customer_activity` ca) a ORDER BY a.date_added DESC LIMIT 0,5");

		return $query->rows;
	}
}