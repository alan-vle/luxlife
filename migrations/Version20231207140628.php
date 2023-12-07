<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231207140628 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE delivery ADD rental_id INT NOT NULL');
        $this->addSql('ALTER TABLE delivery ADD CONSTRAINT FK_3781EC10A7CF2329 FOREIGN KEY (rental_id) REFERENCES rental (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3781EC10A7CF2329 ON delivery (rental_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE delivery DROP CONSTRAINT FK_3781EC10A7CF2329');
        $this->addSql('DROP INDEX UNIQ_3781EC10A7CF2329');
        $this->addSql('ALTER TABLE delivery DROP rental_id');
    }
}
