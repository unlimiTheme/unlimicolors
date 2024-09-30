<?php

namespace UnlimiCore\Base;

class UnlimiColors_Customize_base extends UnlimiColors_Base
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