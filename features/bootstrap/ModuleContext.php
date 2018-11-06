<?php

class ModuleContext extends \Behat\MinkExtension\Context\RawMinkContext
{
    /**
     * @When I navigate to page displaying non-installed modules
     */
    public function iNavigateToPageDisplayingNonInstalledModules()
    {
        $this->getSession()->visit('/admin/module/setup/view-menu');
    }

    /**
     * @When I choose to install :arg1 module
     */
    public function iChooseToInstallModule($moduleName)
    {
        $moduleItem = $this->getSession()->getPage()->find('xpath', '//b[text()="' . $moduleName . '"]');
        assert($moduleItem != null);
        $installLink = $moduleItem->find("xpath", "../..//a");
        assert($installLink != null);
        $installLink->click();
    }

    /**
     * @When I select module :arg1 mode
     */
    public function iSelectModuleMode($mode)
    {
        $this->getSession()->getPage()->checkField($mode);
        $this->getSession()->getPage()->uncheckField('is_displayed_in_utilities');
        $this->assertSession()->checkboxChecked($mode);
        $this->assertSession()->checkboxNotChecked('is_displayed_in_utilities');
        $this->getSession()->getPage()->pressButton('Save and close');
    }

    /**
     * @When I select module :arg1 and :arg2 mode
     */
    public function iSelectModuleAndMode($mode1, $mode2)
    {
        $this->getSession()->getPage()->checkField($mode1);
        $this->assertSession()->checkboxChecked($mode1);
        $this->getSession()->getPage()->checkField($mode2);
        $this->assertSession()->checkboxChecked($mode2);
        $this->getSession()->getPage()->pressButton('Save and close');
    }

    /**
     * @Then the module :arg1 should be displayed in menu
     */
    public function theModuleShouldBeDisplayedInMenu($moduleName)
    {
        $this->assertSession()->elementExists('xpath', $this->getModuleMenuSelector($moduleName));
    }

    /**
     * @When I navigate to page displaying installed modules
     */
    public function iNavigateToPageDisplayingInstalledModules()
    {
        $this->visitPath('/admin/module');
    }

    /**
     * @When I choose to uninstall :arg1 module
     */
    public function iChooseToUninstallModule($moduleName)
    {
        $moduleItem = $this->getSession()->getPage()->find('xpath', '//td//a[text()="' . $moduleName . '"]');
        assert($moduleItem != null);
        $link = $moduleItem->find('xpath', '../../..//a[contains(@href, "uninstall")]');
        assert($link != null);
        $link->click();
    }

    /**
     * @Then the module :arg1 should be not displayed in menu anymore
     */
    public function theModuleShouldBeNotDisplayedInMenuAnymore($moduleName)
    {
        $this->assertSession()->elementNotExists('xpath', $this->getModuleMenuSelector($moduleName));
    }

    /**
     * @When I run module :arg1
     */
    public function iRunModule($moduleName)
    {
        $this->flushCache();
        $path = $this->getModuleMenuSelector($moduleName);
        $this->getSession()->getPage()->getHtml();
        $element = $this->getSession()->getPage()->find('xpath', $path);
        if ($element == null) {
            $this->flushCache();
            $element = $this->getSession()->getPage()->find('xpath', $this->getUtilitiesModuleSelector($moduleName));
        }
        if ($element == null) {
            throw new RuntimeException("Unable to run module $moduleName");
        }
        $element->click();
    }

    /**
     * @When I hit module's :arg1 run URL
     */
    public function iHitModulesRunUrl($moduleName)
    {
        $this->flushCache();
        $this->visitPath('admin/module/run/' . $this->toModuleUrlPath($moduleName));
    }


    /**
     * @Then the module :arg1 should be runnable
     */
    public function theModuleShouldBeRunnable($moduleName)
    {
        $this->flushCache();
        $this->assertSession()->pageTextContains($moduleName);
        $this->assertSession()->statusCodeEquals(200);
    }

    /**
     * @Then the module :arg1 should not be runnable
     */
    public function theModuleShouldNotBeRunnable($moduleName)
    {
        $this->assertSession()->pageTextNotContains($moduleName);
        $this->assertSession()->pageTextContains('404');
        $this->assertSession()->statusCodeEquals(404);
    }

    /**
     * @Then the module :arg1 should not be displayed in utilities
     */
    public function theModuleShouldNotBeDisplayedInUtilities($moduleName)
    {
        $this->getSession()->visit('/admin/utilities');
        $this->assertSession()->elementNotExists('xpath', $this->getUtilitiesModuleSelector($moduleName));
    }

    /**
     * @Then the module :arg1 should be displayed in utilities
     */
    public function theModuleShouldBeDisplayedInUtilities($moduleName)
    {
        $this->getSession()->visit('/admin/utilities');
        $this->assertSession()->elementExists('xpath', $this->getUtilitiesModuleSelector($moduleName));
    }

    /**
     * @When I navigate to :arg1 module from menu
     */
    public function iNavigateToModuleFromMenu($moduleName)
    {
        $this->visitPath('/admin/utilities');
        $this->getSession()->getPage()->clickLink($moduleName);
    }

    /**
     * @When I fill the :arg1 field with contents of :arg2
     */
    public function iFillTheFieldWithContentsOf($textareaId, $relativePath)
    {
        $this->assertSession()->pageTextContains('SQL Console v1.');
        $path = './vendor/piotrpolak/pepiscms/docs/sql/' . $relativePath;

        if (!file_exists($path)) {
            throw new RuntimeException('File ' . $path . ' does not exist on page ' . $this->getSession()->getCurrentUrl());
        }

        $contents = file_get_contents($path);
        $this->assertSession()->elementExists('xpath', $this->textareaSelector($textareaId));
        $this->getSession()->getPage()->find('xpath', $this->textareaSelector($textareaId))->setValue($contents);
    }

    /**
     * @When I press :arg1
     */
    public function iPress($arg1)
    {
        $this->assertSession()->elementExists('xpath', $this->submitSelector());
        $element = $this->getSession()->getPage()->find('xpath', $this->submitSelector());
        if (!$element) {
            throw new RuntimeException('Button ' . $arg1 . ' does not exists!');
        }
        $element->press();
    }

    /**
     * @Then I should not see :arg1 table in the database table list
     */
    public function iShouldNotSeeTableInTheDatabaseTableList($moduleName)
    {
        $this->assertSession()->elementNotExists('xpath', $this->tableSelector($moduleName));
    }

    /**
     * @Then I should see :arg1 table in the database table list
     */
    public function iShouldSeeTableInTheDatabaseTableList($moduleName)
    {
        $this->assertSession()->elementExists('xpath', $this->tableSelector($moduleName));
    }

    /**
     * @When Click :arg1 item from module dashboard
     */
    public function clickItemFromModuleDashboard($moduleName)
    {
        $element = $this->getSession()->getPage()->find('xpath', $this->getUtilitiesModuleSelector($moduleName));
        $element->click();
    }

    /**
     * @When Specify :name field value to :value
     */
    public function specifyFieldValueTo($name, $value)
    {
        $this->getSession()->getPage()->fillField($name, $value);
    }

    /**
     * @When I physically delete :ignored module from the filesystem
     */
    public function iPhysicallyDeleteModuleFromTheFilesystem($ignored)
    {
        system('rm -rf ./modules/items');
        assert(!file_exists('./modules/items'), './modules/items should not be cleaned up');
    }

    /**
     * @param $moduleName
     * @return string
     */
    private function toModuleUrlPath($moduleName)
    {
        return trim(strtolower(str_replace(' ', '', $moduleName)));
    }

    /**
     * @param $moduleName
     * @return string
     */
    private function getModuleMenuSelector($moduleName)
    {
        return '//*[@id="primary_navigation"]//ul/li//span[text()="' . $moduleName . '"]/..';
    }

    /**
     * @param $moduleName
     * @return string
     */
    private function getUtilitiesModuleSelector($moduleName)
    {
        return '//*[@id="content"]//ul/li/a[normalize-space()="' . $moduleName . '"]';
    }

    private function flushCache()
    {
        $this->visitPath('/admin/utilities/flush_system_cache');
    }

    /**
     * @param $id
     * @return string
     */
    private function textareaSelector($id)
    {
        return '//textarea[@id="' . $id . '"]';
    }

    /**
     * @return string
     */
    private function submitSelector()
    {
        return '//input[@type="submit"]';
    }

    /**
     * @param $tableName
     * @return string
     */
    private function tableSelector($tableName)
    {
        return '//li[@class="has_items"]/a[text()="' . $tableName . '"]';
    }

}