<p align="center">
    <a href="https://sylius.com" target="_blank">
        <picture>
          <source media="(prefers-color-scheme: dark)" srcset="https://media.sylius.com/sylius-logo-800-dark.png">
          <source media="(prefers-color-scheme: light)" srcset="https://media.sylius.com/sylius-logo-800.png">
          <img alt="Sylius Logo." src="https://media.sylius.com/sylius-logo-800.png">
        </picture>
    </a>
</p>

<h1 align="center">Admin Order Creation Plugin</h1>

<p align="center"><a href="https://sylius.com/plugins/" target="_blank"><img src="https://sylius.com/assets/badge-official-sylius-plugin.png" width="200"></a></p>

<p align="center">This plugin allows to create an order in admin panel.</p>

![Screenshot showing the order creation page, Shipments&Payments section](docs/screenshot.png)

## Business value

So far it was up to the Customer to place an order using available product variants as well as payment and shipping
methods.

The whole process of placing an order is not that obvious, however. For some reason a Customer may feel a little bit
confused when a promotion is no longer available or shipping method is not eligible for given area. Here comes
Admin Order Creation Plugin.

Briefly speaking, it allows an Administrator to place or reorder an order in the name of a Customer. It helps them solve
even more of Customers' fundamental problems and equips an Administrator with basic tools making creating an 
order possible.

Admin Order Creation Plugin processes are strongly based on standard Order model taken from SyliusCoreBundle.
The only things that differ are order creation context and business requirements. Right now it is up to the Administrator
to provide a channel, locale and currency in which an Order is created. What's more, the Administrator is able to add
a discount for any item or the whole Order, which is, technically speaking, a new type of Sylius Adjustments.

After creating an Order via Admin panel, this new Order is listed like any other order placed via Sylius.

## Installation

#### Beware!

> This installation instruction assumes that you're using Symfony Flex. If you don't, take a look at the
[legacy installation instruction](docs/legacy_installation.md). However, we strongly encourage you to use
Symfony Flex, it's much quicker! :)

1. Require plugin with composer:

    ```bash
    composer require sylius/admin-order-creation-plugin
    ```
    
    > Remember to allow community recipes with `composer config extra.symfony.allow-contrib true` or during plugin installation process

2. Copy Sylius templates overridden in plugin to your templates directory (e.g `templates/bundles/`):

    ```bash
    mkdir -p templates/bundles/SyliusAdminBundle/
    cp -R vendor/sylius/admin-order-creation-plugin/src/Resources/views/SyliusAdminBundle/* templates/bundles/SyliusAdminBundle/
    ```

3. Override repositories

   1. Create repository classes
      ```bash
      mkdir src/Repository
      touch src/Repository/CustomerRepository.php
      touch src/Repository/ProductVariantRepository.php
      ```
   2. Paste the following content to the `src/Repository/CustomerRepository.php`:
      ```php
      <?php
    
      declare(strict_types=1);
    
      namespace App\Repository;
      
      use Sylius\AdminOrderCreationPlugin\Doctrine\ORM\CustomerRepositoryInterface;
      use Sylius\AdminOrderCreationPlugin\Doctrine\ORM\CustomerRepositoryTrait;
      use Sylius\Bundle\CoreBundle\Doctrine\ORM\CustomerRepository as BaseCustomerRepository;
      
      final class CustomerRepository extends BaseCustomerRepository implements CustomerRepositoryInterface
      {
          use CustomerRepositoryTrait;
      }
      ```
   3. Paste the following content to the `src/Repository/ProductVariantRepository.php`:
      ```php
      <?php
    
      declare(strict_types=1);
    
      namespace App\Repository;

      use Sylius\AdminOrderCreationPlugin\Doctrine\ORM\ProductVariantRepositoryInterface;
      use Sylius\AdminOrderCreationPlugin\Doctrine\ORM\ProductVariantRepositoryTrait;
      use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductVariantRepository as BaseProductVariantRepository;
      
      final class ProductVariantRepository extends BaseProductVariantRepository implements ProductVariantRepositoryInterface
      {
          use ProductVariantRepositoryTrait;
      }
      ```
   4. Configure repositories in `config/packages/_sylius.yaml`:
   ```diff
    sylius_customer:
        resources:
            customer:
                classes:
                    model: App\Entity\Customer\Customer
   +                repository: App\Repository\CustomerRepository
   
    sylius_product:
        resources:
            product_variant:
                classes:
                    model: App\Entity\Product\ProductVariant
   +                repository: App\Repository\ProductVariantRepository
   ```
      

## Extension points

Admin Order Creation Plugin makes it possible to add custom discount during order creation - thus some of Order
Show templates need to be replaced with those placed in `Resources/views` package.

Payment link generation and sending process is based on logic placed in the PaymentLinkCreationListener class. Thus, it can
be easily replaced with suitable implementation.

Adjustments set is not closed and strictly defined - adding custom adjustment means defining a new constant in the
AdjustmentType class.

Significant part of Reorder Processing is inspired by official Sylius 
[Customer Reorder Plugin](https://github.com/Sylius/CustomerReorderPlugin/). In case of the need for more processors,
just add new class implementing `ReorderProcessor` interface, declare it in `reorder_processing.xml` file and match
it with a proper tag.

Admin Order Creation process is based on Symfony Forms. To find out more about Symfony Forms extension possibilities, check out
[Symfony Docs](https://symfony.com/doc/current/form/create_form_type_extension.html).   

## Security issues

If you think that you have found a security issue, please do not use the issue tracker and do not post it publicly. 
Instead, all security issues must be sent to `security@sylius.com`.
