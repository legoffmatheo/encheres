<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241202082438 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE enchere (id INT AUTO_INCREMENT NOT NULL, le_produit_id INT DEFAULT NULL, titre VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, date_heure_debut DATETIME DEFAULT NULL, date_heure_fin DATETIME DEFAULT NULL, statut VARCHAR(255) DEFAULT NULL, prix_debut DOUBLE PRECISION DEFAULT NULL, UNIQUE INDEX UNIQ_38D1870F2C340150 (le_produit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE participation (id INT AUTO_INCREMENT NOT NULL, la_enchere_id INT DEFAULT NULL, le_user_id INT DEFAULT NULL, prix_encheri DOUBLE PRECISION DEFAULT NULL, budget_maximum DOUBLE PRECISION DEFAULT NULL, INDEX IDX_AB55E24FC80D2A2F (la_enchere_id), INDEX IDX_AB55E24F88A1A5E2 (le_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE produit (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, prix_plancher DOUBLE PRECISION DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE enchere ADD CONSTRAINT FK_38D1870F2C340150 FOREIGN KEY (le_produit_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE participation ADD CONSTRAINT FK_AB55E24FC80D2A2F FOREIGN KEY (la_enchere_id) REFERENCES enchere (id)');
        $this->addSql('ALTER TABLE participation ADD CONSTRAINT FK_AB55E24F88A1A5E2 FOREIGN KEY (le_user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE enchere DROP FOREIGN KEY FK_38D1870F2C340150');
        $this->addSql('ALTER TABLE participation DROP FOREIGN KEY FK_AB55E24FC80D2A2F');
        $this->addSql('ALTER TABLE participation DROP FOREIGN KEY FK_AB55E24F88A1A5E2');
        $this->addSql('DROP TABLE enchere');
        $this->addSql('DROP TABLE participation');
        $this->addSql('DROP TABLE produit');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
