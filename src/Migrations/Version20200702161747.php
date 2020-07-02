<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200702161747 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE SEQUENCE transaction_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE user_balance_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE transaction (id INT NOT NULL, user_balance_id INT DEFAULT NULL, amount INT NOT NULL, transaction_id VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_723705D12FC0CB0F ON transaction (transaction_id)');
        $this->addSql('CREATE INDEX IDX_723705D19F66531 ON transaction (user_balance_id)');
        $this->addSql('CREATE TABLE user_balance (id INT NOT NULL, user_id VARCHAR(255) NOT NULL, balance INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F4F901F4A76ED395 ON user_balance (user_id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D19F66531 FOREIGN KEY (user_balance_id) REFERENCES user_balance (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT FK_723705D19F66531');
        $this->addSql('DROP SEQUENCE transaction_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE user_balance_id_seq CASCADE');
        $this->addSql('DROP TABLE transaction');
        $this->addSql('DROP TABLE user_balance');
    }
}
