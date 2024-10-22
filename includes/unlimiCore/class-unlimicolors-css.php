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

    public function __construct()
    {

    }

    public function toCSS( UNLIMICOLORS_Structure $structure ): string
    {
        $styles = $structure->getStyles();
        $path = new UNLIMICOLORS_Paths();

        $css = [];
        foreach ( $styles as $style ) {

            $s = new UNLIMICOLORS_ItemStructure( $style );
            $items = $s->getStylesStructure();
            $key_version = $s->keyVersion();

            foreach ( $items as $selector => $item ) {

                $key = $path->keyToCssPath( $s->key() );
                $toStyle = $this->_toStyles( $item );

                if ( !isset( $css[$key_version] ) ) {
                    $css[$key_version] = '';
                }

                $css[$key_version] .= "$key$selector { $toStyle }";
            }
        }

        ksort($css);

        return implode( ' ', $css );
    }

    protected function _toStyles( $items ): string
    {
        $s = '';
        foreach ($items as $k => $v) {
            $s .= "$k: $v->value".(isset($v->important) && $v->important === true ? ' !important' : '').";";
        }
        
        return $s;
    }
}


