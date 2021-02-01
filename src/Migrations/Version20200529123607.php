<?php

declare(strict_types=1);

namespace Webgriffe\SyliusBackInStockNotificationPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200529123607 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE webgriffe_back_in_stock_notification (id INT AUTO_INCREMENT NOT NULL, customer_id INT DEFAULT NULL, email VARCHAR(255) NOT NULL, product_variant_code VARCHAR(255) NOT NULL, channel_id INT NOT NULL, locale_code VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, hash VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE webgriffe_back_in_stock_notification');
    }
}
