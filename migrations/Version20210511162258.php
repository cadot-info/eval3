<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210511162258 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE crypto (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, symbol VARCHAR(5) NOT NULL, quantite INTEGER NOT NULL, prix_achat INTEGER NOT NULL)');
        $this->addSql('CREATE TABLE resultat (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, date DATE NOT NULL, valeur INTEGER NOT NULL)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE crypto');
        $this->addSql('DROP TABLE resultat');
    }
}
