<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

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

/**
 * Class CrudDefinitionBuilder
 *
 * @since 1.0.0
 */
class CrudDefinitionBuilder
{
    /**
     * @var CrudFieldDefinitionBuilder[]
     */
    private $fieldBuilders = array();

    /**
     * @var string
     */
    private $moduleName;

    /**
     * @var PEPISCMS_Lang
     */
    private $lang;

    /**
     * @var bool
     */
    private $isWithImplicitTranslations = false;

    private function __construct()
    {

    }

    public static function create()
    {
        return new CrudDefinitionBuilder();
    }

    /**
     * @param $fieldName
     * @return CrudFieldDefinitionBuilder
     */
    public function withField($fieldName)
    {
        $fieldBuilder = new CrudFieldDefinitionBuilder($fieldName, $this);
        $this->fieldBuilders[$fieldName] = $fieldBuilder;
        return $fieldBuilder;
    }

    /**
     * Enables implicit translations
     *
     * @param $moduleName string
     * @param $lang PEPISCMS_Lang
     * @return $this
     */
    public function withImplicitTranslations($moduleName, $lang)
    {
        $this->moduleName = $moduleName;
        $this->lang = $lang;
        $this->isWithImplicitTranslations = true;
        return $this;
    }

    /**
     * @return array
     */
    public function build()
    {
        $output = array();
        foreach ($this->fieldBuilders as $fieldName => $fieldBuilder) {
            if ($this->isWithImplicitTranslations) {
                $fieldBuilder->withImplicitTranslations($this->moduleName, $this->lang);
            }
            $output[$fieldName] = $fieldBuilder->build();
        }

        return $output;
    }
}
