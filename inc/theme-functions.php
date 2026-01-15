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

        public static function hide_layout_elements()
        {
            return is_404() || (is_account_page() && !is_user_logged_in());
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

            if (is_shop() && !is_admin()) {

                $wc_template = wc_locate_template('archive-product.php');
                if ($wc_template) {
                    return $wc_template;
                }
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

        /*
        [
            'finish-colors' => [
                'label' => 'Finish & Colors',
                'attributes' => [
                    [
                        'label' => 'Finish Color',
                        'slug' => 'pa_fc_finish_color',
                        'options' => [
                            ['id'=>12, 'slug'=>'tobacco', 'name'=>'Tobacco', 'type'=>'color', 'color'=>'#6b4a2d', 'image'=>null],
                            ['id'=>13, 'slug'=>'walnut', 'name'=>'Walnut', 'type'=>'image', 'color'=>null, 'image'=>34],
                        ]
                    ],
                    [
                        'label' => 'Base Material',
                        'slug' => 'pa_fc_base_material',
                        'options' => [...]
                    ]
                ]
            ],
            'upholstery' => [...],
            'size' => [...],
            'shipping' => [...]
        ]
        */
        public static function get_product_attribute_data($product_id)
        {
            $product = wc_get_product($product_id);
            if (!$product) {
                return [];
            }

            $attribute_tab_map = [
                'fc' => 'Finish & Colors',
                'up' => 'Upholstery',
                'sz' => 'Size',
            ];

            $result = [];

            foreach ($product->get_attributes() as $attribute) {

                $taxonomy = $attribute->get_name();
                if (!str_starts_with($taxonomy, 'pa_')) {
                    continue;
                }

                $slug = str_replace('pa_', '', $taxonomy);
                $prefix = explode('_', $slug)[0];

                $tabKey = $attribute_tab_map[$prefix] ?? 'Other';
                $tabSlug = sanitize_title($tabKey);

                if (!isset($result[$tabSlug])) {
                    $result[$tabSlug] = [
                        'label' => $tabKey,
                        'attributes' => [],
                    ];
                }

                if ($attribute->is_taxonomy()) {
                    $terms = wc_get_product_terms($product_id, $taxonomy, ['fields' => 'all']);
                } else {
                    $terms = $attribute->get_options();
                }

                $options = [];

                foreach ($terms as $term) {
                    if (is_object($term)) {
                        $options[] = [
                            'id' => $term->term_id,
                            'slug' => $term->slug,
                            'name' => $term->name,
                            'type' => get_field('swatch_type', 'term_' . $term->term_id),
                            'color' => get_field('attr_color', 'term_' . $term->term_id),
                            'image' => get_field('attr_image', 'term_' . $term->term_id),
                        ];
                    } else {
                        $options[] = [
                            'slug' => sanitize_title($term),
                            'name' => $term,
                            'type' => 'text',
                        ];
                    }
                }

                $result[$tabSlug]['attributes'][] = [
                    'label' => wc_attribute_label($taxonomy),
                    'slug' => $taxonomy,
                    'options' => $options,
                ];
            }

            return $result;
        }
        /**
         * Render product attributes as swatches
         *
         * @param WC_Product|int $product Product object or ID
         * @param array $show_only Optional array of attribute slugs to render
         */
        public static function render_product_attributes_swatches($product, $tab_slug = null, $show_only = [])
        {
            if (is_int($product)) {
                $product = wc_get_product($product);
            }

            if (!$product instanceof WC_Product) {
                return;
            }

            $product_data = self::get_product_attribute_data($product->get_id());

            // Determine which tabs to render
            if ($tab_slug && isset($product_data[$tab_slug])) {
                $tabs_to_render = [$tab_slug => $product_data[$tab_slug]];
            } else {
                $tabs_to_render = $product_data;
            }

            foreach ($tabs_to_render as $slug => $tab) {

                $tab_attributes = $tab['attributes'];

                // Optional filter by specific attributes
                if (!empty($show_only)) {
                    $tab_attributes = array_filter($tab_attributes, function ($attr) use ($show_only) {
                        return in_array($attr['slug'], $show_only, true);
                    });
                }

                if (empty($tab_attributes)) {
                    continue;
                }

                echo '<div class="Attribute-group">';

                foreach ($tab_attributes as $attribute): ?>
                        <div class="Attribute-root">
                            <div class="Attribute-title">
                                <h4><?php echo esc_html($attribute['label']); ?>:</h4>
                            </div>

                            <div class="Attribute-options">
                                <?php $i = 0; ?>
                                <?php foreach ($attribute['options'] as $opt): ?>
                                    <label class="Swatch-root" data-tooltip="<?php echo esc_html($opt['name']); ?>">
                                        <input type="radio" name="attribute_<?php echo esc_attr($attribute['slug']); ?>"
                                            value="<?php echo esc_attr($opt['slug']); ?>" <?php checked($i, 0); ?> />

                                        <?php
                                        $type = is_array($opt['type'])
                                            ? ($opt['type']['value'] ?? '')
                                            : $opt['type'];

                                        if ($type === 'image' && !empty($opt['image'])): ?>
                                            <img class="Swatch-image"
                                                src="<?php echo esc_url(wp_get_attachment_image_url($opt['image'], 'thumbnail')); ?>"
                                                alt="<?php echo esc_attr($opt['name']); ?>" />
                                        <?php elseif ($type === 'color' && !empty($opt['color'])): ?>
                                            <span class="Swatch-color" style="background-color: <?php echo esc_attr($opt['color']); ?>;"></span>
                                        <?php endif; ?>

                                        <span class="Swatch-name" style="display:none;">
                                            <?php echo esc_html($opt['name']); ?>
                                        </span>
                                    </label>
                                    <?php $i++; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach;

                echo '</div>';
            }
        }

        public static function render_cart_attribute($item, $wrapper_class = '')
        {
            if (empty($item) || !is_array($item)) {
                return;
            }
            ?>
                <div class="Attribute-root Attribute-cart <?php echo esc_attr($wrapper_class); ?>">
                    <div class="Attribute-options">

                        <?php foreach ($item as $key => $value): ?>
                            <?php
                            // Only product attributes
                            if (strpos($key, 'attribute_pa_') !== 0) {
                                continue;
                            }

                            $taxonomy = str_replace('attribute_', '', $key);
                            $label = wc_attribute_label($taxonomy);

                            // Get term
                            $term = get_term_by('slug', $value, $taxonomy);
                            if (!$term) {
                                continue;
                            }

                            // Term meta
                            $color = get_term_meta($term->term_id, 'attr_color', true);
                            $image_id = get_term_meta($term->term_id, 'attr_image', true);
                            $image = $image_id ? wp_get_attachment_image_url($image_id, 'thumbnail') : null;
                            ?>

                            <div class="Swatch-root" data-tooltip="<?php echo esc_attr($label . ': ' . $term->name); ?>">

                                <?php if ($image): ?>
                                    <img class="Swatch-image" src="<?php echo esc_url($image); ?>"
                                        alt="<?php echo esc_attr($term->name); ?>">

                                <?php elseif ($color): ?>
                                    <span class="Swatch-color" style="background:<?php echo esc_attr($color); ?>;"></span>
                                <?php endif; ?>

                            </div>

                        <?php endforeach; ?>

                    </div>
                </div>
                <?php
        }
        public static function render_order_item_attributes($item, $wrapper_class = '')
        {
            if (!$item instanceof WC_Order_Item_Product) {
                return;
            }

            $product = $item->get_product();
            if (!$product) {
                return;
            }

            $product_attributes = $product->get_attributes();
            $meta_data = $item->get_meta_data();

            if (empty($meta_data)) {
                return;
            }
            ?>
                <div class="Attribute-root Attribute-order <?php echo esc_attr($wrapper_class); ?>">
                    <div class="Attribute-options">

                        <?php foreach ($meta_data as $meta): ?>
                            <?php
                            $label = $meta->key;   // Finish Color
                            $value = $meta->value; // Chocolate
                            ?>

                            <?php foreach ($product_attributes as $attribute): ?>
                                <?php
                                // Match attribute label
                                if (wc_attribute_label($attribute->get_name()) !== $label) {
                                    continue;
                                }

                                // Only taxonomy-backed attributes
                                if (!$attribute->is_taxonomy()) {
                                    continue;
                                }

                                $taxonomy = $attribute->get_name();

                                // Match term by NAME
                                $term = get_term_by('name', $value, $taxonomy);
                                if (!$term) {
                                    continue;
                                }

                                $color = get_term_meta($term->term_id, 'attr_color', true);
                                $image_id = get_term_meta($term->term_id, 'attr_image', true);
                                $image = $image_id ? wp_get_attachment_image_url($image_id, 'thumbnail') : null;
                                ?>

                                <div class="Swatch-root" data-tooltip="<?php echo esc_attr($label . ': ' . $term->name); ?>">

                                    <?php if ($image): ?>
                                        <img class="Swatch-image" src="<?php echo esc_url($image); ?>"
                                            alt="<?php echo esc_attr($term->name); ?>">

                                    <?php elseif ($color): ?>
                                        <span class="Swatch-color" style="background:<?php echo esc_attr($color); ?>;"></span>

                                    <?php else: ?>
                                        <span class="Swatch-text">
                                            <?php echo esc_html($term->name); ?>
                                        </span>
                                    <?php endif; ?>

                                </div>

                            <?php endforeach; ?>

                        <?php endforeach; ?>

                    </div>
                </div>
                <?php
        }

        public static function cw_get_wishlist_count($user_id = null)
        {
            $user_id = $user_id ?: get_current_user_id();
            $wishlist = get_user_meta($user_id, '_cw_wishlist', true);
            return is_array($wishlist) ? count($wishlist) : 0;
        }

        public static function cw_is_in_wishlist($product_id, $user_id = null)
        {
            $user_id = $user_id ?: get_current_user_id();
            $wishlist = get_user_meta($user_id, '_cw_wishlist', true);
            return is_array($wishlist) && in_array($product_id, $wishlist);
        }
    }
}

ThemeFunctions::init();