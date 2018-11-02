Feature: Module management

  Scenario: Accessing vendor restricted files
    When I navigate to a protected file "/vendor/autoload.php"
    Then I should get a 404 error

  Scenario: Accessing application restricted files
    When I navigate to a protected file "/application/config/_pepiscms.php"
    Then I should get a 404 error

  Scenario: Accessing application user files
    When I create user file
    When I navigate to a protected file "/application/users/user.png"
    Then I should get a 200 status code
    And It should be "image/png" content type
    And I should clean up user file

  Scenario: Accessing builtin modules public resources
    When PepisCMS file "pepiscms/modules/pages/resources/icon_16.png" exists
    And I navigate to a protected file "/pepiscms/modules/pages/resources/icon_16.png"
    Then I should get a 200 status code
    And It should be "image/png" content type

  Scenario: Accessing builtin modules restricted resources
    When PepisCMS file "pepiscms/modules/pages/resources/sql/pages.sql" exists
    And I navigate to a protected file "/pepiscms/modules/pages/resources/sql/pages.sql"
    Then I should get a 404 status code

  Scenario: Accessing user modules public resources
    When I copy buildin pages module to user space
    And PepisCMS file "modules/pages/resources/icon_16.png" exists
    And I navigate to a protected file "/modules/pages/resources/icon_16.png"
    Then I should get a 200 status code
    And It should be "image/png" content type
    And I should clean up user space modules from page module

  Scenario: Accessing user modules restricted resources
    When I copy buildin pages module to user space
    And PepisCMS file "modules/pages/resources/sql/pages.sql" exists
    And I navigate to a protected file "/modules/pages/resources/sql/pages.sql"
    Then I should get a 404 status code
    And I should clean up user space modules from page module