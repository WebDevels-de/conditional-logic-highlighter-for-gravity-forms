<?php
class GFCLH_Highlighter {
    private $options;

    public function __construct() {
        $this->options = get_option('gfclh_options');
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_styles'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'));
        add_filter('gform_field_css_class', array($this, 'add_conditional_class'), 10, 3);
        add_action('gform_editor_js', array($this, 'add_form_editor_script'));
    }

    public function enqueue_frontend_styles() {
        // Debug: Überprüfen Sie, ob die Methode aufgerufen wird
        error_log('GFCLH: enqueue_frontend_styles wurde aufgerufen');

        if (!empty($this->options['highlight_frontend'])) {
            // Debug: Überprüfen Sie den Wert von highlight_frontend
            error_log('GFCLH: highlight_frontend ist aktiviert');

            $custom_css = $this->options['frontend_css'] ?? '';
            $default_css = 'background-color: #e6f3ff !important; border: 1px solid #2196f3 !important; border-radius: 6px !important;';

            // Debug: Überprüfen Sie das benutzerdefinierte CSS
            error_log('GFCLH: Benutzerdefiniertes CSS: ' . $custom_css);

            if (empty(trim($custom_css))) {
                $css = "
                .gfield_conditional_logic_active {
                    {$default_css}
                }
                ";
            } else {
                $css = "
                .gfield_conditional_logic_active {
                    {$custom_css}
                }
                ";
            }

            // Debug: Überprüfen Sie das endgültige CSS
            error_log('GFCLH: Endgültiges CSS: ' . $css);

            // Fügen Sie das CSS direkt in den Header ein, anstatt wp_add_inline_style zu verwenden
            add_action('wp_head', function() use ($css) {
                echo "<style type='text/css'>\n{$css}\n</style>\n";
            });

            // Debug: Überprüfen Sie, ob wp_head Aktion hinzugefügt wurde
            error_log('GFCLH: wp_head Aktion wurde hinzugefügt');
        } else {
            // Debug: Wenn highlight_frontend nicht aktiviert ist
            error_log('GFCLH: highlight_frontend ist nicht aktiviert');
        }
    }

    public function enqueue_admin_styles($hook) {
        if (!empty($this->options['highlight_admin']) && ('toplevel_page_gf_edit_forms' === $hook || 'forms_page_gf_entries' === $hook)) {
            wp_add_inline_style('gform_admin', $this->get_highlight_css('admin'));
        }
    }

    private function get_highlight_css($context) {
        $css = $context === 'admin' ? $this->options['admin_css'] : $this->options['frontend_css'];
        return ".gform_wrapper .gfield.gfield_conditional_logic_active {
            {$css}
        }";
    }

    public function add_conditional_class($classes, $field, $form) {
        if (!empty($field->conditionalLogic) && !empty($field->conditionalLogic['rules'])) {
            $classes .= ' gfield_conditional_logic_active';
        }
        return $classes;
    }

    public function add_form_editor_script() {
?>
        <script type="text/javascript">
            gform.addAction('gform_post_load_field_settings', function(fields, form) {
                updateAllFieldsHighlight(form);
            });

            gform.addFilter('gform_conditional_logic_description', function(description, descProp, ruleIdx, rule, fieldId, formId) {
                setTimeout(function() {
                    updateAllFieldsHighlight(form);
                }, 0);
                return description;
            });

            function updateAllFieldsHighlight(form) {
                if (!form || !form.fields) return;

                form.fields.forEach(function(field) {
                    var $field = jQuery('#field_' + field.id);
                    if (field.conditionalLogic && field.conditionalLogic.rules && field.conditionalLogic.rules.length > 0) {
                        $field.addClass('gfield_conditional_logic_active');
                    } else {
                        $field.removeClass('gfield_conditional_logic_active');
                    }
                });
            }

            jQuery(document).on('gform_field_deleted gform_field_added', function(event, form) {
                setTimeout(function() {
                    updateAllFieldsHighlight(form);
                }, 100);
            });

            gform.addAction('gform_after_refresh_field_preview', function(fieldId, formId) {
                var form = window.form; // Gravity Forms stores the current form in the global 'form' variable
                if (form) {
                    updateAllFieldsHighlight(form);
                }
            });
        </script>
<?php
    }
}
