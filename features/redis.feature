Feature: Using redis as cache backend
  In order to store data in the cache
  As a library user
  I need to be able to use redis as a backend

  Scenario: Storing data in the cache
    Given The cache is empty
    And I store a an item in the cache
    When I retrieve it
    Then I should get the same item

  Scenario: Storing data for a limited time in the cache
    Given The cache is empty
    And I store a an item in the cache for 1 second
    When I wait 2 seconds
    Then I should not be able to retrieve it

  Scenario: Deleting an item from the cache
    Given The cache is empty
    And I store a an item in the cache
    When I delete the item from the cache
    Then I should not be able to retrieve it

  Scenario: Flushing the full cache
    Given The cache is empty
    And I store a an item in the cache
    When I flush all the items item from the cache
    Then I should not be able to retrieve it

  Scenario: The factory can create a redis connection
    Given I have the connection parameters for redis
    When I request a new redis cache instance to the factory
    Then I should receive a redis cache already connected