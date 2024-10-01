<?php
class GFCLH_Highlighter {
    private $options;

    public function __construct() {
        $this->options = get_option('gfclh_options');
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_styles'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'));
        add_filter('gform_field_css_class', array($this, 'add_conditional_class'), 10, 3);
        add_filter('gform_field_content', array($this, 'add_conditional_class_to_field_content'), 10, 5);
        add_action('wp_head', array($this, 'print_frontend_styles'));
    }

    public function enqueue_frontend_styles() {
        if (!empty($this->options['highlight_frontend'])) {
            wp_add_inline_style('gform_css', '.gfield_conditional_logic_active { ' . esc_html($this->options['frontend_css']) . ' }');
        }
    }

    public function enqueue_admin_styles($hook) {
        if (!empty($this->options['highlight_admin']) && ('toplevel_page_gf_edit_forms' === $hook || 'forms_page_gf_entries' === $hook)) {
            wp_add_inline_style('gform_admin', '.gfield_conditional_logic_active { ' . esc_html($this->options['admin_css']) . ' }');
        }
    }

    public function add_conditional_class($classes, $field, $form) {
        if (!empty($field->conditionalLogic) && !empty($field->conditionalLogic['rules'])) {
            $classes .= ' gfield_conditional_logic_active';
        }
        return $classes;
    }

    public function add_conditional_class_to_field_content($content, $field, $value, $lead_id, $form_id) {
        if (!empty($field->conditionalLogic) && !empty($field->conditionalLogic['rules'])) {
            $content = str_replace('class="gfield', 'class="gfield gfield_conditional_logic_active', $content);
        }
        return $content;
    }

    public function print_frontend_styles() {
        if (!empty($this->options['highlight_frontend'])) {
            echo '<style type="text/css">.gfield_conditional_logic_active { ' . esc_html($this->options['frontend_css']) . ' }</style>';
        }
    }
}
