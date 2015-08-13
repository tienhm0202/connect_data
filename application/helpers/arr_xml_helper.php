<?php

/**
 * @author Le Sy Dat
 * @copyright 2011
 */

class arr2xml
{
	var $array = array();
	var $xml = '';
	var $root_name = '';
	var $charset = '';

	function __construct($array, $charset = 'utf-8', $root_name = 'root')
	{
		$this->array = $array;
		$this->root_name = $root_name;
		$this->charset = $charset;

		if (is_array($array) && count($array) > 0) {
			$this->struct_xml($array);
		} else {
			$this->xml .= "no data";
		}
	}

	function struct_xml($array)
	{
		foreach ($array as $k => $v) {
			if (is_array($v)) {
				$tag = ereg_replace('^[0-9]{1,}', 'item', $k); // replace numeric key in array to 'data'
				$this->xml .= "<$tag>";
				$this->struct_xml($v);
				$this->xml .= "</$tag>";
			} else {
				$tag = ereg_replace('^[0-9]{1,}', 'item', $k); // replace numeric key in array to 'data'
				$this->xml .= "<$tag><![CDATA[$v]]></$tag>";
			}
		}
	}

	function get_xml()
	{
		$header = "<?xml version=\"1.0\" encoding=\"" . $this->charset . "\"?><" . $this->root_name . ">";
		$footer = "</" . $this->root_name . ">";

		return $header . $this->xml . $footer;
	}
}

function simpleXMLToArray($xml, $flattenValues = true, $flattenAttributes = true, $flattenChildren = true, $valueKey = '@value', $attributesKey = '@attributes', $childrenKey = '@children')
{
	$return = array();
	if (!($xml instanceof SimpleXMLElement)) {
		return $return;
	}
	$name = $xml->getName();
	$_value = trim((string)$xml);
	if (strlen($_value) == 0) {
		$_value = null;
	}

	if ($_value !== null) {
		if (!$flattenValues) {
			$return[$valueKey] = $_value;
		} else {
			$return = $_value;
		}
	}

	$children = array();
	$first = true;
	foreach ($xml->children() as $elementName => $child) {
		$value = simpleXMLToArray($child, $flattenValues, $flattenAttributes, $flattenChildren, $valueKey, $attributesKey, $childrenKey);
		if (count($value) == 0)
			$value = '';
		if (isset($children[$elementName])) {
			if ($first) {
				$temp = $children[$elementName];
				unset($children[$elementName]);
				$children[$elementName][] = $temp;
				$first = false;
			}
			$children[$elementName][] = $value;
		} else {
			$children[$elementName] = $value;
		}
	}
	if (count($children) > 0) {
		if (!$flattenChildren) {
			$return[$childrenKey] = $children;
		} else {
			$return = array_merge($return, $children);
		}
	}

	$attributes = array();
	foreach ($xml->attributes() as $name => $value) {
		$attributes[$name] = trim($value);
	}
	if (count($attributes) > 0) {
		if (!$flattenAttributes) {
			$return[$attributesKey] = $attributes;
		} else {
			$return = array_merge($return, $attributes);
		}
	}

	return $return;
}