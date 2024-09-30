<?php

namespace UnlimiCore\Admin;

use \UnlimiCore\Base\UnlimiColors_Base;

class UnlimiColors_Admin extends UnlimiColors_Base
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
}
