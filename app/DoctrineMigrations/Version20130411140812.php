<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130411140812 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE tsk_contract DROP FOREIGN KEY FK_3318CE06CED6CD99");
        $this->addSql("DROP INDEX IDX_3318CE06CED6CD99 ON tsk_contract");
        $this->addSql("ALTER TABLE tsk_contract ADD program_name VARCHAR(255) NOT NULL, ADD contract_expire_date DATE DEFAULT NULL, ADD contract_num_tokens INT DEFAULT NULL, DROP fk_program_id, CHANGE legacy_contract_id legacy_contract_id VARCHAR(255) DEFAULT NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE tsk_contract ADD fk_program_id INT NOT NULL, DROP program_name, DROP contract_expire_date, DROP contract_num_tokens, CHANGE legacy_contract_id legacy_contract_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE tsk_contract ADD CONSTRAINT FK_3318CE06CED6CD99 FOREIGN KEY (fk_program_id) REFERENCES tsk_program (program_id) ON DELETE CASCADE");
        $this->addSql("CREATE INDEX IDX_3318CE06CED6CD99 ON tsk_contract (fk_program_id)");
    }
}
