<?php
/**
 * Shortcodes
 */

function default_post_slider_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'category' => '',
        'tag' => '',
        'posts_per_page' => 5,
    ), $atts);

    $args = array(
        'post_type' => 'post',
        'posts_per_page' => $atts['posts_per_page'],
    );

    if (!empty($atts['category'])) {
        $args['category_name'] = $atts['category'];
    }

    if (!empty($atts['tag'])) {
        $args['tag'] = $atts['tag'];
    }

    $query = new WP_Query($args);

    if (!$query->have_posts())
        return 'No posts found.';

    ob_start();
    ?>
    <style>
        .Post-swiper-wrapper {
            background-color: hsl(0, 0%, 100%, 80%);
            max-width: 434px;
            padding: clamp(1.5rem, 1.85vw + 0.5rem, 2rem);
            border-top-left-radius: 1.5rem;
            aspect-ratio: 1 / 1.25;
        }

        .Post-swiper-wrapper .Block-heading [data-level="1"] {
            font-size: clamp(1.25rem, 1.5vw + 0.5rem, 1.5rem);
        }

        .Post-swiper-wrapper {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .Post-swiper-wrapper .Post-head {
            margin-bottom: 1.5rem;
        }

        .Post-swiper-wrapper .Post-swiper-controls {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .Post-swiper-wrapper .Post-pagination {
            margin-right: auto;
            display: flex;
            gap: 0.25rem;
        }

        .Post-swiper-wrapper .Post-pagination .swiper-pagination-bullet {
            width: 0.725rem;
            border: 2px solid var(--clr-primary-main);
            height: auto;
            border-radius: 50%;
            aspect-ratio: 1;
            background-color: transparent;
            cursor: pointer;
            transition: 300ms ease;
        }

        .Post-swiper-wrapper .Post-pagination .swiper-pagination-bullet.swiper-pagination-bullet-active {
            background-color: var(--clr-primary-main);
        }

        .Post-swiper-wrapper .Post-swiper-controls .Button-root {
            padding: 0.35em 0.5em;
            font-size: 1.5rem;
            border-radius: 0.5em;
            display: grid;
        }

        .Post-swiper-wrapper .Post-swiper-controls .Button-root[aria-disabled="true"] {
            opacity: 0.25;
            pointer-events: none;
            filter: grayscale(1);
        }
    </style>

    <div class="Post-swiper-wrapper">
        <swiper-container class="Post-swiper" init="false">
            <?php while ($query->have_posts()):
                $query->the_post(); ?>
                <swiper-slide class="Post-slide Post-root">
                    <div class="Post-head">
                        <div class="Block-heading">
                            <?php
                            if (!empty($atts['category']) && $atts['category'] === 'stories') {

                                $sub = get_post_meta(get_the_ID(), '_story_sub_heading', true);

                                if (!empty($sub)) { ?>
                                    <span aria-level="2" data-level="2">
                                        <?php echo esc_html($sub); ?>
                                    </span>
                                <?php }
                            }
                            ?>

                            <span aria-level="1" data-level="1"><?php the_title(); ?></span>
                        </div>
                    </div>

                    <div class="Post-body">
                        <?php
                        if (!empty($atts['category']) && $atts['category'] === 'stories') {
                            the_content();
                        } else {
                            the_excerpt();
                        }
                        ?>
                    </div>
                </swiper-slide>
            <?php endwhile; ?>
        </swiper-container>

        <div class="Post-swiper-controls">
            <div class="Post-pagination"></div>

            <div data-tooltip="Previous" class="Button-root Button-primary Post-prev" data-variant="contained">
                <iconify-icon icon="system-uicons:arrow-left"></iconify-icon>
            </div>

            <button data-tooltip="Next" class="Button-root Button-primary Post-next" data-variant="contained">
                <iconify-icon icon="system-uicons:arrow-right"></iconify-icon>
            </button>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {

            const swiperEl = document.querySelector('.Post-swiper-wrapper .Post-swiper');
            if (!swiperEl) return;

            const params = {
                slidesPerView: 1,
                loop: false,
                navigation: {
                    nextEl: ".Post-swiper-controls .Post-next",
                    prevEl: ".Post-swiper-controls .Post-prev"
                },
                pagination: {
                    el: ".Post-swiper-wrapper .Post-pagination",
                    clickable: true,
                    renderBullet: function (index, className) {
                        return `<span data-tooltip="Slide to ${index + 1}" class="${className}"></span>`;
                    }
                }
            };

            Object.assign(swiperEl, params);

            swiperEl.initialize();
        });
    </script>

    <?php
    wp_reset_postdata();
    return ob_get_clean();
}

add_shortcode('post_slider', 'default_post_slider_shortcode');
?>