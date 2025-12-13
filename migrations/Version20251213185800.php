<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251213185800 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create expenses table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE expenses (id SERIAL NOT NULL, description VARCHAR(255) NOT NULL, amount NUMERIC(10, 2) NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, category VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE expenses');
    }
}
