<?php
class ModelSettingSetting extends Model {
	public function getSetting($code, $store_id = 0) {
		$setting_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int)$store_id . "' AND `code` = '" . $this->db->escape($code) . "'");

		foreach ($query->rows as $result) {
			if (!$result['serialized']) {
				$setting_data[$result['key']] = $result['value'];
			} else {
				$setting_data[$result['key']] = unserialize($result['value']);
			}
		}

		return $setting_data;
	}

	public function editSetting($code, $data, $store_id = 0) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE store_id = '" . (int)$store_id . "' AND `code` = '" . $this->db->escape($code) . "'");

		foreach ($data as $key => $value) {
			if (substr($key, 0, strlen($code)) == $code) {
				if (!is_array($value)) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '" . (int)$store_id . "', `code` = '" . $this->db->escape($code) . "', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape($value) . "'");
				} else {
					$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '" . (int)$store_id . "', `code` = '" . $this->db->escape($code) . "', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape(serialize($value)) . "', serialized = '1'");
				}
			}
		}
	}

	public function deleteSetting($code, $store_id = 0) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int)$store_id . "' AND `code` = '" . $this->db->escape($code) . "'");
	}

	public function editSettingValue($code = '', $key = '', $value = '', $store_id = 0) {
		if (!is_array($value)) {
			$this->db->query("UPDATE " . DB_PREFIX . "setting SET `value` = '" . $this->db->escape($value) . "', serialized = '0'  WHERE `code` = '" . $this->db->escape($code) . "' AND `key` = '" . $this->db->escape($key) . "' AND store_id = '" . (int)$store_id . "'");
		} else {
			$this->db->query("UPDATE " . DB_PREFIX . "setting SET `value` = '" . $this->db->escape(serialize($value)) . "', serialized = '1' WHERE `code` = '" . $this->db->escape($code) . "' AND `key` = '" . $this->db->escape($key) . "' AND store_id = '" . (int)$store_id . "'");
		}
	}

// BOF - Zappo - ImagEngine - Update Settings (insert or update)
	public function updateSetting($code, $data, $store_id = 0) {
		if (!is_array($data)) return false;
		// Check for INSERT or UPDATE needs!
		foreach ($data as $key => $value) {
			if (substr($key, 0, strlen($code)) == $code) {
				if (is_array($value)) {
					$value = serialize($value);
					$id = ", serialized = '1'";
				} else {
					$id = ", serialized = '0'";
				}
				$query = "INSERT INTO";
				$exist = $this->db->query("SELECT setting_id AS id FROM " . DB_PREFIX . "setting WHERE `key` = '" . $this->db->escape($key) . "' AND `code` = '" . $code . "'");
				if (isset($exist->row['id']) && $exist->row['id']) {
					$query = "UPDATE";
					$id .= " WHERE `key` = '" . $key . "' AND `code` = '" . $code . "'";
				} else {
					$id .= ", `code` = '" . $this->db->escape($code) . "'";
				}
				$query .= DB_PREFIX . " setting SET store_id = '" . (int)$store_id . "', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape($value) . "'" . $id;
				$this->db->query($query);
			}
		}
	}
// EOF - Zappo - ImagEngine - Update Settings (insert or update)
}
