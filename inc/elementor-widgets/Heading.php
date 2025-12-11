<?php

if (!defined('ABSPATH'))
    exit;

class Heading_Widget extends \Elementor\Widget_Base
{
    public function get_name()
    {
        return 'theme-heading';
    }

    public function get_title()
    {
        return __('Theme Heading', 'theme');
    }

    public function get_icon()
    {
        return 'eicon-t-letter';
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
                'label' => __('Heading Settings', 'theme'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        // Main heading
        $this->add_control(
            'main_heading',
            [
                'label' => __('Main Heading', 'theme'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Main Heading', 'theme'),
            ]
        );

        // Sub heading
        $this->add_control(
            'sub_heading',
            [
                'label' => __('Sub Heading (Optional)', 'theme'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '',
                'placeholder' => __('Add a sub heading'),
            ]
        );

        // Sub heading position
        $this->add_control(
            'sub_position',
            [
                'label' => __('Sub Heading Position', 'theme'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'top',
                'options' => [
                    'top' => __('Top', 'theme'),
                    'bottom' => __('Bottom', 'theme'),
                ],
                'condition' => ['sub_heading!' => ''],
            ]
        );

        $this->add_control(
            'main_prefix',
            [
                'label' => __('Main Heading Prefix (Optional)', 'theme'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '',
                'placeholder' => __('Add prefix before main heading'),
            ]
        );

        $this->add_control(
            'main_suffix',
            [
                'label' => __('Main Heading Suffix (Optional)', 'theme'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '',
                'placeholder' => __('Add suffix after main heading'),
            ]
        );

        $this->end_controls_section();

        // Style section
        $this->start_controls_section(
            'style_section',
            [
                'label' => __('Style', 'theme'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'main_typography',
                'label' => __('Main Heading Typography', 'theme'),
                'selector' => '{{WRAPPER}} .Block-heading-main',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'sub_typography',
                'label' => __('Sub Heading Typography', 'theme'),
                'selector' => '{{WRAPPER}} .Block-heading-sub',
                'condition' => ['sub_heading!' => ''],
            ]
        );

        $this->add_control(
            'main_color',
            [
                'label' => __('Main Heading Color', 'theme'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .Block-heading-main' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'sub_color',
            [
                'label' => __('Sub Heading Color', 'theme'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .Block-heading-sub' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'alignment',
            [
                'label' => __('Alignment', 'theme'),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'left' => ['title' => __('Left', 'theme'), 'icon' => 'eicon-text-align-left'],
                    'center' => ['title' => __('Center', 'theme'), 'icon' => 'eicon-text-align-center'],
                    'right' => ['title' => __('Right', 'theme'), 'icon' => 'eicon-text-align-right'],
                ],
                'default' => 'left',
                'toggle' => true,
                'selectors' => [
                    '{{WRAPPER}} .Block-heading' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $s = $this->get_settings_for_display();
        $main = wp_kses_post($s['main_heading']);
        $sub = wp_kses_post($s['sub_heading']);
        $sub_position = $s['sub_position'];

        // New prefix and suffix for main heading
        $main_prefix = isset($s['main_prefix']) ? wp_kses_post($s['main_prefix']) : '';
        $main_suffix = isset($s['main_suffix']) ? wp_kses_post($s['main_suffix']) : '';

        echo '<div class="Block-heading">';

        if ($sub && $sub_position === 'top') {
            echo '<span data-level="2" aria-level="2" class="Block-heading-sub">' . $sub . '</span>';
        }

        echo '<span data-level="1" aria-level="1" class="Block-heading-main">';
        if ($main_prefix)
            echo '<span class="Block-heading-main-prefix">' . $main_prefix . '</span>';
        echo $main;
        if ($main_suffix)
            echo '<span class="Block-heading-main-suffix">' . $main_suffix . '</span>';
        echo '</span>';

        if ($sub && $sub_position === 'bottom') {
            echo '<span data-level="2" aria-level="2" class="Block-heading-sub">' . $sub . '</span>';
        }

        echo '</div>';
    }
}