<?php
/*
 * To add another image type:
 * - Search for the existing image "additional" (that string is unique in this file)
 * - Add: language entry, Image type and error check (by copying the "additional" example)
 * - Add: language file translation(s) in "admin/language/LANGUAGES/design/imagengine.php"
 * At this point, you have the new image type settings in your admin area.
 * After saving the new settings, you can use them anywhere in the catalog by using (where your new type is NEWTYPE):
 * $this->model_tool_image->resize($your['image'], $this->config->get('config_image_NEWTYPE_width'), $this->config->get('config_image_NEWTYPE_height'))
 */
class ControllerDesignImagengine extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('design/imagengine');

		$this->document->setTitle($this->language->get('heading_title'));
		$this->document->addScript('view/javascript/jscolor.js');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->load->model('setting/setting');
			$this->model_setting_setting->updateSetting('imagengine', $this->request->post['imgne']);
			$this->model_setting_setting->updateSetting('config', $this->request->post['config']);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('design/imagengine', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_image_addons'] = $this->language->get('text_image_addons');
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_none'] = $this->language->get('text_none');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_once'] = $this->language->get('text_once');
		$data['text_each_angle'] = $this->language->get('text_each_angle');
		$data['text_stretch'] = $this->language->get('text_stretch');

		$data['entry_image_quality'] = $this->language->get('entry_image_quality');
		$data['entry_popup_quality'] = $this->language->get('entry_popup_quality');
		$data['entry_save_angle'] = $this->language->get('entry_save_angle');
		$data['entry_cache'] = $this->language->get('entry_cache');
		$data['entry_clear_cache'] = $this->language->get('entry_clear_cache');
		$data['entry_image_category'] = $this->language->get('entry_image_category');
		$data['entry_image_thumb'] = $this->language->get('entry_image_thumb');
		$data['entry_image_popup'] = $this->language->get('entry_image_popup');
		$data['entry_image_product'] = $this->language->get('entry_image_product');
		$data['entry_image_additional'] = $this->language->get('entry_image_additional');
		$data['entry_image_option'] = $this->language->get('entry_image_option');
		$data['entry_image_related'] = $this->language->get('entry_image_related');
		$data['entry_image_compare'] = $this->language->get('entry_image_compare');
		$data['entry_image_wishlist'] = $this->language->get('entry_image_wishlist');
		$data['entry_image_cart'] = $this->language->get('entry_image_cart');
		$data['entry_image_common_cart'] = $this->language->get('entry_image_common_cart');
		$data['entry_image_location'] = $this->language->get('entry_image_location');
		$data['entry_border_addon'] = $this->language->get('entry_border_addon');
		$data['entry_border_show'] = $this->language->get('entry_border_show');
		$data['entry_border_fill'] = $this->language->get('entry_border_fill');
		$data['entry_border_scale'] = $this->language->get('entry_border_scale');
		$data['entry_addon_padding'] = $this->language->get('entry_addon_padding');
		$data['entry_bestseller_limit'] = $this->language->get('entry_bestseller_limit');
		$data['entry_latest_limit'] = $this->language->get('entry_latest_limit');
		$data['entry_specials_addon'] = $this->language->get('entry_specials_addon');
		$data['entry_bestseller_addon'] = $this->language->get('entry_bestseller_addon');
		$data['entry_latest_addon'] = $this->language->get('entry_latest_addon');
		$data['entry_zoom_addon'] = $this->language->get('entry_zoom_addon');
		$data['entry_show'] = $this->language->get('entry_show');
		$data['entry_pos_x'] = $this->language->get('entry_pos_x');
		$data['entry_pos_y'] = $this->language->get('entry_pos_y');
		$data['entry_scale'] = $this->language->get('entry_scale');
		$data['entry_font_dir'] = $this->language->get('entry_font_dir');
		$data['entry_font'] = $this->language->get('entry_font');
		$data['entry_font_height'] = $this->language->get('entry_font_height');
		$data['entry_font_color'] = $this->language->get('entry_font_color');
		$data['entry_font_backg'] = $this->language->get('entry_font_backg');
		$data['entry_font_size'] = $this->language->get('entry_font_size');

		$data['help_quality'] = $this->language->get('help_quality');
		$data['help_save_angle'] = $this->language->get('help_save_angle');
		$data['help_cache'] = $this->language->get('help_cache');
		$data['help_clear_cache'] = $this->language->get('help_clear_cache');
		$data['help_width'] = $this->language->get('help_width');
		$data['help_height'] = $this->language->get('help_height');
		$data['help_fixed'] = $this->language->get('help_fixed');
		$data['help_border'] = $this->language->get('help_border');
		$data['help_border_color'] = $this->language->get('help_border_color');
		$data['help_angle'] = $this->language->get('help_angle');
		$data['help_anglefix'] = $this->language->get('help_anglefix');
		$data['help_border_addon'] = $this->language->get('help_border_addon');
		$data['help_border_show'] = $this->language->get('help_border_show');
		$data['help_border_fill'] = $this->language->get('help_border_fill');
		$data['help_border_scale'] = $this->language->get('help_border_scale');
		$data['help_addon_padding'] = $this->language->get('help_addon_padding');
		$data['help_bestseller_limit'] = $this->language->get('help_bestseller_limit');
		$data['help_latest_limit'] = $this->language->get('help_latest_limit');
		$data['help_specials_addon'] = $this->language->get('help_specials_addon');
		$data['help_bestseller_addon'] = $this->language->get('help_bestseller_addon');
		$data['help_latest_addon'] = $this->language->get('help_latest_addon');
		$data['help_zoom_addon'] = $this->language->get('help_zoom_addon');
		$data['help_show'] = $this->language->get('help_show');
		$data['help_pos_x'] = $this->language->get('help_pos_x');
		$data['help_pos_y'] = $this->language->get('help_pos_y');
		$data['help_scale'] = $this->language->get('help_scale');
		$data['help_font_dir'] = $this->language->get('help_font_dir');
		$data['help_font'] = $this->language->get('help_font');
		$data['help_font_height'] = $this->language->get('help_font_height');
		$data['help_font_color'] = $this->language->get('help_font_color');
		$data['help_font_backg'] = $this->language->get('help_font_backg');
		$data['help_font_size'] = $this->language->get('help_font_size');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		$data['tab_general'] = $this->language->get('tab_general');
		$data['tab_sizes'] = $this->language->get('tab_sizes');
		$data['tab_addon'] = $this->language->get('tab_addon');
		$data['tab_font'] = $this->language->get('tab_font');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('design/imagengine', 'token=' . $this->session->data['token'], 'SSL')
		);

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$data['action'] = $this->url->link('design/imagengine', 'token=' . $this->session->data['token'], 'SSL');
		$data['cancel'] = $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL');
		$data['autocomplete'] = str_replace('&amp;', '&', $this->url->link('design/imagengine/autocomplete', 'token=' . $this->session->data['token'], 'SSL'));
		$data['clearcache'] = str_replace('&amp;', '&', $this->url->link('design/imagengine/clearcache', 'token=' . $this->session->data['token'], 'SSL'));

		$data['token'] = $this->session->data['token'];

		// Image types (for size settings)
		 $data['image_type'] = array(
			'category',
			'thumb',
			'popup',
			'product',
			'additional',
			'option',
			'related',
			'compare',
			'wishlist',
			'cart',
			'common_cart',
			'location'
		);

		// All settings strings
		$settings = array(
			'imagengine_image_quality',
			'imagengine_popup_quality',
			'imagengine_save_angle',
			'imagengine_cache',
			'imagengine_border_addon',
			'imagengine_border_show',
			'imagengine_border_fill',
			'imagengine_border_scale',
			'imagengine_addon_padding',
			'config_bestseller_limit',
			'config_latest_limit',
			'imagengine_specials_addon',
			'imagengine_specials_show',
			'imagengine_specials_x',
			'imagengine_specials_y',
			'imagengine_specials_scale',
			'imagengine_latest_addon',
			'imagengine_latest_show',
			'imagengine_latest_x',
			'imagengine_latest_y',
			'imagengine_latest_scale',
			'imagengine_bestseller_addon',
			'imagengine_bestseller_show',
			'imagengine_bestseller_x',
			'imagengine_bestseller_y',
			'imagengine_bestseller_scale',
			'imagengine_zoom_addon',
			'imagengine_zoom_show',
			'imagengine_zoom_x',
			'imagengine_zoom_y',
			'imagengine_zoom_scale',
			'imagengine_font_dir',
			'imagengine_font',
			'imagengine_font_height',
			'imagengine_font_color',
			'imagengine_font_backg',
			'imagengine_font_size'
		);
		// Add Settings strings for Image Types
		foreach ($data['image_type'] as $img) {
			$settings[] = 'config_image_'.$img.'_width';
			$settings[] = 'config_image_'.$img.'_height';
			$settings[] = 'imagengine_image_'.$img.'_fixed';
			$settings[] = 'imagengine_image_'.$img.'_border';
			$settings[] = 'imagengine_image_'.$img.'_back';
			$settings[] = 'imagengine_image_'.$img.'_angle';
			$settings[] = 'imagengine_image_'.$img.'_anglefix';
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$this->load->model('tool/image');
		if (isset($this->request->post['imagengine_border_addon']) && is_file(DIR_IMAGE . $this->request->post['imagengine_border_addon'])) {
			$data['border_thumb'] = $this->model_tool_image->resize($this->request->post['imagengine_border_addon'], 100, 100);
		} elseif ($this->config->get('imagengine_border_addon') && is_file(DIR_IMAGE . $this->config->get('imagengine_border_addon'))) {
			$data['border_thumb'] = $this->model_tool_image->resize($this->config->get('imagengine_border_addon'), 100, 100);
		} else {
			$data['border_thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}
		if (isset($this->request->post['imagengine_specials_addon']) && is_file(DIR_IMAGE . $this->request->post['imagengine_specials_addon'])) {
			$data['specials_thumb'] = $this->model_tool_image->resize($this->request->post['imagengine_specials_addon'], 100, 100);
		} elseif ($this->config->get('imagengine_specials_addon') && is_file(DIR_IMAGE . $this->config->get('imagengine_specials_addon'))) {
			$data['specials_thumb'] = $this->model_tool_image->resize($this->config->get('imagengine_specials_addon'), 100, 100);
		} else {
			$data['specials_thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}
		if (isset($this->request->post['imagengine_bestseller_addon']) && is_file(DIR_IMAGE . $this->request->post['imagengine_bestseller_addon'])) {
			$data['bestseller_thumb'] = $this->model_tool_image->resize($this->request->post['imagengine_bestseller_addon'], 100, 100);
		} elseif ($this->config->get('imagengine_bestseller_addon') && is_file(DIR_IMAGE . $this->config->get('imagengine_bestseller_addon'))) {
			$data['bestseller_thumb'] = $this->model_tool_image->resize($this->config->get('imagengine_bestseller_addon'), 100, 100);
		} else {
			$data['bestseller_thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}
		if (isset($this->request->post['imagengine_latest_addon']) && is_file(DIR_IMAGE . $this->request->post['imagengine_latest_addon'])) {
			$data['latest_thumb'] = $this->model_tool_image->resize($this->request->post['imagengine_latest_addon'], 100, 100);
		} elseif ($this->config->get('imagengine_latest_addon') && is_file(DIR_IMAGE . $this->config->get('imagengine_latest_addon'))) {
			$data['latest_thumb'] = $this->model_tool_image->resize($this->config->get('imagengine_latest_addon'), 100, 100);
		} else {
			$data['latest_thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}
		if (isset($this->request->post['imagengine_zoom_addon']) && is_file(DIR_IMAGE . $this->request->post['imagengine_zoom_addon'])) {
			$data['zoom_thumb'] = $this->model_tool_image->resize($this->request->post['imagengine_zoom_addon'], 100, 100);
		} elseif ($this->config->get('imagengine_zoom_addon') && is_file(DIR_IMAGE . $this->config->get('imagengine_zoom_addon'))) {
			$data['zoom_thumb'] = $this->model_tool_image->resize($this->config->get('imagengine_zoom_addon'), 100, 100);
		} else {
			$data['zoom_thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}
		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		// Get errors and values for all settings
		foreach ($settings as $setting) {
			$errorname = explode('_', $setting);
			array_pop($errorname);
			array_shift($errorname);
			$errorname = implode('_', $errorname);
			if (isset($this->error[$errorname])) {
				$data['error_' . $errorname] = $this->error[$errorname];
			} else {
				$data['error_' . $errorname] = '';
			}

			if (isset($this->request->post[$setting])) {
				$data[$setting] = $this->request->post[$setting];
			} else {
				$data[$setting] = $this->config->get($setting);
			}
		}
		if (!$data['imagengine_border_show']) $data['imagengine_border_show'] = array();
		if (!$data['imagengine_specials_show']) $data['imagengine_specials_show'] = array();
		if (!$data['imagengine_bestseller_show']) $data['imagengine_bestseller_show'] = array();
		if (!$data['imagengine_latest_show']) $data['imagengine_latest_show'] = array();
		if (!$data['imagengine_zoom_show']) $data['imagengine_zoom_show'] = array();

		// Get values and texts for Addon locations
		$data['border_shows'] = $data['specials_shows'] = $data['bestseller_shows'] = $data['latest_shows'] = $data['zoom_shows'] = array(
			'wishlist' => $this->language->get('text_wishlist'),
			'cart' => $this->language->get('text_cart'),
			'common_cart' => $this->language->get('text_common_cart'),
			'bestseller' => $this->language->get('text_bestseller'),
			'carousel' => $this->language->get('text_carousel'),
			'search' => $this->language->get('text_search'),
			'featured' => $this->language->get('text_featured'),
			'latest' => $this->language->get('text_latest'),
			'thumb' => $this->language->get('text_product'),
			'additional' => $this->language->get('text_additional'),
			'option' => $this->language->get('text_option'),
			'manufacturer' => $this->language->get('text_manufacturer'),
			'compare' => $this->language->get('text_compare'),
			'special' => $this->language->get('text_special'),
			'product' => $this->language->get('text_product_list'),
			'category' => $this->language->get('text_category'),
			'related' => $this->language->get('text_related')
		);
		$data['zoom_shows'] = array(
			'thumb' => $this->language->get('text_product'),
			'additional' => $this->language->get('text_additional')
		);
		unset($data['specials_shows']['cart'], $data['specials_shows']['common_cart'], $data['specials_shows']['carousel'], $data['specials_shows']['additional'], $data['specials_shows']['option'], $data['specials_shows']['special'], $data['specials_shows']['category']);
		unset($data['bestseller_shows']['cart'], $data['bestseller_shows']['common_cart'], $data['bestseller_shows']['bestseller'], $data['bestseller_shows']['carousel'], $data['bestseller_shows']['additional'], $data['bestseller_shows']['option'], $data['bestseller_shows']['category']);
		unset($data['latest_shows']['cart'], $data['latest_shows']['common_cart'], $data['latest_shows']['carousel'], $data['latest_shows']['latest'], $data['latest_shows']['additional'], $data['latest_shows']['option'], $data['latest_shows']['category']);

		// Get Fonts for dropdown
		$data['fonts'] = array();
		$dir = $data['imagengine_font_dir'];
		if (file_exists($dir) && $root = opendir($dir)) {
			while (false !== ($file = readdir($root))) {
				if (in_array(substr($file, -3), array('ttf'))) { // file extension filter
					$data['fonts'][] = substr($file, 0, -4);
				}
			}
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('design/imagengine.tpl', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'design/imagengine')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['config']['config_image_category_width'] || !$this->request->post['config']['config_image_category_height']) {
			$this->error['image_category'] = $this->language->get('error_image_category');
		}

		if (!$this->request->post['config']['config_image_thumb_width'] || !$this->request->post['config']['config_image_thumb_height']) {
			$this->error['image_thumb'] = $this->language->get('error_image_thumb');
		}

		if (!$this->request->post['config']['config_image_popup_width'] || !$this->request->post['config']['config_image_popup_height']) {
			$this->error['image_popup'] = $this->language->get('error_image_popup');
		}

		if (!$this->request->post['config']['config_image_product_width'] || !$this->request->post['config']['config_image_product_height']) {
			$this->error['image_product'] = $this->language->get('error_image_product');
		}

		if (!$this->request->post['config']['config_image_additional_width'] || !$this->request->post['config']['config_image_additional_height']) {
			$this->error['image_additional'] = $this->language->get('error_image_additional');
		}

		if (!$this->request->post['config']['config_image_option_width'] || !$this->request->post['config']['config_image_option_height']) {
			$this->error['image_option'] = $this->language->get('error_image_option');
		}

		if (!$this->request->post['config']['config_image_related_width'] || !$this->request->post['config']['config_image_related_height']) {
			$this->error['image_related'] = $this->language->get('error_image_related');
		}

		if (!$this->request->post['config']['config_image_compare_width'] || !$this->request->post['config']['config_image_compare_height']) {
			$this->error['image_compare'] = $this->language->get('error_image_compare');
		}

		if (!$this->request->post['config']['config_image_wishlist_width'] || !$this->request->post['config']['config_image_wishlist_height']) {
			$this->error['image_wishlist'] = $this->language->get('error_image_wishlist');
		}

		if (!$this->request->post['config']['config_image_cart_width'] || !$this->request->post['config']['config_image_cart_height']) {
			$this->error['image_cart'] = $this->language->get('error_image_cart');
		}

		if (!$this->request->post['config']['config_image_common_cart_width'] || !$this->request->post['config']['config_image_common_cart_height']) {
			$this->error['image_common_cart'] = $this->language->get('error_image_cart');
		}

		if (!$this->request->post['config']['config_image_location_width'] || !$this->request->post['config']['config_image_location_height']) {
			$this->error['image_location'] = $this->language->get('error_image_location');
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	// Get File or Folder tree for Autocomplete ($exts = extensions array)
	public function autocomplete($path='../', $exts=array()) {
		$ignore = array('install', 'vqmod', 'nbproject', '.svn', '.', '..');
		$path = (isset($this->request->get['dir'])) ? $this->request->get['dir'] : $path;
		if (substr($path, 0, 1) != '/') $path = '../' . $path;

		$tree = array();
		$path = explode('/', $path);
		$find = array_pop($path);
		$len = strlen($find);
		$path = implode('/', $path) . '/';
		if (file_exists($path)) {
			$dh = opendir($path);
			while (false !== ($file = readdir($dh))) {
				if (!in_array($file, $ignore) && (!$find || substr($file, 0, $len) == $find)) {
					$dir = $path . $file;
					if (is_dir($dir)) {
						$dir .= '/';
						$tree[] = str_replace('../', '', $dir);
					} else {
						$ext = explode('.', $file);
						$ext = array_pop($ext);
						if (in_array($ext, $exts)) $tree[] = str_replace('../', '', $dir);
					}
				}
			}
			closedir($dh);
		}
		$this->response->setOutput(json_encode($tree));
	}

	public function clearcache($folder=false) {
		$this->load->language('design/imagengine');
		$folder = ($folder ? $folder : DIR_IMAGE . 'cache') . '/*';
		$files = glob($folder);
		if (!$files) $files = array();
		$json = array('success' => $this->language->get('success_cache_clear'));
		foreach($files as $file) {
			if (is_file($file)) {
				if (unlink($file)) $json['success'] = $this->language->get('success_deleting_images');
				else $json['error'] = $this->language->get('error_deleting_images');
			} else {
				$this->clearcache($file);
			}
		}
		$this->response->setOutput(json_encode($json));
	}
}