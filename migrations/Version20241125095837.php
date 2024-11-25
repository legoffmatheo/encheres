<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241125095837 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE enchere ADD les_participations_id INT NOT NULL, ADD le_produit_id INT NOT NULL');
        $this->addSql('ALTER TABLE enchere ADD CONSTRAINT FK_38D1870F4F998DC7 FOREIGN KEY (les_participations_id) REFERENCES participation (id)');
        $this->addSql('ALTER TABLE enchere ADD CONSTRAINT FK_38D1870F2C340150 FOREIGN KEY (le_produit_id) REFERENCES produit (id)');
        $this->addSql('CREATE INDEX IDX_38D1870F4F998DC7 ON enchere (les_participations_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_38D1870F2C340150 ON enchere (le_produit_id)');
        $this->addSql('ALTER TABLE user ADD les_participations_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6494F998DC7 FOREIGN KEY (les_participations_id) REFERENCES participation (id)');
        $this->addSql('CREATE INDEX IDX_8D93D6494F998DC7 ON user (les_participations_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE enchere DROP FOREIGN KEY FK_38D1870F4F998DC7');
        $this->addSql('ALTER TABLE enchere DROP FOREIGN KEY FK_38D1870F2C340150');
        $this->addSql('DROP INDEX IDX_38D1870F4F998DC7 ON enchere');
        $this->addSql('DROP INDEX UNIQ_38D1870F2C340150 ON enchere');
        $this->addSql('ALTER TABLE enchere DROP les_participations_id, DROP le_produit_id');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6494F998DC7');
        $this->addSql('DROP INDEX IDX_8D93D6494F998DC7 ON user');
        $this->addSql('ALTER TABLE user DROP les_participations_id');
    }
}
