<?php
if (!class_exists('TestimonialsModuleCard')) {
    class TestimonialsModuleCard {
        public static function render($post) {
            $post_id = $post->ID;
            $title = get_the_title($post_id);
            $content = apply_filters('the_content', $post->post_content);
            $designation = get_field('designation', $post_id);

            $thumbnail_url = get_the_post_thumbnail_url($post_id, 'thumbnail');
            $attachment_id = get_post_thumbnail_id($post_id);
            $alt_text = $attachment_id ? get_post_meta($attachment_id, '_wp_attachment_image_alt', true) : '';
            $image_title = $attachment_id ? get_the_title($attachment_id) : '';

            ?>
            <swiper-slide>
                <div class="Review-root">
                    <div class="Review-body">
                        <?= $content; ?>
                    </div>
                    <div class="Review-footer">
                        <?php if ($thumbnail_url) : ?>
                            <div class="Review-thumb">
                                <img src="<?= esc_url($thumbnail_url) ?>" alt="<?= esc_attr($alt_text) ?>" title="<?= esc_attr($image_title) ?>">
                            </div>
                        <?php endif; ?>
                        <div class="Review-info">
                            <div class="Review-title">
                                <h3><?= esc_html($title) ?></h3>
                            </div>
                            <?php if ($designation) : ?>
                                <div class="Review-designation">
                                    <h3><?= esc_html($designation) ?></h3>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </swiper-slide>
            <?php
        }
    }
}