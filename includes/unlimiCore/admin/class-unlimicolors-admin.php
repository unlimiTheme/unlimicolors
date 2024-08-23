<?php

namespace UnlimiCore\Admin;

use \UnlimiCore\Base\UnlimiColor_Base;

class UnlimiColor_Admin extends UnlimiColor_Base
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
		
		new UnlimiColors_Customizer_Preview();
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
		$html = '<div id="unlimiThmWrapper" class="__unlimithm__box-main-wrapper">';
		$html .= '<div class="__unlimithm__box-main-settings">';
		$html .= '<input id="unlimiThmUseBox" type="hidden" value="'.get_theme_mod('unlimicolor_plugin_use_box').'" class="__unlimithm__box-main-settings-use" />';
		$html .= '</div>';
		$html .= '</div>';

		echo $html;
	}
}
