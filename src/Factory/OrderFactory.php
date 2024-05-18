<?php

declare(strict_types=1);

namespace Sylius\AdminOrderCreationPlugin\Factory;

use Sylius\AdminOrderCreationPlugin\ReorderProcessing\ReorderProcessor;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Webmozart\Assert\Assert;

final class OrderFactory implements OrderFactoryInterface
{
    /** @var FactoryInterface */
    private $baseOrderFactory;

    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    /** @var ChannelRepositoryInterface */
    private $channelRepository;

    /** @var ReorderProcessor */
    private $reorderProcessor;

    public function __construct(
        FactoryInterface $baseOrderFactory,
        CustomerRepositoryInterface $customerRepository,
        ChannelRepositoryInterface $channelRepository,
        ReorderProcessor $reorderProcessor,
    ) {
        $this->baseOrderFactory = $baseOrderFactory;
        $this->customerRepository = $customerRepository;
        $this->channelRepository = $channelRepository;

        $this->reorderProcessor = $reorderProcessor;
    }

    public function createNew(): OrderInterface
    {
        /** @var OrderInterface $order */
        $order = $this->baseOrderFactory->createNew();
        Assert::isInstanceOf($order, OrderInterface::class);

        return $order;
    }

    public function createForCustomerAndChannel(string $customerId, string $channelCode): OrderInterface
    {
        /** @var CustomerInterface $customer */
        $customer = $this->customerRepository->find($customerId);
        Assert::isInstanceOf($customer, CustomerInterface::class);

        /** @var OrderInterface $order */
        $order = $this->createNew();

        /** @var ChannelInterface $channel */
        $channel = $this->channelRepository->findOneByCode($channelCode);
        Assert::isInstanceOf($channel, ChannelInterface::class);

        $order->setCustomer($customer);
        $order->setChannel($channel);

        /** @var CurrencyInterface $currency */
        $currency = $channel->getBaseCurrency();
        Assert::isInstanceOf($currency, CurrencyInterface::class);
        $order->setCurrencyCode($currency->getCode());

        /** @var LocaleInterface $defaultLocale */
        $defaultLocale = $channel->getDefaultLocale();
        Assert::isInstanceOf($defaultLocale, LocaleInterface::class);
        $order->setLocaleCode($defaultLocale->getCode());

        return $order;
    }

    public function createFromExistingOrder(OrderInterface $order): OrderInterface
    {
        $reorder = $this->createNew();
        Assert::isInstanceOf($reorder, OrderInterface::class);

        $this->reorderProcessor->process($order, $reorder);

        return $reorder;
    }
}
