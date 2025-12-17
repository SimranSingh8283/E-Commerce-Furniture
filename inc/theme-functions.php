<?php
/**
 * Theme Gallery + Story Sub Heading Meta Boxes
 */

if (!class_exists('ThemeFunctions')) {
    class ThemeFunctions
    {
        public static function init()
        {
            // Existing gallery meta box
            add_action('add_meta_boxes', [__CLASS__, 'add_meta_box']);
            add_action('save_post', [__CLASS__, 'save_meta_box']);

            // NEW: Sub heading meta box for stories
            add_action('add_meta_boxes', [__CLASS__, 'add_story_subheading_box']);
            add_action('save_post', [__CLASS__, 'save_story_subheading']);

            // NEW: force archive-product.php for product searches
            add_filter('template_include', [__CLASS__, 'force_product_search_template']);
        }

        /**
         * -------------------------
         *  GALLERY META BOX (OLD)
         * -------------------------
         */
        public static function add_meta_box()
        {
            add_meta_box(
                'custom_gallery_box',
                __('Gallery Images', THEME_SLUG),
                [__CLASS__, 'render_meta_box'],
                'properties',
                'normal',
                'high'
            );
        }

        public static function render_meta_box($post)
        {
            $gallery_ids = get_post_meta($post->ID, 'custom_gallery_ids', true);
            ?>
            <input type="hidden" id="custom_gallery_ids" name="custom_gallery_ids" value="<?php echo esc_attr($gallery_ids); ?>">
            <button type="button" class="button select-gallery-button"><?php _e('Select Images', THEME_SLUG); ?></button>
            <div id="gallery-preview" style="margin-top:10px; display: flex; flex-wrap: wrap; gap: 10px;">
                <?php
                if ($gallery_ids) {
                    $ids = explode(',', $gallery_ids);
                    foreach ($ids as $id) {
                        $thumb = wp_get_attachment_image($id, 'thumbnail');
                        echo '<div class="gallery-item" data-id="' . esc_attr($id) . '" style="position:relative;cursor:move;">';
                        echo $thumb;
                        echo '<span class="remove-image" style="position:absolute;top:0;right:0;background:#000;color:#fff;padding:2px 6px;cursor:pointer;">&times;</span>';
                        echo '</div>';
                    }
                }
                ?>
            </div>

            <script>
                jQuery(document).ready(function ($) {
                    var frame;

                    function updateHiddenInput() {
                        var ids = [];
                        $('#gallery-preview .gallery-item').each(function () {
                            ids.push($(this).data('id'));
                        });
                        $('#custom_gallery_ids').val(ids.join(','));
                    }

                    $('#gallery-preview').sortable({
                        items: '.gallery-item',
                        update: updateHiddenInput
                    });

                    $('#gallery-preview').on('click', '.remove-image', function () {
                        $(this).closest('.gallery-item').remove();
                        updateHiddenInput();
                    });

                    $('.select-gallery-button').on('click', function (e) {
                        e.preventDefault();

                        if (frame) {
                            frame.open();
                            return;
                        }

                        frame = wp.media({
                            title: '<?php _e('Select or Upload Images', THEME_SLUG); ?>',
                            button: { text: '<?php _e('Use these images', THEME_SLUG); ?>' },
                            multiple: true
                        });

                        frame.on('select', function () {
                            var selection = frame.state().get('selection');

                            selection.each(function (attachment) {
                                attachment = attachment.toJSON();
                                var exists = $('#gallery-preview .gallery-item[data-id="' + attachment.id + '"]').length > 0;

                                if (!exists) {
                                    var thumb = attachment.sizes?.thumbnail?.url || attachment.url;
                                    var item = `
                                        <div class="gallery-item" data-id="${attachment.id}" style="position:relative; cursor:move;">
                                            <img src="${thumb}" style="width:100px;height:auto;">
                                            <span class="remove-image" style="position:absolute;top:0;right:0;background:#000;color:#fff;padding:2px 6px;cursor:pointer;">&times;</span>
                                        </div>
                                    `;
                                    $('#gallery-preview').append(item);
                                }
                            });

                            updateHiddenInput();
                        });

                        frame.open();
                    });
                });
            </script>
            <?php
        }

        public static function save_meta_box($post_id)
        {
            if (array_key_exists('custom_gallery_ids', $_POST)) {
                update_post_meta($post_id, 'custom_gallery_ids', sanitize_text_field($_POST['custom_gallery_ids']));
            }
        }

        public static function add_story_subheading_box()
        {
            add_meta_box(
                'story_subheading_box',
                __('Story Sub Heading', THEME_SLUG),
                [__CLASS__, 'render_story_subheading_box'],
                'post',
                'normal',
                'high'
            );
        }

        public static function render_story_subheading_box($post)
        {
            // Get category slugs
            $cats = wp_get_post_categories($post->ID, ['fields' => 'slugs']);

            // Check lowercase slug
            if (!in_array('stories', $cats)) {
                echo "<p style='color:#888;'>Assign this post to the <strong>Stories</strong> category to enable Sub Heading.</p>";
                return;
            }

            // Get saved value
            $value = get_post_meta($post->ID, '_story_sub_heading', true);
            ?>

            <label for="story_sub_heading"><strong>Sub Heading</strong></label>
            <input type="text" id="story_sub_heading" name="story_sub_heading" style="width:100%; margin-top:8px;"
                value="<?php echo esc_attr($value); ?>">

            <?php
        }

        public static function save_story_subheading($post_id)
        {
            if (array_key_exists('story_sub_heading', $_POST)) {
                update_post_meta(
                    $post_id,
                    '_story_sub_heading',
                    sanitize_text_field($_POST['story_sub_heading'])
                );
            }
        }

        public static function force_product_search_template($template)
        {
            if (is_search() && isset($_GET['post_type']) && $_GET['post_type'] === 'product') {
                $wc_template = wc_locate_template('archive-product.php');
                if ($wc_template)
                    return $wc_template;
            }

            // Case 2: Product category filter via GET param
            if (isset($_GET['product_cat']) && !is_admin()) {
                // Only if the current page is shop or home with GET param
                $shop_id = wc_get_page_id('shop');
                if (is_page($shop_id) || is_home() || is_front_page()) {
                    $wc_template = wc_locate_template('archive-product.php');
                    if ($wc_template)
                        return $wc_template;
                }
            }

            return $template;
        }

        /**
         * Currency Formatter
         */
        public static function formatCurrency($amount, $region = 'IN')
        {
            $region = strtoupper($region);

            switch ($region) {
                case 'IN':
                    if ($amount >= 10000000) {
                        return round($amount / 10000000, 2) . ' Cr';
                    } elseif ($amount >= 100000) {
                        return round($amount / 100000, 2) . ' L';
                    } elseif ($amount >= 1000) {
                        return round($amount / 1000, 2) . ' K';
                    } else {
                        return number_format($amount);
                    }

                default:
                    if ($amount >= 1000000000) {
                        return round($amount / 1000000000, 2) . ' B';
                    } elseif ($amount >= 1000000) {
                        return round($amount / 1000000, 2) . ' M';
                    } elseif ($amount >= 1000) {
                        return round($amount / 1000, 2) . ' K';
                    } else {
                        return number_format($amount);
                    }
            }
        }
    }
}

ThemeFunctions::init();