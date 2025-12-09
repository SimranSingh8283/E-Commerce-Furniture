<?php

if (!defined('ABSPATH'))
    exit;

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

        // ------- Content -------
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Button Settings', 'theme'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        // Button text
        $this->add_control(
            'text',
            [
                'label' => __('Text', 'theme'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Button', 'theme'),
                'placeholder' => __('Enter button text', 'theme'),
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
                    'text' => __('Text', 'theme'),
                    'outlined' => __('Outlined', 'theme'),
                ],
            ]
        );

        // Color
        $this->add_control(
            'color',
            [
                'label' => __('Color', 'theme'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'text',
                'options' => [
                    'primary' => __('Primary', 'theme'),
                    'secondary' => __('Secondary', 'theme'),
                    'error' => __('Error', 'theme'),
                    'warning' => __('Warning', 'theme'),
                    'info' => __('Info', 'theme'),
                    'success' => __('Success', 'theme'),
                    'text' => __('Text (Default)', 'theme'),
                ],
            ]
        );

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
                'placeholder' => __('https://your-link.com', 'theme'),
                'show_external' => true,
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();

        $text = $settings['text'];
        $variant = $settings['variant'];
        $color = $settings['color'];
        $size = $settings['size'];
        $link = $settings['link'];

        // Button classes (shared)
        $classes = 'Button-root Button-' . esc_attr($color);

        // Attributes (shared)
        $attrs = ' data-variant="' . esc_attr($variant) . '" data-size="' . esc_attr($size) . '"';

        // Check if link field has a URL
        if (!empty($link['url'])) {

            // Build attributes for <a>
            $this->add_render_attribute('button_link', 'href', esc_url($link['url']));

            if ($link['is_external']) {
                $this->add_render_attribute('button_link', 'target', '_blank');
            }

            if ($link['nofollow']) {
                $this->add_render_attribute('button_link', 'rel', 'nofollow');
            }

            echo '<a class="' . $classes . '" ' . $this->get_render_attribute_string('button_link') . $attrs . '>'
                . esc_html($text) .
                '</a>';

        } else {

            // Fallback: render <button>
            echo '<button class="' . $classes . '" ' . $attrs . '>'
                . esc_html($text) .
                '</button>';
        }
    }
}