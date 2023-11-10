<?php
/*
Plugin Name: Field Tooltips for Gravity Forms
Description: Adds tooltips functionality to Gravity Forms fields.
Version: 1.0
Author: Zohrab Mazmanyan
Text Domain: gravityforms-field-tooltip-addon
*/

final class GravityFormsFieldTooltip
{
    public static function init()
    {
        new static();
    }

    public function __construct()
    {
        $this->addActions();
        $this->addFilters();
    }

    public function addActions()
    {
        add_action( 'wp_enqueue_scripts', [$this, 'enqueue_scripts'] );
        add_action( 'wp_head', [$this, 'add_inline_styles'] );
        add_action( 'admin_head', [$this, 'add_admin_styles'] );
        add_action( 'wp_footer', [$this, 'add_inline_scripts'] );
        add_action( 'gform_editor_js', [$this, 'editor_script'] );
        add_action( 'gform_field_standard_settings', [$this, 'add_tooltip_setting'], 999, 2 );
    }

    public function addFilters()
    {
        add_filter( 'gform_field_content', [$this, 'render_tooltip'], 10, 5 );
    }

    public function enqueue_scripts()
    {
        wp_enqueue_script( 'jquery-ui-tooltip' );
    }

    public function add_inline_styles()
    {
        ?>
        <style>
            /* CSS for info icons */
            .gf-info-icon {
                position: relative;
                display: inline-block;
                width: 16px;
                height: 16px;
                margin-left: 5px;
                background-size: cover;
                cursor: pointer;
            }

            /* Tooltip style */
            .ui-tooltip {
                position: absolute;
                z-index: 9999;
                max-width: 300px;
                background: #fff;
                padding: 5px 10px;
                box-shadow: 0 0 10px 0px #888;
                border-radius: 5px;
            }

        </style>
        <?php
    }

    public function add_admin_styles()
    {
        ?>
        <style>
            .tooltip-content-setting.field_setting {
                display: block !important;
            }
        </style>
        <?php
    }

    public function add_inline_scripts()
    {
        ?>
        <script type="text/javascript">
            jQuery( ( $ ) => { $( '[data-tooltip]' ).tooltip(); });
        </script>
        <?php
    }

    public function editor_script(){
        ?>
        <script type='text/javascript'>
            fieldSettings.text += ", .tooltip-content-setting";
            jQuery(document).on("gform_load_field_settings", function(event, field, form){
                jQuery( '#field_tooltip_content' ).val(field.tooltip_content);
            });
        </script>
        <?php
    }

    public function add_tooltip_setting( $position, $form_id )
    {
        if ( $position == 5 ) {
            ?>
            <li class="tooltip-content-setting field_setting">
                <label for="field_tooltip_content"><?php esc_html_e( 'Label Tooltip', 'gravityforms-field-tooltip-addon' ); ?></label>
                <textarea class="tooltip-content-input fieldwidth-3" id="field_tooltip_content" onkeyup="SetFieldProperty('tooltip_content', this.value);"></textarea>
            </li>
            <?php
        }
    }

    public function render_tooltip( $content, $field, $value, $lead_id, $form_id )
    {
        $tooltip_content = $field->tooltip_content;
        ob_start();
        if ( ! empty( $tooltip_content ) ) { ?>
            <span class="gf-info-icon" data-tooltip="tooltip" title="<?= esc_attr( $tooltip_content ) ?>">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="16px" height="16px">
                    <path fill="#2196f3" d="M44,24c0,11.045-8.955,20-20,20S4,35.045,4,24S12.955,4,24,4S44,12.955,44,24z"/>
                    <path fill="#fff" d="M22 22h4v11h-4V22zM26.5 16.5c0 1.379-1.121 2.5-2.5 2.5s-2.5-1.121-2.5-2.5S22.621 14 24 14 26.5 15.121 26.5 16.5z"/>
                </svg>
            </span>
        <?php }
        $content = ob_get_clean() . $content;
        return $content;
    }
}

GravityFormsFieldTooltip::init();
