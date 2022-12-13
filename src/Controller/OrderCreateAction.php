<?php

declare(strict_types=1);

namespace Sylius\AdminOrderCreationPlugin\Controller;

use Sylius\AdminOrderCreationPlugin\Factory\OrderFactoryInterface;
use Sylius\AdminOrderCreationPlugin\Form\Type\NewOrderType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final class OrderCreateAction
{
    /** @var OrderFactoryInterface */
    private $orderFactory;

    /** @var FormFactoryInterface */
    private $formFactory;

    /** @var Environment */
    private $twig;

    public function __construct(
        OrderFactoryInterface $orderFactory,
        FormFactoryInterface $formFactory,
        Environment $twig
    ) {
        $this->orderFactory = $orderFactory;
        $this->formFactory = $formFactory;
        $this->twig = $twig;
    }

    public function __invoke(Request $request): Response
    {
        /** @var string $customerId */
        $customerId = $request->attributes->get('customerId');

        /** @var string $channelCode */
        $channelCode = $request->attributes->get('channelCode');

        $order = $this->orderFactory->createForCustomerAndChannel($customerId, $channelCode);

        $form = $this->formFactory->create(NewOrderType::class, $order);
        $form->handleRequest($request);

        return new Response($this->twig->render('@SyliusAdminOrderCreationPlugin/Order/create.html.twig', [
            'form' => $form->createView(),
        ]));
    }
}
