<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180403180633 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6491341DBCA');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6491341DBCA FOREIGN KEY (current_profile_id) REFERENCES profile (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE subscriber ADD account_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE subscriber ADD CONSTRAINT FK_AD005B699B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id)');
        $this->addSql('CREATE INDEX IDX_AD005B699B6B5FBA ON subscriber (account_id)');
        $this->addSql('ALTER TABLE profile DROP FOREIGN KEY FK_8157AA0F9B6B5FBA');
        $this->addSql('ALTER TABLE profile DROP FOREIGN KEY FK_8157AA0FA76ED395');
        $this->addSql('ALTER TABLE profile ADD CONSTRAINT FK_8157AA0F9B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE profile ADD CONSTRAINT FK_8157AA0FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE profile DROP FOREIGN KEY FK_8157AA0FA76ED395');
        $this->addSql('ALTER TABLE profile DROP FOREIGN KEY FK_8157AA0F9B6B5FBA');
        $this->addSql('ALTER TABLE profile ADD CONSTRAINT FK_8157AA0FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE profile ADD CONSTRAINT FK_8157AA0F9B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE subscriber DROP FOREIGN KEY FK_AD005B699B6B5FBA');
        $this->addSql('DROP INDEX IDX_AD005B699B6B5FBA ON subscriber');
        $this->addSql('ALTER TABLE subscriber DROP account_id');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6491341DBCA');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6491341DBCA FOREIGN KEY (current_profile_id) REFERENCES profile (id)');
    }
}
