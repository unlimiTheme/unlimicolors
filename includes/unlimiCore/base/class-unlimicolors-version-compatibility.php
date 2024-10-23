<?php 

namespace UNLIMICOLORS\Base;

class UNLIMICOLORS_VersionCompatibility
{
    protected $compatibility_options = [
        '1.1.0' => [ // new version
            '1.0.2' => [ // old version
                'admin' => 'doCompatibilityStyleStructure',
                'public' => 'doCompatibilityStyleStructureCSS',
            ],
            '1.0.4' => [ // old version
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

    protected $structure;

    public function __construct( UNLIMICOLORS_Structure $structure, string $new_version, ?string $type = null )
    {
        $this->structure = $structure;
        $this->new_version = $new_version;
        $this->type = $type;

        $this->hydrate();
        $this->_init();
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

                if ( !is_null( $this->type ) && isset( $old_versions_details[$this->type] ) ) {
                    $this->{$old_versions_details[$this->type]}();
                }
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


    protected function doCompatibilityStyleStructure()
    {
        // echo " doCompatibilityStyleStructure ";
    }

    protected function doCompatibilityStyleStructureCSS()
    {
        // echo " doCompatibilityStyleStructureCSS ";
    }
}
