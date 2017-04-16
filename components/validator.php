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
            if ($model->{$attribute} == null) {
                if (array_key_exists('on', $rule)) {
                    if ($model->getScenario() == $rule['on']) {
                        $errors[$attribute] = $attribute . ' could not be empty.';
                    }
                } else
                    $errors[$attribute] = $attribute . ' could not be empty.';
            }
        }

        return $errors;
    }

    public function email($attributes, $rule = null)
    {
        $model = $this->_bean;
        $errors = [];
        foreach ($attributes as $i => $attribute){
            if (filter_var($model->{$attribute}, FILTER_VALIDATE_EMAIL) === false) {
                if (array_key_exists('on', $rule)) {
                    if ($model->getScenario() == $rule['on']) {
                        $errors[$attribute] = $model->{$attribute}.' is not a valid email.';
                    }
                } else
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
                if (array_key_exists('on', $rule)) {
                    if ($model->getScenario() == $rule['on']) {
                        $errors[$attribute] = $model->{$attribute}.' is not a number.';
                    }
                } else
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
            if (array_key_exists('on', $rule)) {
                if ($model->getScenario() != $rule['on']) {
                    unset($errors[$attribute]);
                }
            }
        }

        return $errors;
    }

    public function unique($attributes, $rule)
    {
        $model = $this->_bean;
        $errors = [];
        foreach ($attributes as $i => $attribute){
            $data = $model->findByAttributes([$attribute=>$model->{$attribute}]);
            if ($data instanceof \RedBeanPHP\OODBBean){
                $errors[$attribute] = $attribute.' '.$model->{$attribute}.' already exist.';
            }
        }
        if (array_key_exists('on', $rule)) {
            if ($model->getScenario() != $rule['on']) {
                $errors = [];
            }
        }

        return $errors;
    }
}