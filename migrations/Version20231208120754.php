<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231208120754 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE "agency_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "car_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "delivery_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "manufacturer_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "problem_car_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "rental_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "review_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "web_user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE "agency" (id INT NOT NULL, address VARCHAR(140) NOT NULL, email VARCHAR(90) NOT NULL, opening_hours TIME(0) WITHOUT TIME ZONE NOT NULL, closing_hours TIME(0) WITHOUT TIME ZONE NOT NULL, status BOOLEAN NOT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN "agency".uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "agency".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE "car" (id INT NOT NULL, agency_id INT NOT NULL, manufacturer_id INT NOT NULL, model VARCHAR(50) NOT NULL, kilometers INT NOT NULL, status BOOLEAN NOT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_773DE69DCDEADB2A ON "car" (agency_id)');
        $this->addSql('CREATE INDEX IDX_773DE69DA23B42D ON "car" (manufacturer_id)');
        $this->addSql('COMMENT ON COLUMN "car".uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "car".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE "delivery" (id INT NOT NULL, rental_id INT NOT NULL, status INT NOT NULL, address VARCHAR(130) NOT NULL, delivery_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3781EC10A7CF2329 ON "delivery" (rental_id)');
        $this->addSql('COMMENT ON COLUMN "delivery".uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "delivery".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE "manufacturer" (id INT NOT NULL, name VARCHAR(16) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE "problem_car" (id INT NOT NULL, car_id INT NOT NULL, description TEXT NOT NULL, type BOOLEAN NOT NULL, problem_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_189274F6C3C6F69F ON "problem_car" (car_id)');
        $this->addSql('CREATE TABLE "rental" (id INT NOT NULL, customer_id INT NOT NULL, employee_id INT DEFAULT NULL, car_id INT NOT NULL, contract BOOLEAN NOT NULL, mileage_kilometers INT NOT NULL, from_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, to_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, price NUMERIC(8, 2) NOT NULL, status INT NOT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1619C27D9395C3F3 ON "rental" (customer_id)');
        $this->addSql('CREATE INDEX IDX_1619C27D8C03F15C ON "rental" (employee_id)');
        $this->addSql('CREATE INDEX IDX_1619C27DC3C6F69F ON "rental" (car_id)');
        $this->addSql('COMMENT ON COLUMN "rental".uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "rental".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE "review" (id INT NOT NULL, customer_id INT NOT NULL, agency_id INT NOT NULL, star NUMERIC(2, 1) NOT NULL, details TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_794381C69395C3F3 ON "review" (customer_id)');
        $this->addSql('CREATE INDEX IDX_794381C6CDEADB2A ON "review" (agency_id)');
        $this->addSql('COMMENT ON COLUMN "review".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE "web_user" (id INT NOT NULL, agency_id INT DEFAULT NULL, first_name VARCHAR(50) NOT NULL, last_name VARCHAR(50) NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, address VARCHAR(130) NOT NULL, birth_date DATE NOT NULL, roles JSON NOT NULL, active BOOLEAN NOT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4991DBBCE7927C74 ON "web_user" (email)');
        $this->addSql('CREATE INDEX IDX_4991DBBCCDEADB2A ON "web_user" (agency_id)');
        $this->addSql('COMMENT ON COLUMN "web_user".uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "web_user".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE "car" ADD CONSTRAINT FK_773DE69DCDEADB2A FOREIGN KEY (agency_id) REFERENCES "agency" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "car" ADD CONSTRAINT FK_773DE69DA23B42D FOREIGN KEY (manufacturer_id) REFERENCES "manufacturer" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "delivery" ADD CONSTRAINT FK_3781EC10A7CF2329 FOREIGN KEY (rental_id) REFERENCES "rental" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "problem_car" ADD CONSTRAINT FK_189274F6C3C6F69F FOREIGN KEY (car_id) REFERENCES "car" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "rental" ADD CONSTRAINT FK_1619C27D9395C3F3 FOREIGN KEY (customer_id) REFERENCES "web_user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "rental" ADD CONSTRAINT FK_1619C27D8C03F15C FOREIGN KEY (employee_id) REFERENCES "web_user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "rental" ADD CONSTRAINT FK_1619C27DC3C6F69F FOREIGN KEY (car_id) REFERENCES "car" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "review" ADD CONSTRAINT FK_794381C69395C3F3 FOREIGN KEY (customer_id) REFERENCES "web_user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "review" ADD CONSTRAINT FK_794381C6CDEADB2A FOREIGN KEY (agency_id) REFERENCES "agency" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "web_user" ADD CONSTRAINT FK_4991DBBCCDEADB2A FOREIGN KEY (agency_id) REFERENCES "agency" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE "agency_id_seq" CASCADE');
        $this->addSql('DROP SEQUENCE "car_id_seq" CASCADE');
        $this->addSql('DROP SEQUENCE "delivery_id_seq" CASCADE');
        $this->addSql('DROP SEQUENCE "manufacturer_id_seq" CASCADE');
        $this->addSql('DROP SEQUENCE "problem_car_id_seq" CASCADE');
        $this->addSql('DROP SEQUENCE "rental_id_seq" CASCADE');
        $this->addSql('DROP SEQUENCE "review_id_seq" CASCADE');
        $this->addSql('DROP SEQUENCE "web_user_id_seq" CASCADE');
        $this->addSql('ALTER TABLE "car" DROP CONSTRAINT FK_773DE69DCDEADB2A');
        $this->addSql('ALTER TABLE "car" DROP CONSTRAINT FK_773DE69DA23B42D');
        $this->addSql('ALTER TABLE "delivery" DROP CONSTRAINT FK_3781EC10A7CF2329');
        $this->addSql('ALTER TABLE "problem_car" DROP CONSTRAINT FK_189274F6C3C6F69F');
        $this->addSql('ALTER TABLE "rental" DROP CONSTRAINT FK_1619C27D9395C3F3');
        $this->addSql('ALTER TABLE "rental" DROP CONSTRAINT FK_1619C27D8C03F15C');
        $this->addSql('ALTER TABLE "rental" DROP CONSTRAINT FK_1619C27DC3C6F69F');
        $this->addSql('ALTER TABLE "review" DROP CONSTRAINT FK_794381C69395C3F3');
        $this->addSql('ALTER TABLE "review" DROP CONSTRAINT FK_794381C6CDEADB2A');
        $this->addSql('ALTER TABLE "web_user" DROP CONSTRAINT FK_4991DBBCCDEADB2A');
        $this->addSql('DROP TABLE "agency"');
        $this->addSql('DROP TABLE "car"');
        $this->addSql('DROP TABLE "delivery"');
        $this->addSql('DROP TABLE "manufacturer"');
        $this->addSql('DROP TABLE "problem_car"');
        $this->addSql('DROP TABLE "rental"');
        $this->addSql('DROP TABLE "review"');
        $this->addSql('DROP TABLE "web_user"');
    }
}
