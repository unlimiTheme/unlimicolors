<?php
/**
 * Change colors in the Customizer.
 */

use UnlimCore\Core\UnlimiColors_Customizer;

/**
 * Customizer class.
 */
class UnlimiColors_Customizers
{
	/**
	 * Constructor.
	 */
	public function __construct() {
		
		new UnlimiColors_Customizer();
	}
}

new UnlimiColors_Customizers();
