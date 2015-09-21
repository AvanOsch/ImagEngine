<?php
class ModelToolImage extends Model {
// BOF - Zappo - ImagEngine - ONE LINE - Height can be empty
	public function resize($filename, $width, $height = 0, $type = '') {
		if (!is_file(DIR_IMAGE . $filename)) {
			return;
		}
		$this->load->model('setting/setting');
		$main = $this->model_setting_setting->getSetting('imagengine');

		$extension = pathinfo($filename, PATHINFO_EXTENSION);

// BOF - Zappo - ImagEngine - Load proper image settings
		$config = false;
		$quality = (isset($main['imagengine_image_quality'])) ? $main['imagengine_image_quality'] : 80;
		$add_on = false;
		if (!is_numeric($width)) {
			// Passed as second argument...
			$config = 'config_image_' . $width . '_';
			$add_on = $width;
			if ($width == 'popup' && isset($main['imagengine_popup_quality'])) $quality = $main['imagengine_popup_quality'];
		} else {
			// Detect from backtrace
			$config = debug_backtrace();
			$config = isset($config[0]['file']) ? $config[0]['file'] : '';
			if ($config && isset($main['imagengine_cache'])) {
				$base = basename(dirname($config));
				$caller = substr(basename($config),0,-4);
				//echo "Base = $base --- Caller = $caller<br/>";
				// Get real name from vQMod cache file
				if ($base == 'vqcache' || substr($caller,0,4) == 'vq2-') {
					$base = explode('_', substr($caller, 4));
					foreach ($base as $bnum => $fldr) {
						// "Base folder" is the one after "controller"
						if ($fldr == 'controller') {
							$base = $base[$bnum+1];
							break;
						}
					}
					$caller = explode('_' . $base . '_', $caller, 2);
					$caller = $caller[1];
				}
				$config = 'config_image_' . $base . '_' . $caller . '_';
				$add_on = $base . '_' . $caller;
				if (!$this->config->get($config . 'width')) {
					$config = 'config_image_' . $caller . '_';
					$add_on = $caller;
				}
			}
		}
		if ($config && $this->config->get($config . 'width')) {
			$width = $this->config->get($config . 'width');
			$height = $this->config->get($config . 'height');
			// Got with & height --> Replace "config_" with "imagengine_" for the rest of the values
			$config = str_replace('config_image_', 'imagengine_image_', $config);
		} else {
			$config = false;
		}
		//echo basename($filename) . " &nbsp; &nbsp; from: " . ($config ? $config : $add_on) . "<br/>";
		if (!is_array($type)) {
			$cfg = array();
			if ($type) $cfg['fixed'] = $type;
		} else {
			$cfg = $type;
		}
		$output = (isset($cfg['output']) && $cfg['output']) ? $cfg['output'] : 'url';
		$crop = (isset($cfg['crop']) && is_array($cfg['crop'])) ? $cfg['crop'] : array();
		$cache = (isset($cfg['cache']) && !$cfg['cache']) ? false : true;
		$settings = array('angle', 'anglefix', 'fixed', 'border', 'back');
		foreach ($settings as $setting) {
			${$setting} = isset($cfg[$setting]) ? $cfg[$setting] : ($config ? $this->config->get($config . $setting) : false);
		}
		$type = '';
		if ($fixed) $type .= '-f';
		if ($crop && isset($crop[3]) && $crop[0] < $crop[2] && $crop[1] < $crop[3]) $type .= '-'.$crop[0].$crop[1].$crop[2].$crop[3];
		$angle = ($angle && !$anglefix) ? mt_rand($angle*-1, $angle) : $angle;
		if ($angle) $type .= '-r' . ((isset($main['imagengine_save_angle']) && $main['imagengine_save_angle']) ? $angle : '');
		if ($border) $type .= '-b'.$border;
		if ($back) $type .= '-c'.$back;
		// BOF - Add Add-Ons
		$ttype = '';
		$addon = $addbdr = array();
		if (isset($cfg['addon']) && $cfg['addon']) {
			if (!is_array($cfg['addon'])) $cfg['addon'] = array($cfg['addon']);
			foreach ($cfg['addon'] as $add) {
				$addon_image = $this->config->get('imagengine_' . $add . '_addon');
				$addon_add = $this->config->get('imagengine_' . $add . '_show');
				if ($addon_image && is_array($addon_add) && in_array($add_on, $addon_add)) {
					$addon[$add] = array(
						'scale' => $this->config->get('imagengine_' . $add . '_scale'),
						'image' => $addon_image,
						'x' => $this->config->get('imagengine_' . $add . '_x'),
						'y' => $this->config->get('imagengine_' . $add . '_y')
					);
					$ttype .= substr($add, 0, 1);
				}
			}
		}
		$addon_image = $this->config->get('imagengine_border_addon');
		$addon_add = $this->config->get('imagengine_border_show');
		if ($addon_image && is_array($addon_add) && in_array($add_on, $addon_add)) {
			$addbdr = array(
				'scale' => $this->config->get('imagengine_border_scale'),
				'image' => $addon_image
			);
			$ttype .= 'B';
		}
		if ($ttype != '') $type .= '-a' . $ttype;
		// EOF - Add Add-Ons
		// Add random number if this image should not be cached
		if (!$cache) $type .= mt_rand();
// EOF - Zappo - ImagEngine - Load proper image settings

		$old_image = $filename;
		$new_image = 'cache/' . utf8_substr($filename, 0, utf8_strrpos($filename, '.')) . '-' . $width . 'x' . $height . $type . '.' . $extension;

// BOF - Zappo - ImagEngine - TWO LINES - Set caching
		$cache = (isset($main['imagengine_cache']) && !$main['imagengine_cache']) ? false : true;
		if (!$cache || !file_exists(DIR_IMAGE . $new_image) || (filectime(DIR_IMAGE . $old_image) > filectime(DIR_IMAGE . $new_image))) {
			$path = '';

			$directories = explode('/', dirname(str_replace('../', '', $new_image)));

			foreach ($directories as $directory) {
				$path = $path . '/' . $directory;

				if (!is_dir(DIR_IMAGE . $path)) {
					@mkdir(DIR_IMAGE . $path, 0777);
				}
			}

			list($width_orig, $height_orig) = getimagesize(DIR_IMAGE . $old_image);

// BOF - Zappo - ImagEngine - ONE LINE - Adjust Image (not just resize)
			if ($crop || $angle || $border || $back || $width_orig != $width || $height_orig != $height) {
				$image = new Image(DIR_IMAGE . $old_image);
// BOF - Zappo - ImagEngine - Added image Cropping
				if ($crop) {
					$ratio = (isset($crop[4])) ? ($width_orig > $height_orig ? $width_orig / $crop[4] : $height_orig / $crop[4]) : 1;
					$image->crop($crop[0] * $ratio, $crop[1] * $ratio, $crop[2] * $ratio, $crop[3] * $ratio);
				}
// EOF - Zappo - ImagEngine - Added image Cropping
// BOF - Zappo - ImagEngine - ONE LINE - Added image rotation (Keeping exact size)
				if ($angle && $fixed) $image->rotate($angle*-1);
// BOF - Zappo - Add Add-on Border
				if ($addbdr) {
					$w = $width;
					$h = $height;
					$width = ($width/100) * $addbdr['scale'];
					$height = ($height/100) * $addbdr['scale'];
				}
// EOF - Zappo - Add Add-on Border
// BOF - Zappo - ImagEngine - ONE LINE - Changed resize function
				$image->resize($width, $height, $fixed, $border);
// BOF - Zappo - ImagEngine - ONE LINE - Added image rotation (Disregarding exact size)
				if ($angle && !$fixed) $image->rotate($angle*-1);
// BOF - Zappo - Add Add-on Border and Image
				if ($addbdr) {
					if (($w && $width != $w) || ($h && $height != $h)) {
						$image->setsize($w, $h, $fixed, $back);
						$addme = new Image(DIR_IMAGE . $addbdr['image']);
						$size = $image->getInfo();
						$width = $size['width'];
						$height = $size['height'];
						$addme->resize($width, $height, 'stretch');
					}
					$image->merge($addme->getResource(), 0, 0);
				}
				if ($addon) {
					$padded = false;
					foreach ($addon as $add) {
						// Add extra space for add-on
						if ($this->config->get('imagengine_addon_padding') && !$padded) {
							$width += $main['imagengine_addon_padding'] * 2;
							$height += $main['imagengine_addon_padding'] * 2;
							$image->setsize($width, $height, $fixed);
							$size = $image->getInfo();
							$width = $size['width'];
							$height = $size['height'];
							$padded = true;
						}
						$addme = new Image(DIR_IMAGE . $add['image']);
						$w = ($add['scale'] >= 0) ? ($width/100) * $add['scale'] : abs($add['scale']);
						$h = ($add['scale'] >= 0) ? ($height/100) * $add['scale'] : abs($add['scale']);
						$addme->resize($w, $h);
						if ($add['x'] < 0 || $add['x'] == -0) $add['x'] += $width - $w;
						if ($add['y'] < 0 || $add['y'] == -0) $add['y'] += $height - $h;
						$image->merge($addme->getResource(), $add['x'], $add['y']);
					}
				}
				if ($addon || $addbdr) $image->flatten($main['imagengine_border_fill']);
				else $image->flatten($back);
// EOF - Zappo - Add Add-on Border and Image
				$image->save(DIR_IMAGE . $new_image, $quality);
			} else {
				copy(DIR_IMAGE . $old_image, DIR_IMAGE . $new_image);
			}
		}

// BOF - Zappo - ImagEngine - Added Output Options (for Ajax output)
		if ($output == 'json') {
			if (!isset($image)) $image = new Image(DIR_IMAGE . $new_image);
			$imageArray['width'] = $image->getInfo('width');
			$imageArray['height'] = $image->getInfo('height');
			$imageArray['image'] = $new_image;
			return $imageArray;
		} elseif ($output == 'data') {
			if (!isset($image)) $image = new Image(DIR_IMAGE . $new_image);
			return $image->getData();
		} else {
			if ($this->request->server['HTTPS']) {
				return $this->config->get('config_ssl') . 'image/' . $new_image;
			} else {
				return $this->config->get('config_url') . 'image/' . $new_image;
			}
		}
// EOF - Zappo - ImagEngine - Added Output Options (for Ajax output)
	}

// BOF - Zappo - ImagEngine - Added Text function
	function text($text, $height, $fontfile, $rotate, $color='FFFFFF', $bgcolor='000000', $saveDir='cache/text/', $output='data', $quality=30) {
		if (strpos($color,'#')) $color = substr($color,1);
		if (strpos($bgcolor,'#')) $bgcolor = substr($bgcolor,1);
		$imageName = substr(strrchr($fontfile,'/'),1,-4) . '-' . $color . '-H' . $height . '-A' . $rotate . '-'. md5($text) .'.png';
		$new_image = ((!$saveDir) ? 'cache/text/' : $saveDir) . $imageName;
		if (!file_exists(DIR_IMAGE . $new_image)) {
			$path = '';
			$directories = explode('/', dirname(str_replace('../', '', $new_image)));
			foreach ($directories as $directory) {
				$path = $path . '/' . $directory;
				if (!file_exists(DIR_IMAGE . $path)) @mkdir(DIR_IMAGE . $path, 0777);
			}
			$image = new Image('text');
			$image->text($text, $height, DIR_IMAGE . $fontfile, $rotate, $color, $bgcolor, $quality);
		}
		if ($output == 'json') {
			if (isset($image)) $image->save(DIR_IMAGE . $new_image);
			else $image = new Image(DIR_IMAGE . $new_image);
			$imageArray['width'] = $image->getInfo('width');
			$imageArray['height'] = $image->getInfo('height');
			$imageArray['image'] = $new_image;
			return $imageArray;
		} elseif ($output == 'data') {
			if (!isset($image)) $image = new Image(DIR_IMAGE . $new_image);
			return $image->getData(DIR_IMAGE . $new_image);
		} else {
			if (isset($image)) $image->save(DIR_IMAGE . $new_image);
			if ($output == 'file') {
				return $new_image;
			} else {
				if ($this->request->server['HTTPS']) {
					return $this->config->get('config_ssl') . 'image/' . $new_image;
				} else {
					return $this->config->get('config_url') . 'image/' . $new_image;
				}
			}
		}
	}
// EOF - Zappo - ImagEngine -Added Text function

// BOF - Zappo - ImagEngine - Added Checking need for Merging function
	function needsMerge($images, $width='', $height='', $bgcolor='', $border='', $effect='', $saveDir='') {
		$color = (strlen($bgcolor) == 7) ? substr($bgcolor,1) : $bgcolor;
		$id = (($width && $height) ? '-'.$width .'x'.$height : '') . (($color) ? '-'.$color : '') . (($border) ? '-'.$border : '') . (($effect) ? '-'.strtolower(substr($effect,11,4)) : '');
		foreach ($images as $image) if (isset($image['id']) && $image['id']) $id .= '-'.$image['id'];
		$new_image = (($saveDir) ? $saveDir : 'cache/').'merge'.$id.'.png';
		if (!file_exists(DIR_IMAGE . $new_image)) return true;
		else return false;
	}
// BOF - Zappo - ImagEngine - Added Merging function
	function merge($images, $width='', $height='', $bgcolor='', $border='', $effect='', $saveDir='', $output='url') {
		$color = (strlen($bgcolor) == 7) ? substr($bgcolor,1) : $bgcolor;
		$id = (($width && $height) ? '-'.$width .'x'.$height : '') . (($color) ? '-'.$color : '') . (($border) ? '-'.$border : '') . (($effect) ? '-'.strtolower(substr($effect,11,4)) : '');
		foreach ($images as $image) if (isset($image['id']) && $image['id']) $id .= '-'.$image['id'];
		$new_image = (($saveDir) ? $saveDir : 'cache/').'merge'.$id.'.png';
		if (!file_exists(DIR_IMAGE . $new_image)) {
			$path = '';
			$directories = explode('/', dirname(str_replace('../', '', $new_image)));
			foreach ($directories as $directory) {
				$path = $path . '/' . $directory;
				if (!file_exists(DIR_IMAGE . $path)) {
					@mkdir(DIR_IMAGE . $path, 0777);
				}
			}
			if ($width && $height) {
				$image = new Image(array($width,$height,$color));
			} else {
				$image = new Image(DIR_IMAGE . $images[0]['image']);
				$width = $images[0]['width'];
				$height = $images[0]['height'];
				$image->resize($width, $height);
				if (isset($images[0]['rotation']) && $images[0]['rotation']) {
					$width = $images[0]['height'];
					$height = $images[0]['width'];
					$image->rotate($images[0]['rotation']);
				}
				unset($images[0]);
			}
			foreach ($images as $img) {
				$image_temp = new Image(DIR_IMAGE . $img['image']);
				$image_temp->resize($img['width'], $img['height'], $effect);
				if (isset($img['rotation']) && $img['rotation']) $image_temp->rotate($img['rotation']);
				$imgLeft = (isset($img['left'])) ? (int)$img['left'] : 0;
				$imgTop = (isset($img['top'])) ? (int)$img['top'] : 0;
				$opacity = (isset($img['opacity'])) ? (int)$img['opacity'] : 100;
				$image->merge($image_temp->getResource(),$imgLeft,$imgTop,$opacity);
			}
		}
		if ($output == 'data') {
			if (!file_exists(DIR_IMAGE . $new_image)) $image->save(DIR_IMAGE . $new_image);
			return $image->getData(DIR_IMAGE . $new_image);
		} elseif ($output == 'file') {
			if (!file_exists(DIR_IMAGE . $new_image)) $image->save(DIR_IMAGE . $new_image);
			return $new_image;
		} else {
			if (!file_exists(DIR_IMAGE . $new_image)) $image->save(DIR_IMAGE . $new_image);
			if ($this->request->server['HTTPS']) {
				return $this->config->get('config_ssl') . 'image/' . $new_image;
			} else {
				return $this->config->get('config_url') . 'image/' . $new_image;
			}
		}
	}
// EOF - Zappo - ImagEngine - Added Merging function

// BOF - Zappo - ImagEngine - Designer - Added Design-Merging function
	function design($images, $border='', $effect='', $saveDir='', $copyLast=false, $output='data') {
		$id = (isset($images[0]['design']) && $images[0]['design']) ? $images[0]['design'] : mt_rand();
		$color = (strlen($images[0]['color']) == 7) ? substr($images[0]['color'],1) : $images[0]['color'];
		$new_image = (($saveDir) ? $saveDir : 'cache/').'Design-'.$id.'.png';
		$path = '';
		$directories = explode('/', dirname(str_replace('../', '', $new_image)));
		foreach ($directories as $directory) {
			$path = $path . '/' . $directory;
			if (!file_exists(DIR_IMAGE . $path)) {
				@mkdir(DIR_IMAGE . $path, 0777);
			}
		}
		$image = new Image(DIR_IMAGE . $images[0]['src']);
		$width = $images[0]['width'];
		$height = $images[0]['height'];
		$image->resize($width, $height,'',$color);
		if ($images[0]['rotation']) {
			$image->rotate($images[0]['rotation']*-1);
			$rotate = $images[0]['rotation'];
		}
		// Look for image Mask, to trim stuff outside the target area.
		$imagesrc = substr($images[0]['src'],0,-4).'-'.$images[0]['rotation'].substr($images[0]['src'],-4);
		if (file_exists(DIR_IMAGE . $imagesrc)) {
			$images[0]['width'] = $image->getInfo('width');
			$images[0]['height'] = $image->getInfo('height');
			$images[0]['mask'] = 1;
			$images[0]['src'] = $imagesrc;
			$images[0]['rotation'] = 0;
		}
		if ($copyLast || isset($images[0]['mask'])) { // Cover the top of the image with the base image
			$images[0]['left'] = $images[0]['top'] = 0;
			$images[] = $images[0];
		}
		unset($images[0]);
		foreach ($images as $i => $img) {
			$image_temp = new Image(DIR_IMAGE . $img['src']);
			$size = (isset($img['size']) && $img['size']) ? $img['size']/100 : 1;
			if ($img['type'] != 'text') {
				if (isset($images[$i]['crop']) && strpos($images[$i]['crop'], '/')) {
					$crop = explode('/', $images[$i]['crop']);
					$ratio = (isset($crop[4])) ? ($image_temp->getInfo('width') > $image_temp->getInfo('height') ? $image_temp->getInfo('width') / $crop[4] : $image_temp->getInfo('height') / $crop[4]) : 1;
					$image_temp->crop($crop[0] * $ratio, $crop[1] * $ratio, $crop[2] * $ratio, $crop[3] * $ratio);
				}
				$image_temp->resize($img['width'] * $size, $img['height'] * $size,(isset($img['mask']) ? '' : $effect));
				if ($img['rotation']) $image_temp->rotate($img['rotation']*-1);
			} else {
				$image_temp->text($img['src'], $img['font'], DESIGN_TEXT_DEFAULT_SIZE * $size, DESIGN_FONT_COLOR, '', $img['rotation']*-1);
			}
			$imgLeft = (isset($img['left'])) ? (int)$img['left'] : 0;
			$imgTop = (isset($img['top'])) ? (int)$img['top'] : 0;
			$opacity = ($img['type'] != 'mainProduct') ? 80 : 100;
			$image->merge($image_temp->getResource(),$imgLeft,$imgTop,$opacity);
		}
		/*if ($rotate) {
			$image->rotate($rotate);
			$cx = $image->getInfo('width')/2;
			$cy = $image->getInfo('height')/2;
			$image->crop($cx - ($width/2),$cy - ($height/2), $cx + ($width/2),$cy + ($height/2));
			$image->rotate($rotate*-1);*/
			$width = $image->getInfo('width');
			$height = $image->getInfo('height');
		//}
		$image->resize($width,$height,$border,$color);
		if ($output == 'data') {
			return $image->getData(DIR_IMAGE . $new_image);
		} else {
			$image->save(DIR_IMAGE . $new_image);
			if ($this->request->server['HTTPS']) {
				return $this->config->get('config_ssl') . 'image/' . $new_image;
			} else {
				return $this->config->get('config_url') . 'image/' . $new_image;
			}
		}
	}
// EOF - Zappo - ImagEngine - Designer - Added Design-Merging function

	public function getImageAddon($id, $special) {
		$addon = array('addon' => array());
		if (!$id) return $addon;
		if ($this->customer->isLogged()) {
			$customer_group_id = $this->customer->getCustomerGroupId();
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}
		$limit = $this->config->get('config_bestseller_limit');
		if (!$limit) $limit = 20;
		$query = $this->db->query("SELECT op.product_id, COUNT(*) AS total FROM " . DB_PREFIX . "order_product op LEFT JOIN `" . DB_PREFIX . "order` o ON (op.order_id = o.order_id) LEFT JOIN `" . DB_PREFIX . "product` p ON (op.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE o.order_status_id > '0' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' GROUP BY op.product_id ORDER BY total DESC LIMIT " . (int)$limit);
		foreach ($query->rows as $result) {
			if ($id == $result['product_id']) {
				$addon['addon'][] = 'bestseller';
				break;
			}
		}

		$limit = $this->config->get('config_latest_limit');
		if (!$limit) $limit = 20;
		$query = $this->db->query("SELECT p.product_id FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' ORDER BY p.date_added DESC LIMIT " . (int)$limit);
		foreach ($query->rows as $result) {
			if ($id == $result['product_id']) {
				$addon['addon'][] = 'latest';
				break;
			}
		}

		if ($special) $addon['addon'][] = 'specials';

		return $addon;
	}
}