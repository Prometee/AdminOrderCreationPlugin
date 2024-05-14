@admin_order_creation_managing_orders @ui @javascript
Feature: Placing order after going back from the order preview
    In order to apply changes after previewing the order
    As an Administrator
    I want to be able to apply changes after previewing the order

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Stark Coat" priced at "$100.00"
        And the store ships everywhere for free
        And the store allows paying with "Cash on Delivery"
        And there is a customer account "jon.snow@the-wall.com"
        And I am logged in as an administrator

    Scenario: Placing order after going back from the order preview
        When I create a new order for "jon.snow@the-wall.com" and channel "United States"
        And I add "Stark Coat" to this order
        And I specify this order shipping address as "Ankh-Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I select "Free" shipping method
        And I select "Cash on Delivery" payment method
        And I place this order
        And I go back to the order creation
        And I place and confirm this order
        Then I should be notified that order has been successfully created
        And this order shipping address should be "Jon Snow", "Frost Alley", "90210", "Ankh-Morpork", "United States"
        And this order billing address should be "Jon Snow", "Frost Alley", "90210", "Ankh-Morpork", "United States"
        And this order shipping method should be "Free"
        And this order payment method should be "Cash on Delivery"
        And the product named "Stark Coat" should be in the items list
