<?php

/**
 * PepisCMS
 *
 * Simple content management system
 *
 * @package             PepisCMS
 * @author              Piotr Polak
 * @copyright           Copyright (c) 2007-2018, Piotr Polak
 * @license             See license.txt
 * @link                http://www.polak.ro/
 */

namespace Piotrpolak\Pepiscms\Formbuilder\Component;

if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class ValidationRulesTranslator
 *
 * @since 1.0
 */
class ValidationRulesTranslator
{
    /**
     * Translates CI validation rules into JS library validation rules
     *
     * @param array $validation_rules
     * @return string
     */
    public function translateCIValidationRulesToJSValidationEngineRules($validation_rules)
    {
        $validation_rules = trim($validation_rules);
        if (!$validation_rules) {
            return '';
        }

        $rules = array();

        $validation_rules = explode('|', $validation_rules);

        foreach ($validation_rules as $validation_rule) {
            if (strpos($validation_rule, '[')) {
                if (preg_match("/(.*)\[(.*)\]/", $validation_rule, $match)) {
                    $rules[$match[1]] = $match[2];
                }
            } else {
                $rules[$validation_rule] = $validation_rule;
            }
        }

        $extra_css_classes = array();

        foreach ($rules as $rule => $value) {
            switch ($rule) {
                case 'valid_email':
                    $extra_css_classes[] = 'custom[email]';
                    break;
                case 'numeric':
                    $extra_css_classes[] = 'number';
                    break;
                case 'required':
                    $extra_css_classes[] = 'required';
                    break;
                case 'min':
                    $extra_css_classes[] = 'min[' . $value . ']';
                    break;
                case 'max':
                    $extra_css_classes[] = 'max[' . $value . ']';
                    break;
                case 'min_length':
                    $extra_css_classes[] = 'minSize[' . $value . ']';
                    break;
                case 'max_length':
                    $extra_css_classes[] = 'maxSize[' . $value . ']';
                    break;
                case 'exact_length':
                    $extra_css_classes[] = 'minSize[' . $value . ']';
                    $extra_css_classes[] = 'maxSize[' . $value . ']';
                    break;
            }
        }

        return 'validate[' . implode(',', $extra_css_classes) . ']';
    }
}