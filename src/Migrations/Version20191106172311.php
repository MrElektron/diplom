<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191106172311 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE fos_user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, username_canonical VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, email_canonical VARCHAR(180) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_957A647992FC23A8 (username_canonical), UNIQUE INDEX UNIQ_957A6479A0D96FBF (email_canonical), UNIQUE INDEX UNIQ_957A6479C05FB297 (confirmation_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE file (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, file_size VARCHAR(255) NOT NULL, format VARCHAR(255) NOT NULL, uploaded_at DATETIME NOT NULL, file_name LONGTEXT NOT NULL, stored_file_name LONGTEXT NOT NULL, stored_preview_file_name LONGTEXT DEFAULT NULL, stored_file_dir LONGTEXT DEFAULT NULL, INDEX IDX_8C9F36107E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE discipline (id INT AUTO_INCREMENT NOT NULL, file_id INT DEFAULT NULL, discipline_index VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, exams VARCHAR(255) DEFAULT NULL, offsets VARCHAR(255) DEFAULT NULL, differentiated_offsets VARCHAR(255) DEFAULT NULL, course_projects VARCHAR(255) DEFAULT NULL, coursework VARCHAR(255) DEFAULT NULL, other VARCHAR(255) DEFAULT NULL, maximum_load VARCHAR(255) DEFAULT NULL, independent_work VARCHAR(255) DEFAULT NULL, consultations VARCHAR(255) DEFAULT NULL, total VARCHAR(255) DEFAULT NULL, lessons VARCHAR(255) DEFAULT NULL, practical_lessons VARCHAR(255) DEFAULT NULL, laboratory_classes VARCHAR(255) DEFAULT NULL, course_design VARCHAR(255) DEFAULT NULL, intermediate_certification VARCHAR(255) DEFAULT NULL, lesson_workshop VARCHAR(255) DEFAULT NULL, individual_project VARCHAR(255) DEFAULT NULL, INDEX IDX_75BEEE3F93CB796C (file_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE competence (id INT AUTO_INCREMENT NOT NULL, discipline_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, INDEX IDX_94D4687FA5522701 (discipline_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE semester (id INT AUTO_INCREMENT NOT NULL, discipline_id INT DEFAULT NULL, semester INT NOT NULL, maximum_load VARCHAR(255) DEFAULT NULL, independent_work VARCHAR(255) DEFAULT NULL, consultations VARCHAR(255) DEFAULT NULL, obligatory VARCHAR(255) DEFAULT NULL, lessons VARCHAR(255) DEFAULT NULL, practical_lessons VARCHAR(255) DEFAULT NULL, laboratory_classes VARCHAR(255) DEFAULT NULL, course_design VARCHAR(255) DEFAULT NULL, intermediate_certification VARCHAR(255) DEFAULT NULL, lesson_workshop VARCHAR(255) DEFAULT NULL, individual_project VARCHAR(255) DEFAULT NULL, INDEX IDX_F7388EEDA5522701 (discipline_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE specialty (id INT AUTO_INCREMENT NOT NULL, file_id INT DEFAULT NULL, code VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, number VARCHAR(255) NOT NULL, qualification VARCHAR(255) NOT NULL, INDEX IDX_E066A6EC93CB796C (file_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE competence ADD CONSTRAINT FK_94D4687FA5522701 FOREIGN KEY (discipline_id) REFERENCES discipline (id)');
        $this->addSql('ALTER TABLE discipline ADD CONSTRAINT FK_75BEEE3F93CB796C FOREIGN KEY (file_id) REFERENCES file (id)');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F36107E3C61F9 FOREIGN KEY (owner_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE semester ADD CONSTRAINT FK_F7388EEDA5522701 FOREIGN KEY (discipline_id) REFERENCES discipline (id)');
        $this->addSql('ALTER TABLE specialty ADD CONSTRAINT FK_E066A6EC93CB796C FOREIGN KEY (file_id) REFERENCES file (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE competence DROP FOREIGN KEY FK_94D4687FA5522701');
        $this->addSql('ALTER TABLE semester DROP FOREIGN KEY FK_F7388EEDA5522701');
        $this->addSql('ALTER TABLE discipline DROP FOREIGN KEY FK_75BEEE3F93CB796C');
        $this->addSql('ALTER TABLE specialty DROP FOREIGN KEY FK_E066A6EC93CB796C');
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F36107E3C61F9');
        $this->addSql('DROP TABLE competence');
        $this->addSql('DROP TABLE discipline');
        $this->addSql('DROP TABLE file');
        $this->addSql('DROP TABLE semester');
        $this->addSql('DROP TABLE specialty');
        $this->addSql('DROP TABLE fos_user');
    }
}
