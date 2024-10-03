<?php 

namespace UNLIMICOLORS\Core;

use stdClass;

use \UNLIMICOLORS\Base\UNLIMICOLORS_Base;
use \UNLIMICOLORS\Base\UNLIMICOLORS_ItemStructure;
use \UNLIMICOLORS\Base\UNLIMICOLORS_Structure;
use \UNLIMICOLORS\Base\UNLIMICOLORS_Paths;

class UNLIMICOLORS_CSS extends UNLIMICOLORS_Base
{
    protected $settings;

    protected $use_important = false;

    public function __construct()
    {}

    public function toCSS( UNLIMICOLORS_Structure $structure ): string
    {
        $styles = $structure->getStyles();
        $path = new UNLIMICOLORS_Paths();

        $css = [];
        foreach ( $styles as $style ) {

            $s = new UNLIMICOLORS_ItemStructure( $style );
            $items = $s->getStylesStructure();
            $key_version = $s->keyVersion();

            $key = $path->keyToCssPath( $s->key() );
            $toStyle = $this->_toStyles( $items );

            if ( !isset( $css[$key_version] ) ) {
                $css[$key_version] = '';
            }

            $css[$key_version] .= "$key { $toStyle }";
        }

        ksort($css);

        return implode( ' ', $css );
    }

    protected function _toStyles( $items ): string
    {
        $s = '';
        foreach ($items as $k => $v) {
            $s .= "$k: $v->value".($this->use_important ? ' !important' : '').";";
        }
        
        return $s;
    }
}


