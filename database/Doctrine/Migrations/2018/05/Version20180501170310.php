<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180501170310 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE subscriber ADD subscribers_lists_id INT DEFAULT NULL, ADD surname VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE subscriber ADD CONSTRAINT FK_AD005B6950920C07 FOREIGN KEY (subscribers_lists_id) REFERENCES subscriber_list (id)');
        $this->addSql('CREATE INDEX IDX_AD005B6950920C07 ON subscriber (subscribers_lists_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE subscriber DROP FOREIGN KEY FK_AD005B6950920C07');
        $this->addSql('DROP INDEX IDX_AD005B6950920C07 ON subscriber');
        $this->addSql('ALTER TABLE subscriber DROP subscribers_lists_id, DROP surname');
    }
}
