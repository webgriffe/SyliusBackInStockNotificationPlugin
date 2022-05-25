<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220523152238 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Webgriffe Back in Stock Notification Plugin, add notify state to subscription';
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
