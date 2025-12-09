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
            // Dark Logo Setting
            $wp_customize->add_setting('dark_logo', [
                'default' => '',
                'transport' => 'refresh',
            ]);

            $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'dark_logo', [
                'label' => __('Upload Dark Logo', THEME_SLUG),
                'section' => 'title_tagline',
                'settings' => 'dark_logo',
            ]));

            // === Social Links Section ===
            $social_section = THEME_SLUG . '_social_links';

            $wp_customize->add_section($social_section, [
                'title' => __('Social Links', THEME_SLUG),
                'priority' => 30,
            ]);

            $wp_customize->add_setting('facebook_link', ['default' => '', 'transport' => 'refresh']);
            $wp_customize->add_control('facebook_link', [
                'label' => __('Facebook URL', THEME_SLUG),
                'section' => $social_section,
                'type' => 'url',
            ]);

            $wp_customize->add_setting('instagram_link', ['default' => '', 'transport' => 'refresh']);
            $wp_customize->add_control('instagram_link', [
                'label' => __('Instagram URL', THEME_SLUG),
                'section' => $social_section,
                'type' => 'url',
            ]);

            $wp_customize->add_setting('linkedin_link', ['default' => '', 'transport' => 'refresh']);
            $wp_customize->add_control('linkedin_link', [
                'label' => __('LinkedIn URL', THEME_SLUG),
                'section' => $social_section,
                'type' => 'url',
            ]);

            $wp_customize->add_setting('youtube_link', ['default' => '', 'transport' => 'refresh']);
            $wp_customize->add_control('youtube_link', [
                'label' => __('YouTube URL', THEME_SLUG),
                'section' => $social_section,
                'type' => 'url',
            ]);

            // === Contact Info Section ===
            $contact_section = THEME_SLUG . '_contact_info';

            $wp_customize->add_section($contact_section, [
                'title' => __('Contact Information', THEME_SLUG),
                'priority' => 31,
            ]);

            $wp_customize->add_setting('phone_number', ['default' => '', 'transport' => 'refresh']);
            $wp_customize->add_control('phone_number', [
                'label' => __('Phone Number', THEME_SLUG),
                'section' => $contact_section,
                'type' => 'text',
            ]);

            $wp_customize->add_setting('email_address', ['default' => '', 'transport' => 'refresh']);
            $wp_customize->add_control('email_address', [
                'label' => __('Email Address', THEME_SLUG),
                'section' => $contact_section,
                'type' => 'email',
            ]);

            $wp_customize->add_setting('physical_address', ['default' => '', 'transport' => 'refresh']);
            $wp_customize->add_control('physical_address', [
                'label' => __('Physical Address', THEME_SLUG),
                'section' => $contact_section,
                'type' => 'text',
            ]);
        }
    }
}

ThemeCustomizer::init();