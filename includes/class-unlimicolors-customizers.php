<?php
/**
 * Change colors in the Customizer.
 */

use UnlimCore\Core\UNLIMICOLORS_Customizer;

/**
 * Customizer class.
 */
class UNLIMICOLORS_Customizers
{
	/**
	 * Constructor.
	 */
	public function __construct() {
		
		new UNLIMICOLORS_Customizer();
	}
}

new UNLIMICOLORS_Customizers();
