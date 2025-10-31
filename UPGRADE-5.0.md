# UPGRADE 4.x TO 5.0

## Version Support

- Removed support for Sylius 1.12
- Added support for Sylius 2.0 and 2.1
- PHP 8.2+ required
- Symfony 6.4+ or 7.3+ required

## Removed Classes

- `src/Twig/AvailabilityExtension.php`
- `src/Twig/AvailabilityRuntime.php`

## Removed Templates

- `templates/_javascript.html.twig`
- `templates/_addSubscription.html.twig`
- `templates/productSubscriptionForm.html.twig`
- `templates/_configurableButton.html.twig`

## New Classes

- `src/Twig/Component/AddNotificationComponent.php` - LiveComponent replacing old Twig extension
- `src/Factory/SubscriptionFactory.php`
- `src/Processor/SubscriptionProcessor.php`

## Changed Classes

### SubscriptionController

```diff
  namespace Webgriffe\SyliusBackInStockNotificationPlugin\Controller;

  final class SubscriptionController extends AbstractController
  {
      public function __construct(
-         private ChannelContextInterface $channelContext,
          private TranslatorInterface $translator,
-         private CustomerContextInterface $customerContext,
-         private AvailabilityCheckerInterface $availabilityChecker,
-         private ProductVariantRepositoryInterface $productVariantRepository,
-         private SenderInterface $sender,
-         private LocaleContextInterface $localeContext,
          private SubscriptionRepositoryInterface $backInStockNotificationRepository,
-         private FactoryInterface $backInStockNotificationFactory,
      ) {
      }
```

### AlertCommand

```diff
  namespace Webgriffe\SyliusBackInStockNotificationPlugin\Command;

- use Symfony\Component\Console\Command\Command;

+ use Symfony\Component\Console\Attribute\AsCommand;
  use Symfony\Component\Console\Command\Command;

- final class AlertCommand extends Command
+ #[AsCommand(
+     name: 'webgriffe:back-in-stock-notification:alert',
+     description: 'Send an email to the user if the product is returned in stock',
+ )]
+ final class AlertCommand extends Command
  {
-     protected function configure(): void
-     {
-         $this
-             ->setName('webgriffe:back-in-stock-notification:alert')
-             ->setDescription('Send an email to the user if the product is returned in stock');
-     }
  }
```

## Asset Changes

JavaScript file `assets/shop/back-in-stock-notification.js` has been removed. The plugin now uses LiveComponent architecture where form handling and interactions are managed directly by the `AddNotificationComponent` instead of external JavaScript files.
