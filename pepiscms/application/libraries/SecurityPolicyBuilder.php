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

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Component responsible for writing SecurityPolicies
 *
 * @since 0.2.4
 */
class SecurityPolicyBuilder
{
    /**
     * Builds a string representing XML security policy
     *
     * @param $module_name
     * @param $policy_entries associative array of type array('controller' => $controller, 'method' => $method, 'entity' => $entity, 'access' => $access)
     * @return string
     */
    public function build($module_name, $policy_entries)
    {
        $allowed_entity_accesses = array('NONE', 'READ', 'WRITE', 'FULL_CONTROL');

        $doc = new DomDocument('1.0', 'UTF-8');

        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = true;
        $security_policy = $doc->createElement('security_policy');
        $security_policy->setAttribute('generated_at', date('Y-m-d H:i:s'));
        $security_policy->setAttribute('version', '1.1');
        $doc->appendChild($security_policy);

        $policy = $doc->createElement('policy');
        if ($module_name) {
            $policy->setAttribute('module', $module_name);
        }
        $policy = $security_policy->appendChild($policy);

        foreach ($policy_entries as $item) {
            $xml_controller = $doc->createElement('controller');
            $xml_controller->setAttribute('name', $item['controller']);
            $xml_controller = $policy->appendChild($xml_controller);

            if (!in_array($item['access'], $allowed_entity_accesses)) {
                continue;
            }

            $xml_method = $doc->createElement('method');
            $xml_method->setAttribute('name', $item['method']);
            $xml_method = $xml_controller->appendChild($xml_method);

            $xml_entity = $doc->createElement('entity');
            $xml_entity->setAttribute('name', $item['entity']);
            $xml_entity->setAttribute('access', $item['access']);
            $xml_method->appendChild($xml_entity);
        }

        return $doc->saveXML();
    }
}
