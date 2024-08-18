<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240818082811 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE beneficiario_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE consulta_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE hospital_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE medico_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE observacao_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE beneficiario (id INT NOT NULL, nome VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, data_nascimento DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE consulta (id INT NOT NULL, beneficiario_id INT NOT NULL, medico_id INT NOT NULL, hospital_id INT NOT NULL, data DATE NOT NULL, status BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A6FE3FDE4B64ABC7 ON consulta (beneficiario_id)');
        $this->addSql('CREATE INDEX IDX_A6FE3FDEA7FB1C0C ON consulta (medico_id)');
        $this->addSql('CREATE INDEX IDX_A6FE3FDE63DBB69 ON consulta (hospital_id)');
        $this->addSql('CREATE TABLE hospital (id INT NOT NULL, nome VARCHAR(255) NOT NULL, cep VARCHAR(255) NOT NULL, estado VARCHAR(255) NOT NULL, cidade VARCHAR(255) NOT NULL, bairro VARCHAR(255) NOT NULL, localidade VARCHAR(255) DEFAULT NULL, complemento VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE medico (id INT NOT NULL, hospital_id INT DEFAULT NULL, nome VARCHAR(255) NOT NULL, especialidade VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_34E5914C63DBB69 ON medico (hospital_id)');
        $this->addSql('CREATE TABLE observacao (id INT NOT NULL, consulta_id INT NOT NULL, descricao TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_123A110DE38D288B ON observacao (consulta_id)');
        $this->addSql('ALTER TABLE consulta ADD CONSTRAINT FK_A6FE3FDE4B64ABC7 FOREIGN KEY (beneficiario_id) REFERENCES beneficiario (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE consulta ADD CONSTRAINT FK_A6FE3FDEA7FB1C0C FOREIGN KEY (medico_id) REFERENCES medico (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE consulta ADD CONSTRAINT FK_A6FE3FDE63DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE medico ADD CONSTRAINT FK_34E5914C63DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE observacao ADD CONSTRAINT FK_123A110DE38D288B FOREIGN KEY (consulta_id) REFERENCES consulta (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE beneficiario_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE consulta_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE hospital_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE medico_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE observacao_id_seq CASCADE');
        $this->addSql('ALTER TABLE consulta DROP CONSTRAINT FK_A6FE3FDE4B64ABC7');
        $this->addSql('ALTER TABLE consulta DROP CONSTRAINT FK_A6FE3FDEA7FB1C0C');
        $this->addSql('ALTER TABLE consulta DROP CONSTRAINT FK_A6FE3FDE63DBB69');
        $this->addSql('ALTER TABLE medico DROP CONSTRAINT FK_34E5914C63DBB69');
        $this->addSql('ALTER TABLE observacao DROP CONSTRAINT FK_123A110DE38D288B');
        $this->addSql('DROP TABLE beneficiario');
        $this->addSql('DROP TABLE consulta');
        $this->addSql('DROP TABLE hospital');
        $this->addSql('DROP TABLE medico');
        $this->addSql('DROP TABLE observacao');
    }
}
