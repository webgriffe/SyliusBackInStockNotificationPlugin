# UPGRADE FROM `2.0.1` TO `3.0.0`

## BC Breaks

- [BC] CHANGED: The parameter $backInStockNotificationRepository of Webgriffe\SyliusBackInStockNotificationPlugin\Command\AlertCommand#__construct() changed from Sylius\Component\Resource\Repository\RepositoryInterface to a non-contravariant Webgriffe\SyliusBackInStockNotificationPlugin\Repository\SubscriptionRepositoryInterface
- [BC] CHANGED: The parameter $backInStockNotificationRepository of Webgriffe\SyliusBackInStockNotificationPlugin\Controller\SubscriptionController#__construct() changed from Sylius\Component\Resource\Repository\RepositoryInterface to a non-contravariant Webgriffe\SyliusBackInStockNotificationPlugin\Repository\SubscriptionRepositoryInterface
