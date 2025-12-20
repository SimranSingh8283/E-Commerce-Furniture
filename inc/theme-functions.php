<?php
/**
 * Theme Gallery + Story Sub Heading Meta Boxes
 */

if (!class_exists('ThemeFunctions')) {
    class ThemeFunctions
    {
        public static function init()
        {
            // NEW: Sub heading meta box for stories
            add_action('add_meta_boxes', [__CLASS__, 'add_story_subheading_box']);
            add_action('save_post', [__CLASS__, 'save_story_subheading']);

            // NEW: force archive-product.php for product searches
            add_filter('template_include', [__CLASS__, 'force_product_search_template']);

            // Comment sorting
            add_action('pre_get_comments', [__CLASS__, 'sort_comments_by_dropdown']);
        }

        public static function comment_markup($comment, $args, $depth)
        {
            $tag = ($args['style'] === 'div') ? 'div' : 'li';
            ?>

            <<?php echo $tag; ?>             <?php comment_class('Comment-root'); ?> id="comment-<?php comment_ID(); ?>">

                <div class="Comment-header Comment-meta">
                    <div class="Comment-author">
                        <span class="Comment-avatar">
                            <?php echo get_avatar($comment, 50); ?>
                        </span>
                        <strong class="Comment-author"><?php comment_author(); ?></strong>
                    </div>
                    <span class="Comment-date">
                        <?php echo human_time_diff(get_comment_time('U'), current_time('timestamp')) . ' ago'; ?>
                    </span>
                </div>

                <div class="Comment-body">
                    <?php comment_text(); ?>
                </div>

                <?php
        }

        public static function sort_comments_by_dropdown($query)
        {
            if (!is_admin() && $query instanceof WP_Comment_Query) {
                $order = $_GET['comment_order'] ?? get_option('comment_order');

                if (in_array(strtolower($order), ['asc', 'desc'])) {
                    $query->query_vars['order'] = $order;
                }
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
    }
}

ThemeFunctions::init();