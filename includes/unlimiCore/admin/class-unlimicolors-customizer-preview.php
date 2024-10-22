<?php
/**
 * Customizer.
 */

namespace UNLIMICOLORS\Admin;

use \UNLIMICOLORS\Core\UNLIMICOLORS_Customize;

/**
 * Customizer Preview.
 */
class UNLIMICOLORS_Customizer_Preview extends UNLIMICOLORS_Customize
{
	/**
	 * Constructor.
	 */ 
    public function __construct() 
    {
        parent::__construct();

        $this->_initControls();
        $this->_initPreview();
    }

    protected function _initControls()
    {
        new UNLIMICOLORS_Dynamic_Customizer( $this->_getSettings() );
        add_action( 'customize_controls_enqueue_scripts', [$this, 'enqueueScriptsLiveControls'], 99 );
    }

    protected function _initPreview()
    {
        if ( $this->disabledActionBox() ) {
            return false;
        }

        new UNLIMICOLORS_AjaxCustomizer();
        add_action( 'customize_preview_init', [$this, 'previewInit'], 99 );
    }

    public function previewInit() {
        // Enqueue previewer scripts and styles
        add_action( 'wp_enqueue_scripts', [$this, 'enqueueScriptsLivePreview'], 99 );
    }

    public function enqueueScriptsLivePreview()
    {
        wp_enqueue_style(UNLIMICOLORS_SLUG.'box-styles', UNLIMICOLORS_PLUGIN_PATH . '/includes/unlimiCore/assets/css/unlimibox-styles.css', [], UNLIMICOLORS_VERSION);
        wp_enqueue_style(UNLIMICOLORS_SLUG.'icons', UNLIMICOLORS_PLUGIN_PATH . '/includes/unlimiCore/assets/css/icons.css', [], UNLIMICOLORS_VERSION);
        wp_enqueue_script(UNLIMICOLORS_SLUG.'box-scripts-js', UNLIMICOLORS_PLUGIN_PATH . '/includes/unlimiCore/assets/js/unlimibox-scripts.js', array('jquery', 'jquery-ui-draggable'), UNLIMICOLORS_VERSION, ['in_footer' => true]);

        wp_enqueue_script(UNLIMICOLORS_SLUG.'-customizer-previewer', UNLIMICOLORS_PLUGIN_PATH . '/includes/unlimiCore/assets/js/customizer-preview.js', array( 'customize-preview-widgets', UNLIMICOLORS_SLUG.'box-scripts-js'), UNLIMICOLORS_VERSION, ['in_footer' => true]);
        wp_localize_script(UNLIMICOLORS_SLUG.'-customizer-previewer', 'customizerAction', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( UNLIMICOLORS_NONCE ),
        ));

        /*
        * Here is where you'll want to localize your data for the script. "myCustomData" will be the variable
        * name used within the Previewer when the data is output.
        */
        wp_localize_script(UNLIMICOLORS_SLUG.'-customizer-previewer', 'unlimiCustomData', array(
            'unlimiTarget' => 'data',
            'unlimiValue' => 'data'
        ) );
    }

    public function enqueueScriptsLiveControls() 
    {
        wp_enqueue_script(UNLIMICOLORS_SLUG.'customizer-controls-scripts', UNLIMICOLORS_PLUGIN_PATH . '/includes/unlimiCore/assets/js/customizer-controls.js', array( 'customize-controls' ), UNLIMICOLORS_VERSION, ['in_footer' => true]);
        wp_enqueue_style(UNLIMICOLORS_SLUG.'customize-controls-styles', UNLIMICOLORS_PLUGIN_PATH . '/includes/unlimiCore/assets/css/customizer-controls.css', [], UNLIMICOLORS_VERSION);
    }

    protected function _getSettings()
    {
        $settings = new UNLIMICOLORS_Settings();
        $s = $settings->getCustomizer();

        return $this->_toObject( $s );
    }
}