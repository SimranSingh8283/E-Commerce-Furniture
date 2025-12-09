<?php
/**
 * Theme Gallery Meta Box Class
 */

if (!class_exists('ThemeFunctions')) {
    class ThemeFunctions
    {
        public static function init()
        {
            add_action('add_meta_boxes', [__CLASS__, 'add_meta_box']);
            add_action('save_post', [__CLASS__, 'save_meta_box']);
        }

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

        public static function formatCurrency($amount, $region = 'IN')
        {
            $region = strtoupper($region);

            switch ($region) {
                case 'IN': // Indian Numbering System
                    if ($amount >= 10000000) {
                        return round($amount / 10000000, 2) . ' Cr';
                    } elseif ($amount >= 100000) {
                        return round($amount / 100000, 2) . ' L';
                    } elseif ($amount >= 1000) {
                        return round($amount / 1000, 2) . ' K';
                    } else {
                        return number_format($amount);
                    }

                case 'US':
                case 'EU':
                case 'UK':
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