<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @since      0.0.1
 *
 * @package    Cf_Frontendsub
 * @subpackage Cf_Frontendsub/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      0.0.1
 * @package    Cf_Frontendsub
 * @subpackage Cf_Frontendsub/includes
 */
class Cf_Frontendsub
{

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    0.0.1
	 * @access   protected
	 * @var      Cf_Frontendsub_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    0.0.1
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    0.0.1
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    0.0.1
	 */
	public function __construct()
	{
		define('WPCF7_LOAD_JS', false);
		if (defined('CF7_FRONTEND_SUBMISSION')) {
			$this->version = CF7_FRONTEND_SUBMISSION;
		} else {
			$this->version = '0.0.1';
		}
		$this->plugin_name = 'CF7 Front end submission to CPT';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    0.0.1
	 * @access   private
	 */
	private function load_dependencies()
	{

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-cf-frontendsub-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-cf-frontendsub-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */

		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-create-stories-cpt.php';

		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/shortcode/single-story.php';


		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-save-metadata.php';




		$this->loader = new Cf_Frontendsub_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Cf_Frontendsub_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    0.0.1
	 * @access   private
	 */
	private function set_locale()
	{

		$plugin_i18n = new Cf_Frontendsub_i18n();

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    0.0.1
	 * @access   private
	 */
	private function define_admin_hooks()
	{
		$story = new Create_Stories_Cpt($this->get_plugin_name(), $this->get_version());
		$sc = new Create_Short_Code_Cog($this->get_plugin_name(), $this->get_version());
		$this->loader->add_action('init', $story, 'createStoreisPostType');
		$this->loader->add_action('add_meta_boxes', $story, 'add_custom_metabox');
		$this->loader->add_action('save_post', $story, 'savePostData');
		$this->loader->add_shortcode('latest_story', $sc, 'get_latest_post');



		// $this->getformData;
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    0.0.1
	 * @access   private
	 */
	private function define_public_hooks()
	{
		$public_form = new Save_Form_Meta_Data($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('wpcf7_mail_failed', $public_form, 'deleteAttachmentOnFail');
		$this->loader->add_action('wpcf7_mail_failed', $public_form, 'deletePostOnFail');
		$this->loader->add_action('wpcf7_before_send_mail', $public_form, 'cog_on_before_cf7_send_mail', 99);
		$this->loader->add_action('wpcf7_before_send_mail', $public_form, 'save_form_data', 99);
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    0.0.1
	 */
	public function run()
	{
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     0.0.1
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name()
	{
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     0.0.1
	 * @return    Cf_Frontendsub_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader()
	{
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     0.0.1
	 * @return    string    The version number of the plugin.
	 */
	public function get_version()
	{
		return $this->version;
	}
}
