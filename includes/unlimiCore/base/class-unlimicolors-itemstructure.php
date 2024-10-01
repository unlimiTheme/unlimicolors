<?php 

namespace UNLIMICOLORS\Base;

use stdClass;

class UNLIMICOLORS_ItemStructure extends UNLIMICOLORS_Base
{
    protected $key;

    protected $keyCodeVersion;

    protected $styles;

    public function __construct($structure = [])
    {
        $structure = $this->_toObject($structure);

        $this->key = $structure->key ?? '';
        $this->keyCodeVersion = $structure->key_version ?? '';
        $this->styles = $this->_toObject($structure->styles ?? []);
    }

    public function getStructure(bool $toObject = true)
    {
        $c = new stdClass();
        $c->key = $this->key;
        $c->key_version = $this->keyCodeVersion;
        $c->styles = $this->styles;

        return $this->_toObject($c, $toObject);
    }

    public function getStylesStructure(bool $toObject = true)
    {
        return $this->_toObject($this->styles, $toObject);
    }

    public function key(): string
    {
        return $this->key;
    }

    public function keyVersion(): string
    {
        return $this->keyCodeVersion;   
    }

    public function add(string $key, string $key_version, ?object $items = null, ?object $default = null): void
    {
        $this->key = $key;
        $this->keyCodeVersion = $key_version;

        foreach ($items as $k => $v) {
            $items->{$k} = new stdClass();
            $items->{$k}->value = $v;
            $items->{$k}->initial = $default->{$k} ?? '';
        }

        $this->styles = $items;
    }

    public function update(object $items): void
    {
        foreach ($items as $k => $v) {

            if (!property_exists($this->styles, $k)) {
                $this->styles->{$k} =$this->_getEmptyStyleItem();
            }

            $this->styles->{$k}->value = $v;
        }
    }

    public function _getEmptyStyleItem(): object
    {
        $empty = new stdClass();
        $empty->value = '';
        $empty->initial = '';

        return $empty;
    }
}