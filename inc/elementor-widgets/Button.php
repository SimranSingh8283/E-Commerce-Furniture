<?php

if (!defined('ABSPATH')) exit;

class Button_Widget extends \Elementor\Widget_Base
{
    public function get_name()
    {
        return 'theme-button';
    }

    public function get_title()
    {
        return __('Theme Button', 'theme');
    }

    public function get_icon()
    {
        return 'eicon-button';
    }

    public function get_categories()
    {
        return ['general'];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Button Settings', 'theme'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        // Text
        $this->add_control(
            'text',
            [
                'label' => __('Text', 'theme'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Button', 'theme'),
            ]
        );

        // Variant
        $this->add_control(
            'variant',
            [
                'label' => __('Variant', 'theme'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'text',
                'options' => [
                    'contained' => __('Contained', 'theme'),
                    'text'      => __('Text', 'theme'),
                    'alpha'     => __('Alpha', 'theme'),
                    'underline' => __('Underline', 'theme'),
                    'outlined'  => __('Outlined', 'theme'),
                ],
            ]
        );

        // Predefined color
        $this->add_control(
            'color',
            [
                'label' => __('Theme Color', 'theme'),
                'type'  => \Elementor\Controls_Manager::SELECT,
                'default' => 'text',
                'options' => [
                    'primary'   => __('Primary', 'theme'),
                    'secondary' => __('Secondary', 'theme'),
                    'error'     => __('Error', 'theme'),
                    'warning'   => __('Warning', 'theme'),
                    'info'      => __('Info', 'theme'),
                    'success'   => __('Success', 'theme'),
                    'text'      => __('Default (Text)', 'theme'),
                ],
            ]
        );

        // Use custom color?
        $this->add_control(
            'use_custom_color',
            [
                'label' => __('Use Custom Color?', 'theme'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => __('Yes', 'theme'),
                'label_off' => __('No', 'theme'),
            ]
        );

        // Custom color
        $this->add_control(
            'custom_color',
            [
                'label' => __('Custom Color', 'theme'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '',
                'condition' => ['use_custom_color' => 'yes'],
            ]
        );

        // OPTIONAL: force !important — uncomment and add control if you need it
        // $this->add_control(
        //     'use_custom_color_important',
        //     [
        //         'label' => __('Force !important?', 'theme'),
        //         'type' => \Elementor\Controls_Manager::SWITCHER,
        //         'default' => '',
        //         'description' => 'If your theme uses !important, enable this to add !important to inline styles.',
        //         'condition' => ['use_custom_color' => 'yes'],
        //     ]
        // );

        // Size
        $this->add_control(
            'size',
            [
                'label' => __('Size', 'theme'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'md',
                'options' => [
                    'sm' => __('Small', 'theme'),
                    'md' => __('Medium', 'theme'),
                    'lg' => __('Large', 'theme'),
                ],
            ]
        );

        // Link
        $this->add_control(
            'link',
            [
                'label' => __('Link', 'theme'),
                'type' => \Elementor\Controls_Manager::URL,
                'placeholder' => __('https://your-link.com'),
                'show_external' => true,
            ]
        );

        // Icon / Image
        $this->add_control(
            'media_type',
            [
                'label' => __('Icon / Image Type', 'theme'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'none',
                'options' => [
                    'none' => __('None', 'theme'),
                    'icon' => __('Icon', 'theme'),
                    'image' => __('Image', 'theme'),
                ],
            ]
        );

        // Icon picker
        $this->add_control(
            'icon',
            [
                'label' => __('Icon', 'theme'),
                'type'  => \Elementor\Controls_Manager::ICONS,
                'condition' => ['media_type' => 'icon']
            ]
        );

        // Image upload
        $this->add_control(
            'image',
            [
                'label' => __('Image', 'theme'),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'condition' => ['media_type' => 'image']
            ]
        );

        // Position
        $this->add_control(
            'media_position',
            [
                'label' => __('Position', 'theme'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'right',
                'options' => [
                    'left' => __('Left', 'theme'),
                    'right' => __('Right', 'theme'),
                ],
                'condition' => ['media_type!' => 'none']
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $s = $this->get_settings_for_display();

        $text   = $s['text'];
        $variant = $s['variant'];
        $size    = $s['size'];
        $media_type = $s['media_type'];

        // Classes
        $classes = 'Button-root Button-' . esc_attr($s['color']);

        if ($media_type !== 'none') {
            $classes .= ' Button-icon-' . esc_attr($s['media_position']);
        }

        // Data attributes
        $attrs = ' data-variant="' . esc_attr($variant) . '" data-size="' . esc_attr($size) . '"';

        // MEDIA
        $media_html = '';

        if ($media_type === 'icon' && !empty($s['icon']['value'])) {
            $media_html = '<span class="Button-icon"><i class="' . esc_attr($s['icon']['value']) . '"></i></span>';
        }

        if ($media_type === 'image' && !empty($s['image']['url'])) {
            $media_html = '<span class="Button-image"><img src="' . esc_url($s['image']['url']) . '" alt=""></span>';
        }

        // Final HTML
        if ($media_type === 'none') {
            $inner_html = esc_html($text);
        } else {
            $inner_html = ($s['media_position'] === 'left')
                ? $media_html . esc_html($text)
                : esc_html($text) . $media_html;
        }

        // -------------------------------------------
        // Inline custom color override (applies inline styles)
        // -------------------------------------------
        $style_attr = '';

        if (!empty($s['use_custom_color']) && !empty($s['custom_color'])) {
            $c = esc_attr($s['custom_color']);

            // Basic defaults
            $bg = '';
            $txt = '';
            $bd = '';

            // Set per variant
            switch ($variant) {
                case 'contained':
                    $bg = $c;
                    $bd = $c;
                    // text color: choose white for better contrast — you can change if you want
                    $txt = '#ffffff';
                    break;

                case 'outlined':
                    $bg = 'transparent';
                    $bd = $c;
                    $txt = $c;
                    break;

                case 'alpha':
                case 'text':
                case 'underline':
                default:
                    $bg = 'transparent';
                    $bd = 'transparent';
                    $txt = $c;
                    break;
            }

            // OPTIONAL: force !important if needed (uncomment to enable)
            $important = ''; // set to ' !important' if your theme uses !important and you want to override

            // Build inline style string
            $style_parts = [];
            if ($txt !== '') {
                $style_parts[] = 'color: ' . $txt . $important . ';';
            }

            $style_attr = 'style="' . implode(' ', $style_parts) . '"';
        }
        // -------------------------------------------

        // Output button or link
        if (!empty($s['link']['url'])) {

            $this->add_render_attribute('button_link', 'href', esc_url($s['link']['url']));

            if (!empty($s['link']['is_external'])) {
                $this->add_render_attribute('button_link', 'target', '_blank');
            }

            if (!empty($s['link']['nofollow'])) {
                $this->add_render_attribute('button_link', 'rel', 'nofollow');
            }

            echo '<a class="' . $classes . '" ' . $style_attr . ' ' .
                $this->get_render_attribute_string('button_link') . $attrs . '>' .
                $inner_html .
            '</a>';

        } else {

            echo '<button class="' . $classes . '" ' . $style_attr . ' ' . $attrs . '>' .
                $inner_html .
            '</button>';
        }
    }
}