<?php 

namespace UNLIMICOLORS\Admin;

use \UNLIMICOLORS\Base\UNLIMICOLORS_Base;
use \UNLIMICOLORS\Base\UNLIMICOLORS_Paths;

class UNLIMICOLORS_Box extends UNLIMICOLORS_Base
{
    protected $key;

    protected $cssPath;

    protected $structure;

    protected $defaultValues = [];

    protected $keyVersion;

    protected $exists = false;

    public function __construct($structure, string $key, string $cssPath, $defaultValues=[], string $keyVersion=null, bool $exists=false)
    {
        $this->structure = $this->_toObject($structure, false);
        $this->key = $key;
        $this->cssPath = $cssPath;
        $this->defaultValues = $this->_toObject($defaultValues, false);
        $this->keyVersion = $keyVersion;
        $this->exists = $exists;
    }

    public function get()
    {
        return $this->_doHtml();
    }

    protected function _doHtml()
    {
        $htmlBefore = '';
        $htmlAfter = '';

        foreach ($this->structure as $k => $v) {
            $func = '_get' . ucfirst($k);
            $r = $this->{$func}();
            $htmlBefore .= $r['before'] ?? '';
            $htmlAfter = ($r['after'] ?? '') . $htmlAfter;
        }

        return $htmlBefore . $htmlAfter;

    }

    protected function _getTitle()
    {
        return 'UNLIMICOLORS';
    }

    protected function _getWrapper()
    {
        $before = '<div id="__unlimithm__stylebox" class="__unlimithm__box"><div class="__unlimithm__wrapper">';
		$before .= '<input type="hidden" name="key" value="' . $this->key . '" class="__unlimithm__key" id="__unlimithm__key">';
        $before .= '<input type="hidden" name="key_version" value="' . $this->keyVersion . '" class="__unlimithm__key" id="__unlimithm__keyversion">';
        
        $after = '</div>';

        return [
            'before' => $before,
            'after' => $after
        ];
    }

    protected function _getHeader()
    {
        $before = '<div class="__unlimithm__title-wrapper">';
        $before .= '<a class="__unlimithm__movebox">';
        $before .= '<span class="__unlimithm__moveicon"><span class="gg-maximize-alt"></span></span>';
        $before .= '<span class="__unlimithm__title">' . $this->_getTitle() . '</span>';
        $before .= '</a>';
		$before .= '<a class="__unlimithm__closebox"><span class="gg-close"></span></a>';
		$before .= '</div>';
        
        return ['before' => $before];
    }

    protected function _getHeaderTypes()
    {
        $path = new UNLIMICOLORS_Paths();
        $key_versions = $path->getKeyVersionsInfo();

        $before = '<div class="__unlimithm__types">';

        foreach ( $key_versions as $key_version => $info ) {

            $selected = $key_version == $this->keyVersion ? ' checked' : '';
            $disabled = $this->exists && $key_version < $this->keyVersion ? ' disabled' : '';

            $before .= '<div class="__unlimithm__keytype-item-wrap">';
            $before .= '<input type="radio" id="keytype'.$key_version .'" name="keytypes" value="'.$key_version.'"' . $selected . $disabled . '>';
            $before .= '<label for="keytype'.$key_version .'">'.$info[2].'</label>';
            $before .= '</div>';
        }

        $before .= '</div>';
        
        return ['before' => $before];
    }

    protected function _getFooter()
    {
		$before = '<div class="__unlimithm__bottom">';
		$before .= '<button class="__unlimithm__bottom_button __unlimithm__save" title="Save changes">Apply</button>';
		$before .= '<button class="__unlimithm__bottom_button __unlimithm__cancel" title="Cancel the changes for this element">Cancel</button>';
        $before .= '<button class="__unlimithm__bottom_button __unlimithm__reset" data-confirm="Remove the styles added for this element?" title="Remove styles for this element">Reset</button>';
        $before .= '<button class="__unlimithm__bottom_button __unlimithm__resetall" data-confirm="Remove styles for all elements?" title="Remove styles for all elements">Reset all</button>';
		$before .= '</div>';

        return ['before' => $before];
    }

    protected function _getPanels()
    {
        $panels = $this->_getPanelsStructure();
        $wrapper = $this->_doPanelsWrapper();

        $html = '';
        foreach ($panels as $panel) {
            $html .= $this->_doPanel($panel);
        }
    
        $html = $wrapper['before'] . $html . $wrapper['after'];

        return [
            'before' => $html
        ];
    }

    protected function _doPanelsWrapper()
    {
        $before = '<div class="__unlimithm__pannels-wrapper">';
        
        $after = '</div>';

        return [
            'before' => $before,
            'after' => $after
        ];
    }

    protected function _doPanel(array $panel)
    {
        $wrapper = $this->_doPanelWrapper();
        $title = $this->_doPanelTitle($panel['title']);
        $content = $this->_doPanelContent($panel['content']);

        $html = $wrapper['before'] . $title . $content . $wrapper['after'];

        return $html;
    }

    protected function _doPanelWrapper()
    {
        $before = '<div class="__unlimithm__pannel">';

        $after = '</div>';

        return [
            'before' => $before,
            'after' => $after
        ];
    }

    protected function _doPanelTitle(string $title)
    {
        $title = '<div class="__unlimithm__pannel-title">' . $title . '</div>';
        
        return $title;
    }

    protected function _doPanelContent(array $content)
    {
        $before = '<div class="__unlimithm__pannel-content">';
        $after = '</div>';

        $html = '';
        foreach ($content as $item) {       
            $html .= $this->_doItem($item);
        }

        return $before . $html . $after;
    }

    protected function _doItem(array $item)
    {
        $html = '<p class="__unlimithm__pannel-item-wrap">';

        $item[ 'value' ] = @$this->defaultValues[ $item[ 'data-type' ] ][ 'value' ];
        
        $id = $this->_generateId( $item );
        $item[ 'id' ] = $id;

        $html .= '<label for="'. $item[ 'id' ] .'">' . $item[ 'label' ] . '</label>';
        $html .= $this->_doItemByType( $item[ 'tag' ], $item );
        $html .= '</p>';

        return $html;
    }

    protected function _doItemByType(string $type, array $content)
    {
        switch ($type) {
            case 'input':
                return $this->_doInput($content);
            break;
        }

        return '';
    }

    protected function _doInput(array $attr)
    {
        $attr = $this->_filterAttrs($attr);

        $html = '<input';

        foreach ($attr as $k => $v) {
            $html .= ' '.$k.'="'.$v.'"';
        }

        $hasValue = !empty( $attr['value'] );
        $html .= 'class="__unlimithm__action' . ($hasValue ? ' changed' : '') . '">';

        return  $html;
    }

    protected function _filterAttrs(array $attr)
    {
        $ignore = [ 'tag', 'label', 'class' ];

        return array_diff_key( $attr, array_flip( $ignore ) );
    }

    protected function _generateId($item)
    {
        return '__unlimithm__'.$item['tag'] . ucfirst($item['data-origin']) . ucfirst($item['data-type']);
    }


    protected function _getPanelsStructure()
    {
        return $this->structure['panels'];
    }

    protected function _getPanelStructure(int $index)
    {
        return $this->_getPanelsStructure()[$index];
    }

    protected function _getPanelItems(int $index)
    {
        return $this->_getPanelStructure($index)['content'];
    }
}