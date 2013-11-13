@javascript
Feature: Product group creation
  In order to manage relations between products
  As a user
  I need to be able to manually create a group

  Background:
    Given I am logged in as "admin"

  Scenario: Successfully display all required fields in the group creation form
    Given I am on the product groups page
    And I create a new product group
    Then I should see the Code and Type fields
    And I should not see the Axis field

  Scenario: Successfully create a cross sell
    Given I am on the product groups page
    When I create a new product group
    And I fill in the following information in the popin:
      | Code | Cross |
    And I select "X_SELL" from "Type"
    And I press the "Save" button
    Then I am on the product groups page
    And I should see group Cross

  Scenario: Fail to create a group missing the code
    Given I am on the product groups page
    When I create a new product group
    And I press the "Save" button
    Then I should see validation error "This value should not be blank."

  Scenario: Fail to create a group filling a non-valid code
    Given I am on the product groups page
    When I create a new product group
    And I fill in the following information in the popin:
      | Code | =( |
    And I press the "Save" button
    Then I should see validation error "Group code may contain only letters, numbers and underscores."

  Scenario: Fail to create a group filling an already used code
    Given the following product groups:
      | code   | label          | type   |
      | TSHIRT | T-Shirt Akeneo | X_SELL |
    And I am on the product groups page
    When I create a new product group
    And I fill in the following information in the popin:
      | Code | TSHIRT |
    And I press the "Save" button
    Then I should see validation error "This value is already used."
