<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180423190541 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE dtc_queue_job (id BIGINT AUTO_INCREMENT NOT NULL, worker_name VARCHAR(255) NOT NULL, class_name VARCHAR(255) NOT NULL, method VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, args LONGTEXT NOT NULL, priority INT DEFAULT NULL, crc_hash VARCHAR(255) NOT NULL, when_us NUMERIC(18, 0) DEFAULT NULL, expires_at DATETIME DEFAULT NULL, started_at DATETIME DEFAULT NULL, finished_at DATETIME DEFAULT NULL, elapsed DOUBLE PRECISION DEFAULT NULL, message LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, max_duration INT DEFAULT NULL, run_id BIGINT DEFAULT NULL, stalls INT NOT NULL, max_stalls INT DEFAULT NULL, exceptions INT NOT NULL, max_exceptions INT DEFAULT NULL, failures INT NOT NULL, max_failures INT DEFAULT NULL, retries INT NOT NULL, max_retries INT DEFAULT NULL, INDEX job_crc_hash_idx (crc_hash, status), INDEX job_priority_idx (priority, when_us), INDEX job_when_idx (when_us), INDEX job_status_idx (status, when_us), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dtc_queue_job_archive (id BIGINT NOT NULL, finished_at DATETIME DEFAULT NULL, elapsed DOUBLE PRECISION DEFAULT NULL, started_at DATETIME DEFAULT NULL, when_us NUMERIC(18, 0) DEFAULT NULL, priority INT DEFAULT NULL, expires_at DATETIME DEFAULT NULL, updated_at DATETIME NOT NULL, worker_name VARCHAR(255) NOT NULL, class_name VARCHAR(255) NOT NULL, method VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, args LONGTEXT NOT NULL, crc_hash VARCHAR(255) NOT NULL, message LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, max_duration INT DEFAULT NULL, run_id BIGINT DEFAULT NULL, stalls INT NOT NULL, max_stalls INT DEFAULT NULL, exceptions INT NOT NULL, max_exceptions INT DEFAULT NULL, failures INT NOT NULL, max_failures INT DEFAULT NULL, retries INT NOT NULL, max_retries INT DEFAULT NULL, INDEX job_archive_status_idx (status), INDEX job_archive_updated_at_idx (updated_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dtc_queue_run_archive (id BIGINT AUTO_INCREMENT NOT NULL, started_at DATETIME DEFAULT NULL, duration INT DEFAULT NULL, ended_at DATETIME DEFAULT NULL, elapsed DOUBLE PRECISION DEFAULT NULL, max_count INT DEFAULT NULL, last_heartbeat_at DATETIME DEFAULT NULL, process_timeout INT DEFAULT NULL, current_job_id VARCHAR(255) DEFAULT NULL, processed INT NOT NULL, hostname VARCHAR(255) DEFAULT NULL, pid INT DEFAULT NULL, INDEX run_archive_ended_at_idx (ended_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dtc_queue_run (id BIGINT AUTO_INCREMENT NOT NULL, started_at DATETIME DEFAULT NULL, ended_at DATETIME DEFAULT NULL, elapsed DOUBLE PRECISION DEFAULT NULL, duration INT DEFAULT NULL, last_heartbeat_at DATETIME NOT NULL, max_count INT DEFAULT NULL, processed INT NOT NULL, hostname VARCHAR(255) DEFAULT NULL, pid INT DEFAULT NULL, process_timeout INT DEFAULT NULL, current_job_id VARCHAR(255) DEFAULT NULL, INDEX run_last_heart_beat (last_heartbeat_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dtc_queue_job_timing (id BIGINT AUTO_INCREMENT NOT NULL, finished_at DATETIME NOT NULL, status INT NOT NULL, INDEX job_timing_finished_at (status, finished_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE project CHANGE contents contents JSON DEFAULT NULL COMMENT \'(DC2Type:json_array)\'');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE dtc_queue_job');
        $this->addSql('DROP TABLE dtc_queue_job_archive');
        $this->addSql('DROP TABLE dtc_queue_run_archive');
        $this->addSql('DROP TABLE dtc_queue_run');
        $this->addSql('DROP TABLE dtc_queue_job_timing');
        $this->addSql('ALTER TABLE project CHANGE contents contents JSON DEFAULT NULL');
    }
}
