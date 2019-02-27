<?php
/**
 * Created by FsFlex.
 * User: VH
 * Date: 6/26/2017
 * Time: 2:07 PM
 */

namespace Drupal\revslider\Helper;


class RevSliderOptions
{
    protected static $options;
    protected static $invalid;

    protected static $_table;

    protected static function init()
    {
        if (is_array(self::$options))
            return true;
        self::$_table = RevSliderDB::TABLE_OPTIONS;
        $result = RevSliderDB::table(self::$_table)
            ->where('autoload', 'yes')
            ->get();
        $options = array();
        foreach ($result as $row) {
            if(is_object($row))
                self::loadValue($options, $row->name, $row->value);
            elseif (is_array($row))
                self::loadValue($options, $row['name'], $row['value']);
        }
        self::$options = $options;
        self::$invalid = array();
        return true;
    }

    public static function getOptions($key,$default = false)
    {
        self::init();
        if(is_string($key))
            return self::getOption($key,$default);
        if(is_array($key))
        {
            $result = array();
            $option_keys = array_keys($key);
            foreach ($option_keys as $option_key)
            {
                $result[$option_key] = self::getOption($option_key,$key[$option_key]);
            }
            return $result;
        }
        return false;
    }
    protected static function getOption($key,$default = false)
    {
        self::init();
        $options = self::$options;
        if (key_exists($key, $options))
            return $options[$key];
        $checked = self::$invalid;
        if (in_array($key, $checked))
            return $default;
        $result = RevSliderDB::table(self::$_table)
            ->where('name', $key)
            ->limit(1)
            ->first();
        if (empty($result)) {
            self::$invalid[] = $key;
            return $default;
        }
        self::loadValue($options, $key, $result['value']);
        self::$options = $options;
        return $options[$key];
    }
    public static function updateOption($key, $value, $autoload = true)
    {
        $old = self::getOptions($key,null);
        $autoload = ($autoload === true || $autoload === 'yes' || $autoload === 'on' || $autoload === 1) ? true : false ;
        if(is_null($old))
            return self::createOption($key,$value,$autoload);
        else
        {
            return self::updateOptionDB($key,$value,$autoload);
        }
    }

    protected static function loadValue(&$options, $key, $value)
    {
        $options[$key] = ($val = @unserialize($value)) ? $val : $value;
    }
    protected static function validateValue($value)
    {
        if(is_null($value))
            return '';
        if(is_array($value) || is_object($value))
            return serialize($value);
        if(is_string($value) || is_numeric($value))
            return $value.'';
        //other
        return '';
    }
    protected static function createOption($key, $value, $autoload = true)
    {
        self::init();
        $autoload = ($autoload === true || $autoload === 'yes' || $autoload === 'on' || $autoload === 1) ? true : false ;
        $value = self::validateValue($value);
        $db = RevSliderDB::table(self::$_table);
        $result = $db->insert(array(
            'name'     => $key,
            'value'    => $value,
            'autoload' => ($autoload === true) ? 'yes' : 'no'
        ));
        if($result === false)
            return false;
        self::loadValue(self::$options,$key,$value);
        $index = array_search($key,self::$invalid);
        if($index !== false)
            unset(self::$invalid[$index]);
        return self::getOptions($key);
    }
    protected static function updateOptionDB($key,$value,$autoload = true)
    {
        self::init();
        $value = self::validateValue($value);
        $db = RevSliderDB::table(self::$_table);
        $result = $db->where('name',$key)
            ->update(array(
                'value'=>$value,
                'autoload'=>($autoload === true) ? 'yes' : 'no'
            ));
        if($result == 1)
        {
            self::loadValue(self::$options,$key,$value);
            return self::getOptions($key);
        }
    }
}