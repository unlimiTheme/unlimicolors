<?php
/**
 * Customize
 */

namespace UNLIMICOLORS\Core;

use \UNLIMICOLORS\Base\UNLIMICOLORS_Customize_base;

class UNLIMICOLORS_Customize extends UNLIMICOLORS_Customize_base
{
    static $stucture_key = 'unlimicolors_plugin_structure_json';

    static $disabled_key = 'use_functionality';

	/**
	 * Constructor.
	 */ 
    public function __construct() 
    {
        parent::__construct();
    }   

    public function getStructure()
    {
        return $this->_toObject( $this->_getOption( self::$stucture_key ), false );
    }

    public function disabledActionBox()
    {
        return $this->_getOption( 'use_functionality' );
    }

    static function isCustomizePreview()
    {
        return is_customize_preview();
    }
}