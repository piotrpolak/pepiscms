Feature: Module management

  Background:
    Given I am an authenticated user

  Scenario: Successful module installation in menu only
    When I navigate to page displaying non-installed modules
    And I choose to install "Backup" module
    And I select module "is_displayed_in_menu" mode
    Then the module "Backup" should be displayed in menu
    And the module "Backup" should not be displayed in utilities

  Scenario: Module should be runnable after installing
    When I run module "Backup"
    Then the module "Backup" should be runnable

  Scenario: Successful module uninstalling
    When I navigate to page displaying installed modules
    And I choose to uninstall "Backup" module
    Then the module "Backup" should be not displayed in menu anymore

  Scenario: Module should not be runnable after uninstalling
    When I hit module's "Backup" run URL
    Then the module "Backup" should not be runnable

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
#    And the module "SQL Console" should be runnable

  Scenario: Executing SQL in SQL Console
    When I run module "SQL Console"
    Then I should not see "items" table in the database table list
    When I fill the "sql_input" field with contents of "sample.sql"
    And I press "execute"
    And I run module "SQL Console"
    Then I should see "items" table in the database table list

  Scenario: Cleaning up SQL in SQL Console
    When I run module "SQL Console"
    Then I should see "items" table in the database table list
    When I fill the "sql_input" field with contents of "sample_cleanup.sql"
    And I press "execute"
    Then I should not see "items" table in the database table list

  Scenario: Successful module uninstalling 2
    When I navigate to page displaying installed modules
    And I choose to uninstall "SQL Console" module
    Then the module "SQL Console" should be not displayed in menu anymore
    And I navigate to page displaying installed modules
    And I choose to uninstall "Development tools" module
    Then the module "Development tools" should be not displayed in menu anymore
