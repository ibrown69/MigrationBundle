<?php

namespace Estina\MigrationBundle\Service;

/**
 * Migration service
 */
class Migration
{
    protected $dbal;
    protected $tableName;
    
    function __construct($dbal, $tableName)
    {
        $this->dbal = $dbal;
        $this->tableName = $tableName;
    }

    /**
     * Check if migrations table is created.
     * 
     * @return bool
     */
    public function isTableCreated()
    {
        $query = 'SHOW TABLES';
        $statement = $this->dbal->prepare($query);
        $statement->execute();

        while ($col = $statement->fetchColumn()) {
            if ($col == $this->tableName) {
                return true;
            }
        }

        return false;
    }

    /**
     * Create migrations table.
     * 
     * @return bool
     */
    public function createTable()
    {
        $query = <<<EOT
CREATE TABLE IF NOT EXISTS `$this->tableName` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timestamp` datetime NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `timestamp` (`timestamp`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8
EOT;

        $statement = $this->dbal->prepare($query);
        return $statement->execute();
    }
}
