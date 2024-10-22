<?php

namespace UNLIMICOLORS\Admin;

use \UNLIMICOLORS\Base\UNLIMICOLORS_Base;

class UNLIMICOLORS_Admin extends UNLIMICOLORS_Base
{
	/**
	 * Constructor.
	 */ 
    public function __construct() 
    {
		$this->_init();
	}

	public function _init()
    {
		$this->_loadBasesDependencies();

		add_action( 'wp_footer', [$this, 'settingsArea'] );
		
		new UNLIMICOLORS_Customizer_Preview();
    }

    public function _loadBasesDependencies()
    {
		require_once UNLIMICOLORS_PLUGIN_DIR . 'includes/unlimiCore/admin/class-unlimicolors-settings.php';
		require_once UNLIMICOLORS_PLUGIN_DIR . 'includes/unlimiCore/admin/class-unlimicolors-ajax-customizer.php';
		require_once UNLIMICOLORS_PLUGIN_DIR . 'includes/unlimiCore/admin/class-unlimicolors-dynamic-customizer.php';
		
		require_once UNLIMICOLORS_PLUGIN_DIR . 'includes/unlimiCore/admin/class-unlimicolors-box.php';
		require_once UNLIMICOLORS_PLUGIN_DIR . 'includes/unlimiCore/admin/class-unlimicolors-api.php';
		require_once UNLIMICOLORS_PLUGIN_DIR . 'includes/unlimiCore/admin/class-unlimicolors-customizer-preview.php';
    }

	public function settingsArea()
	{
		echo '<div id="unlimiThmWrapper" class="__unlimithm__box-main-wrapper">';
		echo '<div class="__unlimithm__box-main-settings">';
		echo '<input id="unlimiThmUseBox" type="hidden" value="'. esc_attr( get_theme_mod( 'unlimicolor_plugin_use_box' ) ) . '" class="__unlimithm__box-main-settings-use" />';
		echo '</div>';
		echo '</div>';
	}
}
