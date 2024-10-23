<?php

namespace UNLIMICOLORS\Base;

class UNLIMICOLORS_Customize_base extends UNLIMICOLORS_Base
{
	/**
	 * Constructor.
	 */ 
    public function __construct() 
    {
        parent::__construct();
    }

    public function getOption($id)
    {
        return $this->_getOption($id);
    }

    protected function _getOption($id)
    {
        return get_theme_mod( $id );
    }
}