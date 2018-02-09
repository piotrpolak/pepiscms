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
    public function iSelectModuleMode($moduleName)
    {
        $this->getSession()->getPage()->checkField($moduleName);
        $this->assertSession()->checkboxChecked($moduleName);
        $this->getSession()->getPage()->pressButton('Save and close');
    }

    /**
     * @Then the module :arg1 should be displayed in menu
     */
    public function theModuleShouldBeDisplayedInMenu($moduleName)
    {
        assert($this->getSession()->getPage()->find('xpath', '//ul/li//*[text()="' . $moduleName . '"]') != null);
    }

    /**
     * @When I navigate to page displaying installed modules
     */
    public function iNavigateToPageDisplayingInstalledModules()
    {
        $this->getSession()->visit('/admin/module');
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
        assert($this->getSession()->getPage()->find('xpath', '//ul/li//*[text()="' . $moduleName . '"]') === null);
    }
}