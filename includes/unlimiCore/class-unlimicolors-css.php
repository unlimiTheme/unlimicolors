<?php 

namespace UnlimiCore\Core;

use stdClass;

use \UnlimiCore\Base\UnlimiColors_Base;
use \UnlimiCore\Base\UnlimiColors_ItemStructure;
use \UnlimiCore\Base\UnlimiColors_Structure;
use \UnlimiCore\Base\UnlimiColors_Paths;

class UnlimiColors_CSS extends UnlimiColors_Base
{
    protected $settings;

    protected $use_important = false;

    public function __construct()
    {}

    public function toCSS( UnlimiColors_Structure $structure ): string
    {
        $styles = $structure->getStyles();
        $path = new UnlimiColors_Paths();

        $css = [];
        foreach ( $styles as $style ) {

            $s = new UnlimiColors_ItemStructure( $style );
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


