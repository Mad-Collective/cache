Feature: Using a taggable as cache backend
  In order to flush entries whose key I do not know
  As a library user
  I need to be able to tag them so that they can be flushed by tag name

  Background: The cache is empty

  Scenario: I do not retrieve global data inside tagged backend
    Given I store an item in the cache
    When I create a tag
    Then I should not get the same item

  Scenario: I can store and retrieve from tag
    Given I create a tag
    And I store an item in the cache
    When I retrieve it
    Then I should get the same item

  Scenario: I can delete a tagged key
    Given I create a tag
    And I store an item in the cache
    When I delete the item from the cache
    Then I should not be able to retrieve it

  Scenario: I do not flush non tagged keys
    Given I store an item with key "nontag" and value "nontagValue" in the cache
    And I create a tag
    And I store an item in the cache
    When I flush all the items from the cache
    And I discard the tag
    And I retrieve key "nontag"
    Then I should get as value "nontagValue"

  Scenario: I can flush tagged keys
    Given I flush all the items from the cache
    And I create a tag
    And I store an item in the cache
    When I flush all the items from the cache
    Then I should not be able to retrieve it
    And redis should contain 1 item