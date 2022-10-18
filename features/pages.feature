Feature: Pages module

  Background:
    Given I am an authenticated user

  Scenario: Successful module installation in menu only
    When I navigate to page displaying non-installed modules
    And I choose to install "Pages" module
    And I select module "is_displayed_in_menu" mode
    Then the module "Pages" should be displayed in menu
    And the module "Pages" should not be displayed in utilities

  Scenario: Module should be runnable after installing
    When I run module "Pages"
    Then the module "Pages" should be runnable

  Scenario: Page creation
    When I write a new page
    Then I fill "page_title" with "Sample title"
    Then I fill "page_contents" with "<p>Hello World!</p>"
    Then I fill "page_description" with "Sample page description"
    Then I fill "page_keywords" with "keyword1, keyword2"
    And I press "save"
    And I display the list of pages
    Then "sample-title.html" should be displayed in the pages list

  Scenario: Page visit
    When I go to Site's configuration
    And I enable Frontend Support
    When I visit "sample-title.html"
    Then I should see a page displaying "Hello World!"

  Scenario: Page edit with menu attachment
    When I display the list of pages
    When I edit page "sample-title.html"
    Then I fill "page_title" with "Sample title updated"
    Then I fill "page_contents" with "<p>Hello World updated!</p>"
    Then I fill "page_description" with "Sample page description updated"
    Then I fill "page_keywords" with "keyword1, keyword2, updated"
    Then I fill "parent_item_id" with "0"
    Then I fill "item_name" with "Home"
    And I press "save"
    And I display the list of pages
    Then "sample-title.html" should be displayed in the pages list

  Scenario: Updated page visit
    When I visit "sample-title.html"
    Then I should see a page displaying "Hello World updated!"
    Then I should see a menu item "Home" linking to "sample-title.html"

  Scenario: Successful Frontend Support Disabling
    When I go to Site's configuration
    And I disable Frontend Support
    When I visit "sample-title.html"
    Then I should see a page displaying "Welcome to Demonstration"

  Scenario: Successful module uninstalling
    When I navigate to page displaying installed modules
    And I choose to uninstall "Pages" module
    Then the module "Pages" should be not displayed in menu anymore
