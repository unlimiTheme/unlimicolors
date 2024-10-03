<?php

namespace UNLIMICOLORS\Base;

class UNLIMICOLORS_Base
{
	/**
	 * Constructor.
	 */ 
    public function __construct() 
    {}

    protected function _toObject($a, bool $toObject = true)
    {
        if (is_string($a)) {
            $e = $a;
        } else {
            $e = wp_json_encode($a);
        }

        return $toObject ? (object) json_decode($e, !$toObject) : (array) json_decode($e, !$toObject);
    }
}