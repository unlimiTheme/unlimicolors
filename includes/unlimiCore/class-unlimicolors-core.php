<?php 

namespace UNLIMICOLORS\Core;

use UNLIMICOLORS\Base\UNLIMICOLORS_Structure;
use UNLIMICOLORS\admin\UNLIMICOLORS_Admin;

class UNLIMICOLORS_Core
{
    protected $styles;

    public function __construct()
    {
        $this->_init();
        
        add_action( 'init', [$this, 'initAdmin'] );
    }

    protected function _init()
    {
        $this->_loadBasesDependencies();
        $this->_styles();
    }

    public function initAdmin()
    {
        new UNLIMICOLORS_Admin();
    }

    protected function _loadBasesDependencies()
    {
        require_once UNLIMICOLORS_PLUGIN_DIR . 'includes/unlimiCore/base/class-unlimicolors-base.php';
        require_once UNLIMICOLORS_PLUGIN_DIR . 'includes/unlimiCore/base/class-unlimicolors-settings-base.php';
        require_once UNLIMICOLORS_PLUGIN_DIR . 'includes/unlimiCore/base/class-unlimicolors-customize-base.php';
        require_once UNLIMICOLORS_PLUGIN_DIR . 'includes/unlimiCore/base/class-unlimicolors-itemstructure.php';
        require_once UNLIMICOLORS_PLUGIN_DIR . 'includes/unlimiCore/base/class-unlimicolors-element.php';
        require_once UNLIMICOLORS_PLUGIN_DIR . 'includes/unlimiCore/base/class-unlimicolors-paths.php';
        require_once UNLIMICOLORS_PLUGIN_DIR . 'includes/unlimiCore/base/class-unlimicolors-structure.php';
        require_once UNLIMICOLORS_PLUGIN_DIR . 'includes/unlimiCore/base/class-unlimicolors-version-compatibility.php';

        require_once UNLIMICOLORS_PLUGIN_DIR . 'includes/unlimiCore/class-unlimicolors-customize.php';
        require_once UNLIMICOLORS_PLUGIN_DIR . 'includes/unlimiCore/class-unlimicolors-css.php';
        require_once UNLIMICOLORS_PLUGIN_DIR . 'includes/unlimiCore/admin/class-unlimicolors-admin.php';
    }

    public function _styles()
    {
        add_action( 'wp_head', [$this, 'displayCustomCSS'], 99 );
    }

    public function displayCustomCSS() 
    {
        $c = new UNLIMICOLORS_Customize();
        $s = new UNLIMICOLORS_Structure($c->getStructure());
        $css = new UNLIMICOLORS_CSS();

        $styles = $css->toCSS($s);

        if ( $styles ) :
            $type_attr = current_theme_supports( 'html5', 'style' ) ? '' : ' type=text/css';
            $type_id = UNLIMICOLORS_SLUG . '-custom-css';
            ?>
            <style<?php echo esc_attr( $type_attr ) ?> id="<?php echo esc_attr( $type_id ) ?>">
                            
                <?php // Note that esc_html() cannot be used because `div &gt; span` is not interpreted properly. ?> 
                <?php echo esc_html( trim( wp_strip_all_tags( $styles ) ) ); ?>
            </style>
            <?php
        endif;
    }
}

new UNLIMICOLORS_Core();
