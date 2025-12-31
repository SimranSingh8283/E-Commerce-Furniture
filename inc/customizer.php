<?php
/**
 * Theme Customizer Class
 */

if (!class_exists('ThemeCustomizer')) {
    class ThemeCustomizer
    {
        public static function init()
        {
            add_action('customize_register', [__CLASS__, 'register']);
        }

        public static function register($wp_customize)
        {
            /**
             * Dark Logo
             */
            $wp_customize->add_setting('dark_logo', [
                'default'   => '',
                'transport' => 'refresh',
            ]);

            $wp_customize->add_control(
                new WP_Customize_Image_Control(
                    $wp_customize,
                    'dark_logo',
                    [
                        'label'   => __('Upload Dark Logo', THEME_SLUG),
                        'section' => 'title_tagline',
                        'settings'=> 'dark_logo',
                    ]
                )
            );

            /**
             * Social Links Section
             */
            $social_section = THEME_SLUG . '_social_links';

            $wp_customize->add_section($social_section, [
                'title'    => __('Social Links', THEME_SLUG),
                'priority' => 30,
            ]);

            // Facebook
            $wp_customize->add_setting('facebook_link', ['default' => '', 'transport' => 'refresh']);
            $wp_customize->add_control('facebook_link', [
                'label'   => __('Facebook URL', THEME_SLUG),
                'section' => $social_section,
                'type'    => 'url',
            ]);

            // X (Twitter)
            $wp_customize->add_setting('x_link', ['default' => '', 'transport' => 'refresh']);
            $wp_customize->add_control('x_link', [
                'label'   => __('X (Twitter) URL', THEME_SLUG),
                'section' => $social_section,
                'type'    => 'url',
            ]);

            // Instagram
            $wp_customize->add_setting('instagram_link', ['default' => '', 'transport' => 'refresh']);
            $wp_customize->add_control('instagram_link', [
                'label'   => __('Instagram URL', THEME_SLUG),
                'section' => $social_section,
                'type'    => 'url',
            ]);

            // LinkedIn
            $wp_customize->add_setting('linkedin_link', ['default' => '', 'transport' => 'refresh']);
            $wp_customize->add_control('linkedin_link', [
                'label'   => __('LinkedIn URL', THEME_SLUG),
                'section' => $social_section,
                'type'    => 'url',
            ]);
        }
    }
}

ThemeCustomizer::init();