Feature: User authentication

  Scenario: Successful user authentication
    When I navigate to login page
    And fill the form with valid credential and submit it
    Then I should see the dashboard

  Scenario: Unsuccessful user authentication
    When I navigate to login page
    And fill the form with invalid credential and submit it
    Then I should see an error message and stay on the same page

  Scenario: Successful logout
    Given I am an authenticated user
    When I logout
    Then I should see a successful logout message

  Scenario: Redirect to login form on non existing session
    Given I am an unauthenticated user
    When I navigate to modules page
    Then I should be redirected to the login form
    And Session expired message should be displayed