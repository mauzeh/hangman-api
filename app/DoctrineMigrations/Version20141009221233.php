<?php
namespace Hangman\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

use Goodby\CSV\Import\Standard\Lexer;
use Goodby\CSV\Import\Standard\Interpreter;
use Goodby\CSV\Import\Standard\LexerConfig;

class Version20141009221233 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $lexer = new Lexer(new LexerConfig());

        $words = array();

        $interpreter = new Interpreter();
        $interpreter->addObserver(function(array $row) use (&$words) {
            $words[] = '('.$this->connection->quote($row[0], \PDO::PARAM_STR).')';
        });

        $lexer->parse(__DIR__.'/../Resources/files/words.english', $interpreter);

        $this->addSql('INSERT INTO word (word) VALUES '.implode(',',$words));
    }

    public function down(Schema $schema)
    {
        $this->addSql('TRUNCATE TABLE word');
    }
}
