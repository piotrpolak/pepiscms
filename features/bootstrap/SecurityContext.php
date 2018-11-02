<?php

class SecurityContext extends \Behat\MinkExtension\Context\RawMinkContext
{
    /**
     * @When I navigate to a protected file :arg1
     */
    public function iNavigateToARestrictedFile($arg1)
    {
        $this->getSession()->visit($arg1);
    }

    /**
     * @Then I should get a :arg1 error
     * @Then I should get a :arg1 status code
     */
    public function iShouldGetAError($arg1)
    {
        $this->assertSession()->statusCodeEquals($arg1);
    }

    /**
     * @Then It should be :arg1 content type
     */
    public function itShouldBeContentType($arg1)
    {
        $this->assertSession()->responseHeaderContains('Content-type', $arg1);
    }


    /**
     * @When PepisCMS file :arg1 exists
     */
    public function pepiscmsFileExists($arg1)
    {
        assert(file_exists('./vendor/piotrpolak/pepiscms/' . $arg1));
    }

    /**
     * @When I copy buildin pages module to user space
     */
    public function iCopyBuildinPagesModuleToUserSpace()
    {
        assert(!file_exists('./modules/pages'), './modules/pages should not exist when starting the test');
        system('cp -a ./vendor/piotrpolak/pepiscms/pepiscms/modules/pages/ ./modules');
        assert(file_exists('./modules/pages'), './modules/pages should correctly copied');
    }

    /**
     * @Then I should clean up user space modules from page module
     */
    public function iShouldCleanUpUserSpaceModulesFromPageModule()
    {
        system('rm -rf ./modules/pages');
        assert(!file_exists('./modules/pages'), './modules/pages should not be cleaned up');
    }


    /**
     * @When I create user file
     */
    public function iCreateUserFile()
    {
        assert(!file_exists('application/users/user.png'));
        system('mkdir -p application/users/ && touch application/users/user.png');
        assert(file_exists('application/users/user.png'));
    }

    /**
     * @Then I should clean up user file
     */
    public function iShouldCleanUpUserFile()
    {
        system('rm application/users/user.png');
        assert(!file_exists('application/users/user.png'));
    }

}
