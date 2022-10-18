<?php

// Deprecated: Required parameter $lang follows optional parameter $level in /var/www/html/vendor/piotrpolak/pepiscms/pepiscms/modules/pages/views/admin/index_simple.php on line 3
class PagesContext extends \Behat\MinkExtension\Context\RawMinkContext
{
    /**
     * @When I write a new page
     */
    public function iWriteANewPage()
    {
        $this->visitPath("/admin/module/run/pages/edit/language_code-en/view-simple");
    }

    /**
     * @Then I fill :name with :value
     */
    public function iFillWith($name, $value)
    {
        $this->getSession()->getPage()->fillField($name, $value);
    }

    /**
     * @Then I display the list of pages
     */
    public function iDisplayTheListOfPages()
    {
        $this->visitPath("/admin/module/run/pages/");
    }

    /**
     * @When I edit page :arg1
     */
    public function iEditPage($text)
    {
        $element = $this->getSession()->getPage()->find('xpath', "//a[contains(@href, '$text')]/../../span/a");
        $element->click();
    }

    /**
     * @Then :text should be displayed in the pages list
     */
    public function shouldBeDisplayedInThePagesList($text)
    {
        $this->assertSession()->elementExists('xpath', "//a[contains(@href, '$text')]");
    }

    /**
     * @When I go to Site's configuration
     */
    public function iGoToSitesConfiguration()
    {
        $this->visitPath("/admin/setup");
    }

    /**
     * @When I enable Frontend Support
     */
    public function iEnableFrontendSupport()
    {
        $this->getSession()->getPage()->checkField("cms_enable_frontend");
        $this->getSession()->getPage()->pressButton('Save and close');
    }

    /**
     * @When I disable Frontend Support
     */
    public function iDisableFrontendSupport()
    {
        $this->getSession()->getPage()->uncheckField("cms_enable_frontend");
        $this->getSession()->getPage()->pressButton('Save and close');
    }


    /**
     * @When I visit :arg1
     */
    public function iVisit($arg1)
    {
        $this->visitPath($arg1);
    }

    /**
     * @Then I should see a page displaying :arg1
     */
    public function iShouldSeeAPageDisplaying($arg1)
    {
        $this->assertSession()->statusCodeEquals(200);
        $this->assertSession()->pageTextContains($arg1);
    }

    /**
     * @Then I should see a menu item :label linking to :href
     */
    public function iShouldSeeAMenuItemLinkingTo($label, $href)
    {
        $this->assertSession()
            ->elementExists('xpath', "//a[contains(text(),'$label') and contains(@href, '$href')]");

    }


}