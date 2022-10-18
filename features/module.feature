Feature: Module management

  Background:
    Given I am an authenticated user

  Scenario: Successful module installation in menu only
    When I navigate to page displaying non-installed modules
    And I choose to install "Development tools" module
    And I select module "is_displayed_in_menu" mode
    Then the module "Development tools" should be displayed in menu
    And the module "Development tools" should not be displayed in utilities

  Scenario: Module should be runnable after installing
    When I run module "Development tools"
    Then the module "Development tools" should be runnable

  Scenario: Successful module uninstalling
    When I navigate to page displaying installed modules
    And I choose to uninstall "Development tools" module
    Then the module "Development tools" should be not displayed in menu anymore

  Scenario: Successful installation of SQL console and Development Tools
    When I navigate to page displaying non-installed modules
    And I choose to install "SQL Console" module
    And I select module "is_displayed_in_utilities" and "is_displayed_in_menu" mode
    And I navigate to page displaying non-installed modules
    And I choose to install "Development tools" module
    And I select module "is_displayed_in_utilities" and "is_displayed_in_menu" mode
    Then the module "SQL Console" should be displayed in menu
    And the module "SQL Console" should be displayed in utilities
    Then the module "Development tools" should be displayed in menu
    And the module "Development tools" should be displayed in utilities
    And the module "SQL Console" should be runnable

  Scenario: Executing SQL in SQL Console
    When I run module "SQL Console"
    Then I should not see "items" table in the database table list
    When I fill the "sql_input" field with contents of "sample.sql"
    And I press "execute"
    And I run module "SQL Console"
    Then I should see "items" table in the database table list

  Scenario: Building a new CRUD module
    When I run module "Development tools"
    And Click "Build a new module" item from module dashboard
    And Specify "Database table name" field value to "items"
    And I press "submit"
    Then I hit module's "Items" run URL
    And the module "Items" should be runnable

  Scenario: Cleaning up previously build CRUD module
    When I navigate to page displaying installed modules
    And I choose to uninstall "Items" module
    And I physically delete "Items" module from the filesystem
    Then the module "Items" should be not displayed in menu anymore

  Scenario: Cleaning up SQL in SQL Console
    When I run module "SQL Console"
    Then I should see "items" table in the database table list
    When I fill the "sql_input" field with contents of "sample_cleanup.sql"
    And I press "execute"
    Then I should not see "items" table in the database table list

  Scenario: Module should not be runnable after uninstalling
    When I hit module's "Development tools" run URL
    Then the module "Development tools" should not be runnable

  Scenario: Successful module uninstalling 2
    When I navigate to page displaying installed modules
    And I choose to uninstall "SQL Console" module
    Then the module "SQL Console" should be not displayed in menu anymore
    And I navigate to page displaying installed modules
    And I choose to uninstall "Development tools" module
    Then the module "Development tools" should be not displayed in menu anymore
