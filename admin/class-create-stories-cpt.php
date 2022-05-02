<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

class Create_Stories_Cpt
{
    /**
     * The ID of this plugin.
     *
     * @since    0.0.1
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    0.0.1
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    0.0.1
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function createStoreisPostType()
    {
        $labels = array(
            'name'               => _x('Stories', 'post type general name'),
            'singular_name'      => _x('story', 'post type singular name'),
            'add_new'            => _x('Add New', 'book'),
            'add_new_item'       => __('Add New story'),
            'edit_item'          => __('Edit story'),
            'new_item'           => __('New story'),
            'all_items'          => __('All Stories'),
            'view_item'          => __('View story'),
            'search_items'       => __('Search Stories'),
            'not_found'          => __('No Stories found'),
            'not_found_in_trash' => __('No Stories found in the Trash'),
            'parent_item_colon'  => '',
            'menu_name'          => 'Stories'
        );
        $args = array(
            'labels'        => $labels,
            'description'   => 'Holds our Stories and story specific data',
            'public'        => true,
            'menu_position' => 5,
            'supports'      => array('title', 'editor', 'thumbnail'),
            'has_archive'   => true,
        );
        register_post_type('story', $args);
    }

    public function add_custom_metabox()
    {
        add_meta_box(
            'story_teller_details',
            __('Story Teller Details', 'frontend-sub-cf7'),
            array($this, 'custom_meta_box_content'),
            'story',
            'advanced',
            'default'
        );

        add_meta_box(
            'story_url',
            __('Story URL', 'frontend-sub-cf7'),
            array($this, 'storyUrlMetabox'),
            'story',
            'advanced',
            'default'
        );

        add_meta_box(
            'story_image_gallery',
            __('Story Image Gallery', 'frontend-sub-cf7'),
            array($this, 'storyImageGallery'),
            'story',
            'advanced',
            'default'
        );
    }

    public function custom_meta_box_content($post)
    {
        wp_nonce_field('cog_story_teller_details', 'cog_story_teller_details');

        $name = get_post_meta($post->ID, 'story_teller_name', true);
        $email = get_post_meta($post->ID, 'story_teller_email', true);
        $phone = get_post_meta($post->ID, 'story_teller_phone', true);

        echo '<div class="mb-5">';
        echo '<span><label for="story_teller_name">' . __('Name : ', 'frontend-sub-cf7') . ' </label></span>';
        echo '<input type="text" size= "100" name="story_teller_name" value="' . esc_html($name, 'frontend-sub-cf7') . '">';
        echo '</div>';

        echo '</br>';
        echo '<div class="mb-5">';
        echo '<span><label for="story_teller_email">' . __('Email : ', 'frontend-sub-cf7') . '</label></span>';
        echo '<input type="text" size= "100" name="story_teller_email" value="' . esc_html($email, 'frontend-sub-cf7') . '">';
        echo '</div>';

        echo '</br>';
        echo '<div class="mb-5">';
        echo '<span><label for="story_teller_phone">' . __('Phone : ', 'frontend-sub-cf7') . '</label></span>';
        echo '<input type="text" size= "100" name="story_teller_phone" value="' . esc_html($phone, 'frontend-sub-cf7') . '">';
        echo '</div>';
    }

    public function storyUrlMetabox($post)

    {
        wp_nonce_field('cog_story_url', 'cog_story_url');

        $story_url = get_post_meta($post->ID, 'story_url', true);

        echo '<div class="mb-5">';
        echo '<span><label for="story_url">' . __('Story Url : ', 'frontend-sub-cf7') . '</label></span>';
        echo '<input type="text" size= "150" name="story_url" value="' . esc_url_raw($story_url, 'frontend-sub-cf7') . '">';
        echo '</div>';
    }

    public function storyImageGallery($post)
    {
        wp_nonce_field('cog_story_images', 'cog_story_images');

        // $contents = get_post_meta($post->ID, 'image_galler[]', false);
        $contents = get_post_meta($post->ID, 'image_gallery', false);

        $output = '<div class="cog_gallery_images">';

        foreach ($contents['0'] as $url) {
            $output .= '<img src = ' . esc_url_raw($url) . ' style = "max-height : 250px; max-width : 250pxp; margin-right: 5px">';
        }

        $output .= '</>';

        echo $output;
    }

    public function savePostData($post)
    {
        if (!is_admin()) {
            return;
        }
        // Check autosave.
        if (wp_is_post_autosave($post)) {
            return $post;
        }

        // Check post revision.
        if (wp_is_post_revision($post)) {
            return $post;
        }

        if (!isset($_POST['cog_story_images']) || !isset($_POST['cog_story_url']) || !isset($_POST['cog_story_teller_details'])) {
            echo 'error1';
            return;
        }

        // Verify that the nonce is valid.
        if (!wp_verify_nonce($_POST['cog_story_images'], 'cog_story_images')) {
            echo 'error2';
            return;
        }

        // Verify that the nonce is valid.
        if (!wp_verify_nonce($_POST['cog_story_teller_details'], 'cog_story_teller_details')) {
            echo 'error3';
            return;
        }

        // Verify that the nonce is valid.
        if (!wp_verify_nonce($_POST['cog_story_url'], 'cog_story_url')) {
            echo 'error4';
            return;
        }

        $story_teller_name = sanitize_text_field($_POST['story_teller_name']);
        $story_teller_email = sanitize_text_field($_POST['story_teller_email']);
        $story_teller_phone = sanitize_text_field($_POST['story_teller_phone']);
        $story_url = esc_url_raw($_POST['story_url']);


        update_post_meta($post, 'story_teller_name', $story_teller_name);
        update_post_meta($post, 'story_teller_email', $story_teller_email);
        update_post_meta($post, 'story_teller_phone', $story_teller_phone);
        update_post_meta($post, 'story_url', $story_url);
    }
}
