<?php
if (! defined('ABSPATH')) {
	exit;
}

/**
 * The plugin settings page.
 *
 * @since 1.0.0
 */
class VitalPrivacyNotice_Admin {

	/**
	 * The plugin option key name base.
	 *
	 * @var    string
	 * @access private
	 * @since  1.0.0
	 */
	private $option_prefix;

	/**
	 * The plugin settings.
	 *
	 * @var    array
	 * @access private
	 * @since  1.0.0
	 */
	private $settings;

	public function __construct() {
		$this->option_prefix = 'vtlprvmsg_';

		add_action('admin_init', [$this, 'init']);
		add_action('admin_init', [$this, 'register_settings']);
		add_action('admin_menu', [$this, 'add_menu_item']);
	}

	/**
	 * Initializes settings.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function init() {
		$this->settings = $this->settings_fields();
	}

	/**
	 * Adds settings page to admin menu.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function add_menu_item() {

		add_submenu_page(
			'options-general.php',
			__('Privacy Notice', 'vital-privacy-notice'),
			__('Privacy Notice', 'vital-privacy-notice'),
			'edit_posts',
			'vital_privacy',
			[$this, 'settings_page']
		);
	}

	/**
	 * Builds the settings fields.
	 *
	 * @since 1.0.0
	 * @return array Fields to be displayed on settings page.
	 */
	private function settings_fields() {

		$settings['cookie_notice'] = [
			'title'       => __('Settings', 'vital-privacy-notice'),
			'description' => __('Controls the content and display of the privacy notice.', 'vital-privacy-notice'),
			'fields'      => [
				[
					'id'          => 'cookie_notice_display',
					'label'       => __('Display', 'vital-privacy-notice'),
					'description' => '<br>Turn the notice on/off.',
					'type'        => 'select',
					'options'     => [
						'off' => 'Off',
						'on'  => 'On',
					],
					'default'     => 'off',
				],
				[
					'id'          => 'cookie_notice_pos',
					'label'       => __('Position', 'vital-privacy-notice'),
					'description' => '<br>Select where the notice should appear.',
					'type'        => 'select',
					'options'     => [
						'bottom'       => 'Banner bottom',
						'top'          => 'Banner top',
						'bottom-left'  => 'Floating left',
						'bottom-right' => 'Floating right',
					],
					'default'     => 'bottom',
				],
				[
					'id'          => 'cookie_notice_msg',
					'label'       => __('Message', 'vital-privacy-notice'),
					'description' => '',
					'type'        => 'wysiwyg',
					'default'     => 'We use cookies to offer you a better browsing experience, personalize content, provide social media features, and to analyze our traffic. To learn more, <a href="#">click here</a>. By continuing to use our site, you accept our use of cookies, <a href="#">Privacy Policy</a>, and <a href="#">Terms of Use</a>.',
				],
				[
					'id'          => 'cookie_notice_accept_text',
					'label'       => __('Button Text', 'vital-privacy-notice'),
					'description' => '',
					'type'        => 'text',
					'placeholder' => '',
					'default'     => __('Allow Cookies', 'vital-privacy-notice'),
					'required'    => 'required',
				],
				[
					'id'          => 'cookie_notice_color',
					'label'       => __('Notice Background Color', 'vital-privacy-notice'),
					'description' => '',
					'type'        => 'color',
					'placeholder' => '',
					'default'     => '#000000',
				],
				[
					'id'          => 'cookie_notice_text_color',
					'label'       => __('Notice Text Color', 'vital-privacy-notice'),
					'description' => '',
					'type'        => 'color',
					'placeholder' => '',
					'default'     => '#ffffff',
				],
				[
					'id'          => 'cookie_notice_link_color',
					'label'       => __('Notice Text Link Color', 'vital-privacy-notice'),
					'description' => '',
					'type'        => 'color',
					'placeholder' => '',
					'default'     => '#3fa2f7',
				],
				[
					'id'          => 'cookie_notice_accept_bg_color',
					'label'       => __('Button Background Color', 'vital-privacy-notice'),
					'description' => '',
					'type'        => 'color',
					'placeholder' => '',
					'default'     => '#3fa2f7',
				],
				[
					'id'          => 'cookie_notice_accept_text_color',
					'label'       => __('Button Text Color', 'vital-privacy-notice'),
					'description' => '',
					'type'        => 'color',
					'placeholder' => '',
					'default'     => '#ffffff',
				],
			],
		];

		$settings = apply_filters('vital_privacy_settings_fields', $settings);

		return $settings;
	}

	/**
	 * Registers plugin settings.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function register_settings() {

		if (is_array($this->settings)) {

			foreach ($this->settings as $section => $data) {

				// Add section to page
				add_settings_section($section, $data['title'], [$this, 'settings_section'], 'vital_privacy_' . $section);

				foreach ($data['fields'] as $field) {

					// Validation callback for field
					$validation = '';

					if (isset($field['callback'])) {
						$validation = $field['callback'];
					}

					// Register field
					$option_name = $this->option_prefix . $field['id'];
					register_setting('vital_privacy_' . $section, $option_name, $validation);

					// Add field to page
					add_settings_field($field['id'], $field['label'], array($this, 'display_field'), 'vital_privacy_' . $section, $section, ['field' => $field]);
				}
			}
		}
	}

	/**
	 * Prints settings section description.
	 *
	 * @since 1.0.0
	 * @param array Page sections.
	 * @return void
	 */
	public function settings_section($section) {

		printf(
			"<p>%s</p>\n",
			$this->settings[$section['id']]['description']
		);
	}

	/**
	 * Generates HTML for displaying fields.
	 *
	 * @since 1.0.0
	 * @param  array $args Field data.
	 * @return void
	 */
	public function display_field($args) {

		$field = $args['field'];

		$html = '';

		$option_name = $this->option_prefix . $field['id'];
		$option = get_option($option_name);

		$data = '';

		if (isset($field['default'])) {
			$data = $field['default'];
			if ($option) {
				$data = $option;
			}
		}

		$required = '';

		if (isset($field['required'])) {
			$required = $field['required'];
		}

		switch ($field['type']) {

			case 'text':
			case 'password':
			case 'number':
				$html .= '<input id="' . esc_attr($field['id']) . '" type="' . $field['type'] . '" name="' . esc_attr($option_name) . '" placeholder="' . esc_attr($field['placeholder']) . '" value="' . $data . '" ' . $required . '>' . "\n";
				break;

			case 'url':
				$html .= '<input id="' . esc_attr($field['id']) . '" type="url" name="' . esc_attr($option_name) . '" placeholder="' . esc_attr($field['placeholder']) . '" value="' . $data . '" style="width: 350px;" ' . $required . '>' . "\n";
				break;

			case 'text_secret':
				$html .= '<input id="' . esc_attr($field['id']) . '" type="text" name="' . esc_attr($option_name) . '" placeholder="' . esc_attr($field['placeholder']) . '" value="" ' . $required . '>' . "\n";
				break;

			case 'textarea':
				$html .= '<textarea id="' . esc_attr($field['id']) . '" rows="5" cols="50" name="' . esc_attr($option_name) . '" placeholder="' . esc_attr($field['placeholder']) . '" ' . $required . '>' . $data . '</textarea><br>' . "\n";
				break;

			case 'checkbox':
				$checked = '';
				if ($option && 'on' == $option) {
					$checked = 'checked="checked"';
				}
				$html .= '<input id="' . esc_attr($field['id']) . '" type="' . $field['type'] . '" name="' . esc_attr($option_name) . '" ' . $checked . ' ' . $required . '>' . "\n";
				break;

			case 'checkbox_multi':
				foreach ($field['options'] as $k => $v) {
					$checked = false;
					if (in_array($k, $data)) {
						$checked = true;
					}
					$html .= '<label for="' . esc_attr($field['id'] . '_' . $k) . '"><input type="checkbox" ' . checked($checked, true, false) . ' name="' . esc_attr($option_name) . '[]" value="' . esc_attr($k) . '" id="' . esc_attr($field['id'] . '_' . $k) . '"> ' . $v . '</label> ';
				}
				break;

			case 'radio':
				foreach ($field['options'] as $k => $v) {
					$checked = false;
					if ($k == $data) {
						$checked = true;
					}
					$html .= '<label for="' . esc_attr($field['id'] . '_' . $k) . '"><input type="radio" ' . checked($checked, true, false) . ' name="' . esc_attr($option_name) . '" value="' . esc_attr($k) . '" id="' . esc_attr($field['id'] . '_' . $k) . '"> ' . $v . '</label> ';
				}
				break;

			case 'select':
				$html .= '<select name="' . esc_attr($option_name) . '" id="' . esc_attr($field['id']) . '">';
				foreach ($field['options'] as $k => $v) {
					$selected = false;
					if ($k == $data) {
						$selected = true;
					}
					$html .= '<option ' . selected($selected, true, false) . ' value="' . esc_attr($k) . '">' . $v . '</option>';
				}
				$html .= '</select> ';
				break;

			case 'select_multi':
				$html .= '<select name="' . esc_attr($option_name) . '[]" id="' . esc_attr($field['id']) . '" multiple="multiple">';
				foreach ($field['options'] as $k => $v) {
					$selected = false;
					if (in_array($k, $data)) {
						$selected = true;
					}
					$html .= '<option ' . selected($selected, true, false) . ' value="' . esc_attr($k) . '">' . $v . '</label> ';
				}
				$html .= '</select> ';
				break;

			case 'image':
				$image_thumb = '';
				if ($data) {
					$image_thumb = wp_get_attachment_thumb_url($data);
				}
				$html .= '<img id="' . $option_name . '_preview" class="image_preview" src="' . $image_thumb . '"><br>' . "\n";
				$html .= '<input id="' . $option_name . '_button" type="button" data-uploader_title="' . __('Upload an image' , 'vital-privacy-notice') . '" data-uploader_button_text="' . __('Use image' , 'vital-privacy-notice') . '" class="image_upload_button button" value="'. __('Upload new image' , 'vital-privacy-notice') . '" >' . "\n";
				$html .= '<input id="' . $option_name . '_delete" type="button" class="image_delete_button button" value="'. __('Remove image' , 'vital-privacy-notice') . '">' . "\n";
				$html .= '<input id="' . $option_name . '" class="image_data_field" type="hidden" name="' . $option_name . '" value="' . $data . '"><br>' . "\n";
				break;

			case 'color':
				$html .= "<input type='color' name='{$option_name}' style='width: 32px; height: 32px; padding: 0;' value='{$data}' ' . $required . '>";
				break;

			case 'wysiwyg':
				$wp_editor_args = [
					'media_buttons' => false,
					'textarea_name' => esc_attr($option_name),
					'textarea_rows' => 6,
					'teeny'         => true,
				];
				wp_editor(html_entity_decode($data), $option_name, $wp_editor_args);
				break;
		}

		switch ($field['type']) {
			case 'checkbox_multi':
			case 'radio':
			case 'select_multi':
				$html .= '<br><span class="description">' . $field['description'] . '</span>';
				break;

			default:
				$html .= '<label for="' . esc_attr($field['id']) . '"><span class="description">' . $field['description'] . '</span></label>' . "\n";
				break;
		}

		echo $html;
	}

	/**
	 * Loads settings page content.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function settings_page() {

		$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'cookie_notice';
		?>
		<div class="wrap">
			<h2><?php _e('Vital Privacy Notice', 'vital-privacy-notice'); ?></h2>
			<form method="post" action="options.php" enctype="multipart/form-data">
				<?php
				foreach ($this->settings as $section => $data) {
					if ($active_tab === $section) {
						settings_fields('vital_privacy_' . $section);
						do_settings_sections('vital_privacy_' . $section);
					}
				}
				submit_button();
				?>
			</form>
		</div>
		<?php
	}
}

if (is_admin()) {
	$plugin_settings_page = new VitalPrivacyNotice_Admin();
}
