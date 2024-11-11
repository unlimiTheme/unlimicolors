<?php 

namespace UNLIMICOLORS\Base;

use \UNLIMICOLORS\Base\UNLIMICOLORS_ItemStructure;
use stdClass;

class UNLIMICOLORS_VersionCompatibility
{
    protected $compatibility_options = [
        '1.1.0' => [ // new version
            '1.0.2' => [ // old version
                'admin' => 'doCompatibilityStyleStructure',
                'public' => 'doCompatibilityStyleStructureCSS',
            ]
        ]
    ];

    protected $available_types = [
        'admin',
        'public'
    ];

    protected $type;

    protected $new_version;

    protected UNLIMICOLORS_Structure $structure;

    public function __construct( UNLIMICOLORS_Structure $structure, string $new_version, ?string $type = null )
    {
        $this->structure = $structure;
        $this->new_version = $new_version;
        $this->type = $type;

        $this->hydrate();
        $this->_init();
        $this->_doCompatibilities();
    }

    public function getStructure()
    {
        return $this->structure;
    } 

    protected function _init()
    {
        $old_version = $this->hydrateVersion( $this->structure->getAppVersion() );
        $new_version = $this->hydrateVersion( $this->new_version );

        $versions = [];
        foreach ( $this->compatibility_options as $new_v => $old_versions ) {

            if ( $new_v > $new_version) {
                continue;
            }

            $versions[$new_v] = []; 
            foreach ( $old_versions as $old_v => $old_versions_details ) {

                if ( $old_v > $old_version) {
                   continue;
                }

                $versions[$new_v][$old_v] = $old_versions_details;
            }
        }
    }

    protected function hydrate()
    {
        $result = [];
        foreach ( $this->compatibility_options as $version => $compatibility ) {
            $version = $this->hydrateVersion( $version );

            $result[$version] = [];
            foreach ( $compatibility as $v => $c ) {
                $v = $this->hydrateVersion( $v );
                $result[$version][$v] = $c;
            }

            ksort( $result[$version] );
        }

        $this->compatibility_options = $result;
        ksort( $this->compatibility_options );
    }

    protected function hydrateVersion( $version )
    {
        $version_array = explode( '.', $version );

        $version_array = array_map( function( $v ) {
            return str_pad( $v, 2, '0', STR_PAD_LEFT );
        }, $version_array );

        return implode( '.', $version_array );
    }

    protected function dehydrateVersion( $version )
    {
        $version_array = explode( '.', $version );

        $version_array = array_map( function( $v ) {
            return intval( $v );
        }, $version_array );

        return implode( '.', $version_array );
    }

    protected function _doCompatibilities()
    {
        $type = $this->type;
        if ( !is_null( $this->type ) ) { 
            if ( in_array( $this->type, $this->available_types ) ) {
                $type = [ $this->type ];
            } else {
                $type = [];
            }
        } else {
            $type = [];
        }

        foreach ( $this->compatibility_options as $compatibility ) {    
            foreach ( $compatibility as $c ) {
                foreach ( $type as $t ) {
                    if ( isset( $c[$t] ) ) {
                        $this->{$c[$t]}();
                    }
                }
            }
        }
    }

    protected function doCompatibilityStyleStructure()
    {
        $this->_styleStructure();
        $this->structure->setAppVersion( UNLIMICOLORS_VERSION );
        $this->structure->increaseVersion();
    }

    protected function doCompatibilityStyleStructureCSS()
    {
        $this->_styleStructure();
    }

    protected function _styleStructure()
    {
        $styles = $this->structure->getStyles();

        foreach ( $styles as $k => $s ) {

            $style = new UNLIMICOLORS_ItemStructure( $s );
            $styles_structure = $style->getStylesStructure();

            $result = new stdClass();
            foreach ( $styles_structure as $kk => $ss ) {

                if ( property_exists( $ss, ' ' ) ) {
                    $result->{$kk} = $ss;
                    continue;
                }

                $ss->important = false;
                $result->{$kk} = new stdClass();
                $result->{$kk}->{" "} = $ss;
            }

            $this->structure->update( $style->key(), $style->keyVersion(), $result, false, true );
        }
    }
}
