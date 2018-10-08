<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180403181153 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE subscriber DROP FOREIGN KEY FK_AD005B699B6B5FBA');
        $this->addSql('ALTER TABLE subscriber DROP FOREIGN KEY FK_AD005B69A76ED395');
        $this->addSql('ALTER TABLE subscriber CHANGE user_id user_id INT NOT NULL, CHANGE account_id account_id INT NOT NULL');
        $this->addSql('ALTER TABLE subscriber ADD CONSTRAINT FK_AD005B699B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE subscriber ADD CONSTRAINT FK_AD005B69A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE subscriber CHANGE user_id user_id INT DEFAULT NULL, CHANGE account_id account_id INT DEFAULT NULL');
    }
}
