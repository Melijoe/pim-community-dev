@javascript
Feature: Filter products
  In order to filter products in the catalog
  As a user
  I need to be able to filter products in the catalog

  Background:
    Given the "default" catalog configuration
    And the following family:
      | code      |
      | furniture |
      | library   |
    And the following products:
      | sku    | family    | enabled |
      | postit | furniture | yes     |
      | book   | library   | no      |
    And a "postit" product
    And a "book" product
    And a "book2" product
    And a "ebook" product
    And the following attributes:
      | label       | required | translatable | scopable | useable as grid filter |
      | name        | no       | yes          | no       | yes                    |
      | image       | no       | no           | yes      | yes                    |
      | description | no       | yes          | yes      | yes                    |
    And the following product values:
      | product | attribute   | locale | scope     | value                     |
      | postit  | SKU         |        |           | postit                    |
      | postit  | name        | en_US  |           | Post it                   |
      | postit  | name        | fr_FR  |           | Etiquette                 |
      | postit  | image       |        | ecommerce | large.jpeg                |
      | postit  | image       |        | mobile    | small.jpeg                |
      | postit  | description | en_US  | ecommerce | My ecommerce description  |
      | postit  | description | en_US  | mobile    | My mobile description     |
      | postit  | description | fr_FR  | ecommerce | Ma description ecommerce  |
      | postit  | description | fr_FR  | mobile    | Ma description mobile     |
      | book    | SKU         |        |           | book                      |
      | book    | name        | en_US  |           | Book                      |
      | book    | name        | fr_FR  |           | Livre                     |
      | book    | image       |        | ecommerce | book_large.jpeg           |
      | book    | image       |        | mobile    | book_small.jpeg           |
      | book    | description | en_US  | ecommerce | My ecommerce book descr   |
      | book    | description | en_US  | mobile    | My mobile book descr      |
      | book    | description | fr_FR  | ecommerce | Ma descr livre ecommerce  |
      | book    | description | fr_FR  | mobile    | Ma descr livre mobile     |
      | book2   | SKU         |        |           | book2                     |
      | book2   | name        | en_US  |           | Book2                     |
      | book2   | name        | fr_FR  |           | Livre2                    |
      | book2   | image       |        | ecommerce | book2_large.jpeg          |
      | book2   | image       |        | mobile    | book2_small.jpeg          |
      | book2   | description | en_US  | ecommerce | My ecommerce book2 descr  |
      | book2   | description | en_US  | mobile    | My mobile book2 descr     |
      | book2   | description | fr_FR  | ecommerce | Ma descr livre2 ecommerce |
      | book2   | description | fr_FR  | mobile    | Ma descr livre2 mobile    |
      | ebook   | SKU         |        |           | ebook                     |
      | ebook   | name        | en_US  |           | eBook                     |
      | ebook   | name        | fr_FR  |           | Ebook                     |
      | ebook   | description | en_US  | ecommerce | My ecommerce ebook descr  |
      | ebook   | description | en_US  | mobile    | My mobile ebook descr     |
      | ebook   | description | fr_FR  | ecommerce | Ma descr ebook ecommerce  |
      | ebook   | description | fr_FR  | mobile    | Ma descr ebook mobile     |
    And I am logged in as "admin"

  Scenario: Successfully filter products
    Given I am on the products page
    Then the grid should contain 4 elements
    And I should see products postit and book and book2 and ebook
    And I should be able to use the following filters:
      | filter      | value                 | result                  |
      | SKU         | book                  | book, ebook and book2   |
      | Name        | post                  | postit                  |
      | Description | book                  | book, ebook and book2   |
      | Enabled     | yes                   | postit, ebook and book2 |
      | Enabled     | no                    | book                    |
      | SKU         | contains book         | book, book2 and ebook   |
      | SKU         | does not contain book | postit                  |
      | SKU         | starts with boo       | book and book2          |
      | SKU         | is equal to book      | book                    |
      | SKU         | ends with book        | book and ebook          |

  Scenario: Successfully hide/show filters
    Given I am on the products page
    Then I should see the filters SKU, Family and Enabled
    Then I should not see the filters Name, Image, Description
    When I show the filter "Name"
    And I show the filter "Description"
    And I hide the filter "SKU"
    Then I should see the filters Name, Description, Family and Enabled
    And I should not see the filters Image, SKU

  Scenario: Successfully reset the filters
    Given I am on the products page
    Then I filter by "Enabled" with value "yes"
    And the grid should contain 3 elements
    When I reset the grid
    Then the grid should contain 4 elements

  Scenario: Successfully refresh the grid
    Given I am on the products page
    Then I filter by "Enabled" with value "yes"
    And the grid should contain 3 elements
    When I refresh the grid
    Then the grid should contain 3 elements
