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
        $this->addSql('CREATE TABLE webgriffe_back_in_stock_notification_subscription (id INT AUTO_INCREMENT NOT NULL, customer_id INT DEFAULT NULL, product_variant_id INT NOT NULL, channel_id INT NOT NULL, hash VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, local_code VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_1F046E38D1B862B8 (hash), INDEX IDX_1F046E389395C3F3 (customer_id), INDEX IDX_1F046E38A80EF684 (product_variant_id), INDEX IDX_1F046E3872F5A1AA (channel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE webgriffe_back_in_stock_notification_subscription ADD CONSTRAINT FK_1F046E389395C3F3 FOREIGN KEY (customer_id) REFERENCES sylius_customer (id)');
        $this->addSql('ALTER TABLE webgriffe_back_in_stock_notification_subscription ADD CONSTRAINT FK_1F046E38A80EF684 FOREIGN KEY (product_variant_id) REFERENCES sylius_product_variant (id)');
        $this->addSql('ALTER TABLE webgriffe_back_in_stock_notification_subscription ADD CONSTRAINT FK_1F046E3872F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE webgriffe_back_in_stock_notification_subscription');
    }
}
