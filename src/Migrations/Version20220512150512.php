<?php

declare(strict_types=1);

namespace Webgriffe\SyliusBackInStockNotificationPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210419151725 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Webgriffe Back in Stock Notification Plugin';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE webgriffe_back_in_stock_notification_subscription ADD notify TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE webgriffe_back_in_stock_notification_subscription DROP notify');
    }
}
