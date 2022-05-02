<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}


class Create_Short_Code_Cog
{
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }
    public function get_latest_post($attr)
    {
        $args = shortcode_atts(array(
            'post_type' => 'story'

        ), $attr);

        $args = array(
            'numberposts' => '1',
            'post_type' => $args['post_type']
        );
        $recent_posts = wp_get_recent_posts($args);
        $output = "";

        foreach ($recent_posts as $recent_post) {
            $name = get_post_meta($recent_post['ID'], 'story_teller_name', true);
            $story_url = get_post_meta($recent_post['ID'], 'story_url', true);
            $images = get_post_meta($recent_post['ID'], 'image_gallery', true);

            $content = (!empty($recent_post['post_content'])) ? $recent_post['post_content'] : '';

            ob_start();
            echo '<h1>';
            echo esc_html($recent_post['post_title']);
            echo '</h1>';
            echo wp_kses_post($content);
            echo '<div class = "Imggallery" style ="margin-top : 5px">';
            foreach ($images as $image) {
                echo '<img src="' . esc_url_raw($image) . '" style = "max-height : 250px; max-width : 250px; margin-right: 5px">';
            }
            echo '</div>';

            if ($story_url) {
                echo '<div class= "story_url">Story Url: ' . esc_url_raw($story_url) . '</div>';
            }

            echo '<div class= "autho">Author: ' . esc_html($name) . '</div>';
        }

        $output =  ob_get_clean();

        return $output;
    }
}
