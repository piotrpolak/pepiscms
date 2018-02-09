<?php

class AuthenticationContext extends Behat\MinkExtension\Context\RawMinkContext
{
    /**
     * @When I navigate to login page
     */
    public function iNavigateToLoginPage()
    {
        $this->visitPath("/admin/login");
    }

    /**
     * @When fill the form with valid credential and submit it
     */
    public function fillTheFormWithValidCredentialAndSubmitIt()
    {
        $this->getSession()->getPage()->fillField('user_email', 'piotr@polak.ro');
        $this->getSession()->getPage()->fillField('password', 'demodemo');

        $this->getSession()->getPage()->pressButton('Login');
    }

    /**
     * @Then I should see the dashboard
     */
    public function iShouldSeeTheDashboard()
    {
        $this->assertSession()->pageTextContains('This is the default landing page.');
    }


    /**
     * @When fill the form with invalid credential and submit it
     */
    public function fillTheFormWithInvalidCredentialAndSubmitIt()
    {
        $this->getSession()->getPage()->fillField('user_email', 'piotr@polak.ro');
        $this->getSession()->getPage()->fillField('password', 'incorrect-password');
        $this->getSession()->getPage()->pressButton('Login');
    }

    /**
     * @Then I should see an error message and stay on the same page
     */
    public function iShouldSeeAnErrorMessageAndStayOnTheSamePage()
    {
        $this->assertSession()->elementExists('css', '#user_email');
        $this->assertSession()->elementExists('css', '#password');
        $this->assertSession()->pageTextContains('Incorrect login or password');
    }

    /**
     * @Given I am an authenticated user
     */
    public function iAmAnAuthenticatedUser()
    {
        $this->iNavigateToLoginPage();
        $this->fillTheFormWithValidCredentialAndSubmitIt();
    }

    /**
     * @When I logout
     */
    public function iLogout()
    {
        $this->getSession()->getPage()->clickLink('Logout');
    }

    /**
     * @When I should see a successful logout message
     */
    public function iShouldSeeASuccessfulLogoutMessage()
    {
        $this->assertSession()->pageTextContains('You have successfully logged out.');
    }

    /**
     * @Given I am an unauthenticated user
     */
    public function iAmAnUnauthenticatedUser()
    {
        $this->getSession()->restart();
    }

    /**
     * @When I navigate to modules page
     */
    public function iVisitModulesPage()
    {
        $this->getSession()->visit('/admin/module');
    }

    /**
     * @When I should be redirected to the login form
     */
    public function iShouldBeRedirectedToTheLoginForm()
    {
        $this->assertSession()->elementExists('css', '#user_email');
        $this->assertSession()->elementExists('css', '#password');
    }

    /**
     * @Then Session expired message should be displayed
     */
    public function sessionExpiredMessageShouldBeDisplayed()
    {
        $this->assertSession()->pageTextContains('Session expired');
    }
}
