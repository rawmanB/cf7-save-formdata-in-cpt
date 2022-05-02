<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
class Save_Form_Meta_Data
{

    public $data = array(
        'img_url' => [],
        'p_id' => '',
        'img_id' => []

    );
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

    public function save_form_data($contact_form)
    {
        if (class_exists('WPCF7_Submission')) {

            $wpcf7 = WPCF7_ContactForm::get_current();

            $form_id = $wpcf7->id;

            if ($form_id != 6) {
                return;
            }
            $submission = WPCF7_Submission::get_instance();
            if (!$submission) {
                return;
            }

            $title =  (isset($_POST['story-title'])) ? sanitize_text_field($_POST['story-title']) : '';
            $name =  (isset($_POST['your-name'])) ? sanitize_text_field($_POST['your-name']) : '';
            $story =  (isset($_POST['your-message'])) ? wp_kses_post($_POST['your-message']) : '';
            $email =  (isset($_POST['your-email'])) ? sanitize_text_field($_POST['your-email']) : '';
            $phone =  (isset($_POST['tel-966'])) ? sanitize_text_field($_POST['tel-966']) : '';
            $story_url =  (isset($_POST['url-0'])) ? esc_url_raw($_POST['url-0']) : '';

            $new_post = [];
            $new_post['post_type'] = 'story';
            $new_post['post_status'] = 'draft';

            if ($title != '') {
                $new_post['post_title'] = $title;
            } else {
                return;
            }

            if ($story != '') {
                $new_post['post_content'] = $story;
            } else {
                return;
            }

            $post_id = wp_insert_post($new_post);

            do_action('wp_insert_post', 'wp_insert_post', 10, 1);

            add_post_meta($post_id, 'story_teller_name', $name, true);

            add_post_meta($post_id, 'story_teller_email', $email, true);

            add_post_meta($post_id, 'story_teller_phone', $phone, true);

            add_post_meta($post_id, 'story_url', $story_url, true);

            add_post_meta($post_id, 'image_gallery', $this->data['img_url'], true);

            $this->data['p_id'] = $post_id;
        }
    }

    public function cog_create_attachment($filename)
    {
        $wpcf7 = WPCF7_ContactForm::get_current();

        $form_id = $wpcf7->id;

        if ($form_id != 6) {
            return;
        }
        // Check the type of file. We'll use this as the 'post_mime_type'.
        $filetype = wp_check_filetype(basename($filename), null);

        // Get the path to the upload directory.
        $wp_upload_dir = wp_upload_dir();

        $attachFileName = $wp_upload_dir['path'] . '/' . basename($filename);

        copy($filename, $attachFileName);
        // Prepare an array of post data for the attachment.
        $attachment = array(
            'guid'           => $attachFileName,
            'post_mime_type' => $filetype['type'],
            'post_title'     => preg_replace('/\.[^.]+$/', '', basename($filename)),
            'post_content'   => '',
            'post_status'    => 'inherit'
        );

        // Insert the attachment.
        $attach_id = wp_insert_attachment($attachment, $attachFileName);

        // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        // Required for audio attachments
        require_once(ABSPATH . 'wp-admin/includes/media.php');

        // Generate the metadata for the attachment, and update the database record.
        $attach_data = wp_generate_attachment_metadata($attach_id, $attachFileName);
        wp_update_attachment_metadata($attach_id, $attach_data);

        return $attach_id;
    }

    public function cog_on_before_cf7_send_mail(\WPCF7_ContactForm $contactForm)
    {
        $wpcf7 = WPCF7_ContactForm::get_current();

        $form_id = $wpcf7->id;

        if ($form_id != 6) {
            return;
        }
        $submission = WPCF7_Submission::get_instance();
        if ($submission) {
            $uploaded_files = $submission->uploaded_files();
            if ($uploaded_files) {
                foreach ($uploaded_files as $fieldName => $filepath) {
                    //cf7 5.4
                    if (is_array($filepath)) {
                        foreach ($filepath as $key => $value) {
                            $data = $this->cog_create_attachment($value);
                            $url = wp_get_attachment_url($data);
                            $arrayUrl[] = $url;
                            $arrayId[] = $data;
                        }
                    } else {
                        $data = $this->cog_create_attachment($filepath);
                        $url = wp_get_attachment_url($data);

                        $arrayUrl[] = $url;
                        $arrayId[] = $data;
                    }
                }

                $this->data['img_url'] = $arrayUrl;
                $this->data['img_id'] = $arrayId;
            }
        }
    }


    public function deletePostOnFail()
    {
        $wpcf7 = WPCF7_ContactForm::get_current();

        $form_id = $wpcf7->id;

        if ($form_id != 6) {
            return;
        }
        $pId = $this->data['p_id'];
        wp_delete_post($pId, true);
    }

    public function deleteAttachmentOnFail()
    {
        $wpcf7 = WPCF7_ContactForm::get_current();

        $form_id = $wpcf7->id;

        if ($form_id != 6) {
            return;
        }
        $imgId = $this->data['img_id'];

        foreach ($imgId as $id) {
            wp_delete_attachment($id, true);
        }
    }
}
