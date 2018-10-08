<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180410195447 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE layout (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, header LONGTEXT NOT NULL, footer LONGTEXT NOT NULL, UNIQUE INDEX UNIQ_3A3A6BE25E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE template (id INT AUTO_INCREMENT NOT NULL, layout_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, layout_html LONGTEXT DEFAULT NULL, head_styles LONGTEXT DEFAULT NULL, preheader LONGTEXT DEFAULT NULL, is_public TINYINT(1) DEFAULT \'1\' NOT NULL, is_active TINYINT(1) DEFAULT \'0\' NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, archive_name VARCHAR(255) DEFAULT NULL, archive_original_name VARCHAR(255) DEFAULT NULL, archive_mime_type VARCHAR(255) DEFAULT NULL, archive_size INT DEFAULT NULL, archive_dimensions LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', UNIQUE INDEX UNIQ_97601F835E237E06 (name), INDEX IDX_97601F838C22AA1A (layout_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE template_section (id INT AUTO_INCREMENT NOT NULL, template_id INT DEFAULT NULL, contents LONGTEXT NOT NULL, name VARCHAR(255) NOT NULL, thumbnail VARCHAR(255) DEFAULT NULL, INDEX IDX_7ECB01D65DA0FB8 (template_id), UNIQUE INDEX template_section_name_unique (template_id, name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE purchased_template (id INT AUTO_INCREMENT NOT NULL, template_id INT NOT NULL, purchased_by_id INT DEFAULT NULL, account_id INT NOT NULL, purchase_code VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_ACFFD1E75DA0FB8 (template_id), INDEX IDX_ACFFD1E751D43F65 (purchased_by_id), INDEX IDX_ACFFD1E79B6B5FBA (account_id), UNIQUE INDEX purchase_code_unique (purchase_code), UNIQUE INDEX account_template_unique (template_id, account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE template ADD CONSTRAINT FK_97601F838C22AA1A FOREIGN KEY (layout_id) REFERENCES layout (id)');
        $this->addSql('ALTER TABLE template_section ADD CONSTRAINT FK_7ECB01D65DA0FB8 FOREIGN KEY (template_id) REFERENCES template (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE purchased_template ADD CONSTRAINT FK_ACFFD1E75DA0FB8 FOREIGN KEY (template_id) REFERENCES template (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE purchased_template ADD CONSTRAINT FK_ACFFD1E751D43F65 FOREIGN KEY (purchased_by_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE purchased_template ADD CONSTRAINT FK_ACFFD1E79B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE template DROP FOREIGN KEY FK_97601F838C22AA1A');
        $this->addSql('ALTER TABLE template_section DROP FOREIGN KEY FK_7ECB01D65DA0FB8');
        $this->addSql('ALTER TABLE purchased_template DROP FOREIGN KEY FK_ACFFD1E75DA0FB8');
        $this->addSql('DROP TABLE layout');
        $this->addSql('DROP TABLE template');
        $this->addSql('DROP TABLE template_section');
        $this->addSql('DROP TABLE purchased_template');
    }
}
