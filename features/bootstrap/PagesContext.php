<?php

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
     * @Then :text should be displayed in the pages list
     */
    public function shouldBeDisplayedInThePagesList($text)
    {
        $moduleItem = $this->getSession()->getPage()->find('xpath', '//a.pages_uriXX[text()="' . $arg1 . '"]');
    }
}