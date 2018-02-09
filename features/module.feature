Feature: Module management

  Background:
    Given I am an authenticated user

  Scenario: Successful module installation in menu only
    When I navigate to page displaying non-installed modules
    And I choose to install "Backup" module
    And I select module "is_displayed_in_menu" mode
    Then the module "Backup" should be displayed in menu

  Scenario: Successful module uninstalling
    When I navigate to page displaying installed modules
    And I choose to uninstall "Backup" module
    Then the module "Backup" should be not displayed in menu anymore
