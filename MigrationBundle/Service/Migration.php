<?php

namespace Estina\MigrationBundle\Service;

/**
 * Migration service
 */
class Migration
{
    protected $dbal;
    protected $tableName;
    protected $migrationsDir;
    
    function __construct($dbal, $tableName, $migrationsDir)
    {
        $this->dbal = $dbal;
        $this->tableName = $tableName;
        $this->migrationsDir = $migrationsDir;
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

    /**
     * Create new migration script.
     * 
     * @return string Migration filename
     */
    public function createMigration()
    {
        //Create directory if it does not exist
        if (false === file_exists($this->migrationsDir)) {
            mkdir($this->migrationsDir, 0777, true);
        }

        $path = $this->migrationsDir . '/' . $this->generateMigrationFilename();
        if (file_exists($path)) {
            throw new \Exception("File already exists");
        }
        file_put_contents($path, '');

        return realpath($path);
    }

    /**
     * Generate filename for new migration script.
     * 
     * @return string
     */
    protected function generateMigrationFilename()
    {
        return sprintf('%s.sql', date("YmdHis"));
    }

    /**
     * Update migrations list to database.
     * 
     * @return void
     */
    protected function updateList()
    {
        $files = $this->getFiles();

        $query = "INSERT IGNORE INTO `migrations` (`timestamp`, `active`) VALUES (:timestamp, '0');";
        $statement = $this->dbal->prepare($query);
        foreach ($files as $file) {
            if (1 == preg_match("/\d+/", $file, $match)) {
                $statement->bindValue(':timestamp', $match[0], \PDO::PARAM_INT);
                $statement->execute();
            }
        }
    }

    /**
     * Return list of available migration files.
     * 
     * @return array
     */
    protected function getFiles()
    {
         $filenames = array();
         $iterator = new \DirectoryIterator($this->migrationsDir);
         foreach ($iterator as $fileinfo) {
             if ($fileinfo->isFile() && $fileinfo->getSize() > 0) {
                 $filenames[] = $fileinfo->getFilename();
             }
         }

         return $filenames;
    }

    /**
     * Apply all inactive migrations.
     * 
     * @return void
     */
    public function apply()
    {
        $this->updateList();
    }
}
