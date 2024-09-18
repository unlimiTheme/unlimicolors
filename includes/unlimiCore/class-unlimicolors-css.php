<?php 

namespace UnlimiCore\Core;

use stdClass;

use \UnlimiCore\Base\UnlimiColor_Base;
use \UnlimiCore\Base\UnlimiColor_ItemStructure;
use \UnlimiCore\Base\UnlimiColor_Structure;
use \UnlimiCore\Base\UnlimiColor_Paths;

class UnlimiColors_CSS extends UnlimiColor_Base
{
    protected $settings;

    public function __construct()
    {

    }

    public function toCSS( UnlimiColor_Structure $structure ): string
    {
        $styles = $structure->getStyles();
        $path = new UnlimiColor_Paths();

        $css = [];
        foreach ( $styles as $style ) {

            $s = new UnlimiColor_ItemStructure( $style );
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


