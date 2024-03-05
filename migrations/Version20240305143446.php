<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240305143446 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE "agency_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "app_user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "car_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "delivery_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE email_verifier_token_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "manufacturer_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE password_token_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "problem_car_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "rental_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE rental_archived_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "review_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE "agency" (id INT NOT NULL, address VARCHAR(130) NOT NULL, city VARCHAR(50) NOT NULL, email VARCHAR(180) NOT NULL, opening_hours TIME(0) WITHOUT TIME ZONE NOT NULL, closing_hours TIME(0) WITHOUT TIME ZONE NOT NULL, status SMALLINT NOT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_70C0C6E6E7927C74 ON "agency" (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_70C0C6E6D17F50A6 ON "agency" (uuid)');
        $this->addSql('COMMENT ON COLUMN "agency".uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "agency".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE "app_user" (id INT NOT NULL, agency_id INT DEFAULT NULL, full_name VARCHAR(100) NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, address VARCHAR(130) DEFAULT NULL, birth_date DATE NOT NULL, roles JSON NOT NULL, phone_number VARCHAR(9) NOT NULL, verified_email BOOLEAN NOT NULL, verified_phone_number BOOLEAN NOT NULL, active BOOLEAN NOT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_88BDF3E9E7927C74 ON "app_user" (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_88BDF3E9D17F50A6 ON "app_user" (uuid)');
        $this->addSql('CREATE INDEX IDX_88BDF3E9CDEADB2A ON "app_user" (agency_id)');
        $this->addSql('COMMENT ON COLUMN "app_user".uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "app_user".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE "car" (id INT NOT NULL, agency_id INT NOT NULL, manufacturer_id INT NOT NULL, model VARCHAR(50) NOT NULL, kilometers INT NOT NULL, status SMALLINT NOT NULL, price_per_kilometer NUMERIC(8, 2) NOT NULL, file_path VARCHAR(255) NOT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_773DE69DD17F50A6 ON "car" (uuid)');
        $this->addSql('CREATE INDEX IDX_773DE69DCDEADB2A ON "car" (agency_id)');
        $this->addSql('CREATE INDEX IDX_773DE69DA23B42D ON "car" (manufacturer_id)');
        $this->addSql('COMMENT ON COLUMN "car".uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "car".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE "delivery" (id INT NOT NULL, rental_id INT NOT NULL, rental_archived_id INT DEFAULT NULL, status SMALLINT NOT NULL, address VARCHAR(130) NOT NULL, delivery_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3781EC10D17F50A6 ON "delivery" (uuid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3781EC10A7CF2329 ON "delivery" (rental_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3781EC10C8BEFEF3 ON "delivery" (rental_archived_id)');
        $this->addSql('COMMENT ON COLUMN "delivery".uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "delivery".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE email_verifier_token (id INT NOT NULL, user_id INT NOT NULL, email VARCHAR(180) NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, uuid UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_40D58377E7927C74 ON email_verifier_token (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_40D58377D17F50A6 ON email_verifier_token (uuid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_40D58377A76ED395 ON email_verifier_token (user_id)');
        $this->addSql('COMMENT ON COLUMN email_verifier_token.expires_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN email_verifier_token.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE "manufacturer" (id INT NOT NULL, name VARCHAR(16) NOT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3D0AE6DCD17F50A6 ON "manufacturer" (uuid)');
        $this->addSql('COMMENT ON COLUMN "manufacturer".uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "manufacturer".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE password_token (id INT NOT NULL, user_id INT NOT NULL, token VARCHAR(50) NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BEAB6C245F37A13B ON password_token (token)');
        $this->addSql('CREATE INDEX IDX_BEAB6C24A76ED395 ON password_token (user_id)');
        $this->addSql('CREATE TABLE "problem_car" (id INT NOT NULL, car_id INT NOT NULL, description TEXT NOT NULL, type BOOLEAN NOT NULL, problem_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_189274F6D17F50A6 ON "problem_car" (uuid)');
        $this->addSql('CREATE INDEX IDX_189274F6C3C6F69F ON "problem_car" (car_id)');
        $this->addSql('COMMENT ON COLUMN "problem_car".uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "problem_car".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE "rental" (id INT NOT NULL, agency_id INT NOT NULL, customer_id INT NOT NULL, employee_id INT DEFAULT NULL, car_id INT NOT NULL, contract SMALLINT NOT NULL, mileage_kilometers INT NOT NULL, used_kilometers INT DEFAULT NULL, from_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, to_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, price NUMERIC(8, 2) NOT NULL, status SMALLINT NOT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1619C27DD17F50A6 ON "rental" (uuid)');
        $this->addSql('CREATE INDEX IDX_1619C27DCDEADB2A ON "rental" (agency_id)');
        $this->addSql('CREATE INDEX IDX_1619C27D9395C3F3 ON "rental" (customer_id)');
        $this->addSql('CREATE INDEX IDX_1619C27D8C03F15C ON "rental" (employee_id)');
        $this->addSql('CREATE INDEX IDX_1619C27DC3C6F69F ON "rental" (car_id)');
        $this->addSql('COMMENT ON COLUMN "rental".uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "rental".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE rental_archived (id INT NOT NULL, agency_id INT NOT NULL, customer_id INT NOT NULL, employee_id INT NOT NULL, car_id INT NOT NULL, delivery_id INT DEFAULT NULL, uuid UUID NOT NULL, contract SMALLINT NOT NULL, mileage_kilometers INT NOT NULL, used_kilometers INT DEFAULT NULL, from_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, to_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, price NUMERIC(8, 2) NOT NULL, status SMALLINT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_51D770CDCDEADB2A ON rental_archived (agency_id)');
        $this->addSql('CREATE INDEX IDX_51D770CD9395C3F3 ON rental_archived (customer_id)');
        $this->addSql('CREATE INDEX IDX_51D770CD8C03F15C ON rental_archived (employee_id)');
        $this->addSql('CREATE INDEX IDX_51D770CDC3C6F69F ON rental_archived (car_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_51D770CD12136921 ON rental_archived (delivery_id)');
        $this->addSql('COMMENT ON COLUMN rental_archived.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN rental_archived.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE "review" (id INT NOT NULL, customer_id INT NOT NULL, agency_id INT NOT NULL, star NUMERIC(2, 1) NOT NULL, details TEXT NOT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_794381C6D17F50A6 ON "review" (uuid)');
        $this->addSql('CREATE INDEX IDX_794381C69395C3F3 ON "review" (customer_id)');
        $this->addSql('CREATE INDEX IDX_794381C6CDEADB2A ON "review" (agency_id)');
        $this->addSql('COMMENT ON COLUMN "review".uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "review".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE "app_user" ADD CONSTRAINT FK_88BDF3E9CDEADB2A FOREIGN KEY (agency_id) REFERENCES "agency" (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "car" ADD CONSTRAINT FK_773DE69DCDEADB2A FOREIGN KEY (agency_id) REFERENCES "agency" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "car" ADD CONSTRAINT FK_773DE69DA23B42D FOREIGN KEY (manufacturer_id) REFERENCES "manufacturer" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "delivery" ADD CONSTRAINT FK_3781EC10A7CF2329 FOREIGN KEY (rental_id) REFERENCES "rental" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "delivery" ADD CONSTRAINT FK_3781EC10C8BEFEF3 FOREIGN KEY (rental_archived_id) REFERENCES rental_archived (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE email_verifier_token ADD CONSTRAINT FK_40D58377A76ED395 FOREIGN KEY (user_id) REFERENCES "app_user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE password_token ADD CONSTRAINT FK_BEAB6C24A76ED395 FOREIGN KEY (user_id) REFERENCES "app_user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "problem_car" ADD CONSTRAINT FK_189274F6C3C6F69F FOREIGN KEY (car_id) REFERENCES "car" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "rental" ADD CONSTRAINT FK_1619C27DCDEADB2A FOREIGN KEY (agency_id) REFERENCES "agency" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "rental" ADD CONSTRAINT FK_1619C27D9395C3F3 FOREIGN KEY (customer_id) REFERENCES "app_user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "rental" ADD CONSTRAINT FK_1619C27D8C03F15C FOREIGN KEY (employee_id) REFERENCES "app_user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "rental" ADD CONSTRAINT FK_1619C27DC3C6F69F FOREIGN KEY (car_id) REFERENCES "car" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE rental_archived ADD CONSTRAINT FK_51D770CDCDEADB2A FOREIGN KEY (agency_id) REFERENCES "agency" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE rental_archived ADD CONSTRAINT FK_51D770CD9395C3F3 FOREIGN KEY (customer_id) REFERENCES "app_user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE rental_archived ADD CONSTRAINT FK_51D770CD8C03F15C FOREIGN KEY (employee_id) REFERENCES "app_user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE rental_archived ADD CONSTRAINT FK_51D770CDC3C6F69F FOREIGN KEY (car_id) REFERENCES "car" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE rental_archived ADD CONSTRAINT FK_51D770CD12136921 FOREIGN KEY (delivery_id) REFERENCES "delivery" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "review" ADD CONSTRAINT FK_794381C69395C3F3 FOREIGN KEY (customer_id) REFERENCES "app_user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "review" ADD CONSTRAINT FK_794381C6CDEADB2A FOREIGN KEY (agency_id) REFERENCES "agency" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE "agency_id_seq" CASCADE');
        $this->addSql('DROP SEQUENCE "app_user_id_seq" CASCADE');
        $this->addSql('DROP SEQUENCE "car_id_seq" CASCADE');
        $this->addSql('DROP SEQUENCE "delivery_id_seq" CASCADE');
        $this->addSql('DROP SEQUENCE email_verifier_token_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE "manufacturer_id_seq" CASCADE');
        $this->addSql('DROP SEQUENCE password_token_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE "problem_car_id_seq" CASCADE');
        $this->addSql('DROP SEQUENCE "rental_id_seq" CASCADE');
        $this->addSql('DROP SEQUENCE rental_archived_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE "review_id_seq" CASCADE');
        $this->addSql('ALTER TABLE "app_user" DROP CONSTRAINT FK_88BDF3E9CDEADB2A');
        $this->addSql('ALTER TABLE "car" DROP CONSTRAINT FK_773DE69DCDEADB2A');
        $this->addSql('ALTER TABLE "car" DROP CONSTRAINT FK_773DE69DA23B42D');
        $this->addSql('ALTER TABLE "delivery" DROP CONSTRAINT FK_3781EC10A7CF2329');
        $this->addSql('ALTER TABLE "delivery" DROP CONSTRAINT FK_3781EC10C8BEFEF3');
        $this->addSql('ALTER TABLE email_verifier_token DROP CONSTRAINT FK_40D58377A76ED395');
        $this->addSql('ALTER TABLE password_token DROP CONSTRAINT FK_BEAB6C24A76ED395');
        $this->addSql('ALTER TABLE "problem_car" DROP CONSTRAINT FK_189274F6C3C6F69F');
        $this->addSql('ALTER TABLE "rental" DROP CONSTRAINT FK_1619C27DCDEADB2A');
        $this->addSql('ALTER TABLE "rental" DROP CONSTRAINT FK_1619C27D9395C3F3');
        $this->addSql('ALTER TABLE "rental" DROP CONSTRAINT FK_1619C27D8C03F15C');
        $this->addSql('ALTER TABLE "rental" DROP CONSTRAINT FK_1619C27DC3C6F69F');
        $this->addSql('ALTER TABLE rental_archived DROP CONSTRAINT FK_51D770CDCDEADB2A');
        $this->addSql('ALTER TABLE rental_archived DROP CONSTRAINT FK_51D770CD9395C3F3');
        $this->addSql('ALTER TABLE rental_archived DROP CONSTRAINT FK_51D770CD8C03F15C');
        $this->addSql('ALTER TABLE rental_archived DROP CONSTRAINT FK_51D770CDC3C6F69F');
        $this->addSql('ALTER TABLE rental_archived DROP CONSTRAINT FK_51D770CD12136921');
        $this->addSql('ALTER TABLE "review" DROP CONSTRAINT FK_794381C69395C3F3');
        $this->addSql('ALTER TABLE "review" DROP CONSTRAINT FK_794381C6CDEADB2A');
        $this->addSql('DROP TABLE "agency"');
        $this->addSql('DROP TABLE "app_user"');
        $this->addSql('DROP TABLE "car"');
        $this->addSql('DROP TABLE "delivery"');
        $this->addSql('DROP TABLE email_verifier_token');
        $this->addSql('DROP TABLE "manufacturer"');
        $this->addSql('DROP TABLE password_token');
        $this->addSql('DROP TABLE "problem_car"');
        $this->addSql('DROP TABLE "rental"');
        $this->addSql('DROP TABLE rental_archived');
        $this->addSql('DROP TABLE "review"');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
