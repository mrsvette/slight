<?php

namespace Components;

class Validator
{
    protected $_bean;

    public function __construct($bean)
    {
        $this->_bean = $bean;
    }

    public function execute($rule)
    {
        $attr_data = preg_replace('/\s+/', '', $rule[0]);
        $attrs = explode(",", $attr_data);

        return self::{$rule[1]}($attrs, $rule);
    }

    public function required($attributes, $rule = null)
    {
        $model = $this->_bean;
        $errors = [];
        foreach ($attributes as $i => $attribute){
            if ($model->{$attribute} == null)
                $errors[$attribute] = $attribute.' could not be empty.';
        }

        return $errors;
    }

    public function email($attributes, $rule = null)
    {
        $model = $this->_bean;
        $errors = [];
        foreach ($attributes as $i => $attribute){
            if (filter_var($model->{$attribute}, FILTER_VALIDATE_EMAIL) === false) {
                $errors[$attribute] = $model->{$attribute}.' is not a valid email.';
            }
        }

        return $errors;
    }

    public function numerical($attributes, $rule = null)
    {
        $model = $this->_bean;
        $errors = [];
        foreach ($attributes as $i => $attribute){
            if (!is_numeric($model->{$attribute})) {
                $errors[$attribute] = $model->{$attribute}.' is not a number.';
            } else {
                if (array_key_exists('integerOnly', $rule) && $rule['integerOnly']) {
                    if (is_int($model->{$attribute}))
                        $errors[$attribute] = $model->{$attribute}.' is not an integer.';
                }
            }
        }

        return $errors;
    }

    public function length($attributes, $rule = null)
    {
        $model = $this->_bean;
        $errors = [];
        foreach ($attributes as $i => $attribute){
            if (array_key_exists('max', $rule)) {
                if (strlen($model->{$attribute}) > $rule['max']) {
                    $errors[$attribute] = 'Maximum character for '.$model->{$attribute}.' is '.$rule['max'].'.';
                }
            }
            if (array_key_exists('min', $rule)) {
                if (strlen($model->{$attribute}) < $rule['min']) {
                    $errors[$attribute] = 'Minimum character for '.$model->{$attribute}.' is '.$rule['min'].'.';
                }
            }
        }

        return $errors;
    }
}