<?php
/*
Plugin Name: Vital Privacy Notice
Plugin URI: https://vtldesign.com
Description: Displays a privacy/cookie notice message to site users.
Version: 1.1.0
Author: Vital
Author URI: http://vtldesign.com
Requires at least: 5.2
Requires PHP: 7.0
Text Domain: vital-privacy-notice
License: GPLv2

Copyright 2020  VITAL  (email : hello@vtldesign.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if (! defined('ABSPATH')) {
	exit;
}

class VitalPrivacyNotice {

	/**
	 * The plugin path.
	 *
	 * @var    string
	 * @access private
	 * @since  1.0.0
	 */
	private $plugin_path;

	/**
	 * The plugin URL.
	 *
	 * @var    string
	 * @access private
	 * @since  1.0.0
	 */
	private $plugin_url;

	/**
	 * The plugin version.
	 *
	 * @var    string
	 * @access private
	 * @since  1.0.0
	 */
	private $plugin_version;

	/**
	 * The plugin option key name base.
	 *
	 * @var    string
	 * @access private
	 * @since  1.0.0
	 */
	private $option_prefix;

	public function __construct() {

		$this->plugin_path    = plugin_dir_path(__FILE__);
		$this->plugin_url     = plugin_dir_url(__FILE__);
		$this->plugin_version = '1.1.0';
		$this->option_prefix  = 'vtlprvmsg_';

		require $this->plugin_path . 'admin.php';

		register_activation_hook(__FILE__, [$this, 'install']);

		add_filter('plugin_action_links_' . plugin_basename(__FILE__), [$this, 'add_action_link']);

		if (get_option($this->option_prefix . 'cookie_notice_display') !== 'off') {
			add_action('wp_enqueue_scripts', [$this, 'enqueuer']);
			add_action('wp_head', [$this, 'styles'], 100);
		}
	}

	/**
	 * Adds plugin options on activation.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public function install() {

		if (get_option($this->option_prefix . 'cookie_notice_display') === false) {
			add_option($this->option_prefix . 'cookie_notice_display', 'off');
		}
	}

	/**
	 * Adds link to plugin settings page on main Plugins list page.
	 *
	 * @since  1.0.0
	 * @param  array $links   List of links.
	 * @return array Filtered list of links.
	 */
	public function add_action_link($links) {
		$custom_link = array(
			'<a href="' . admin_url('options-general.php?page=vital_privacy') . '">Settings</a>',
		);
		return array_merge($custom_link, $links);
	}

	/**
	 * Enqueues scripts and stylesheets.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function enqueuer() {

		$suffix = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? '' : '.min';

		wp_enqueue_script(
			'vtlprvmsg_cc',
			$this->plugin_url . 'assets/js/cookieconsent' . $suffix . '.js',
			false,
			$this->plugin_version,
			true
		);

		wp_enqueue_style(
			'vtlprvmsg_cc_css',
			$this->plugin_url . 'assets/css/cookieconsent' . $suffix . '.css',
			false,
			$this->plugin_version
		);

		wp_enqueue_script(
			'vtlprvmsg_cookie_notice',
			$this->plugin_url . 'assets/js/vital-privacy-cookie-notice' . $suffix . '.js',
			false,
			$this->plugin_version,
			true
		);

		$cookie_options = [
			'cookie_notice_display'           => get_option($this->option_prefix . 'cookie_notice_display'),
			'cookie_notice_pos'               => get_option($this->option_prefix . 'cookie_notice_pos'),
			'cookie_notice_msg'               => get_option($this->option_prefix . 'cookie_notice_msg'),
			'cookie_notice_accept_text'       => get_option($this->option_prefix . 'cookie_notice_accept_text'),
			'cookie_notice_accept_text_color' => get_option($this->option_prefix . 'cookie_notice_accept_text_color'),
			'cookie_notice_accept_bg_color'   => get_option($this->option_prefix . 'cookie_notice_accept_bg_color'),
			'cookie_notice_color'             => get_option($this->option_prefix . 'cookie_notice_color'),
			'cookie_notice_text_color'        => get_option($this->option_prefix . 'cookie_notice_text_color'),
		];

		wp_localize_script('vtlprvmsg_cookie_notice', 'VitalPrivacy', $cookie_options);
	}

	/**
	 * Prints inline CSS in wp_head.
	 *
	 * @since 1.1.0
	 */
	public function styles() {

		$styles = [];

		if ($link_color = get_option($this->option_prefix . 'cookie_notice_link_color')) {
			$styles[] = ".cc-message a { color: {$link_color}; }";
		}

		if (!empty($styles)) {

			$styles = join("\n", $styles);

			printf(
				"\n<style>%s</style>\n",
				esc_html($styles)
			);
		}
	}
}

new VitalPrivacyNotice();
