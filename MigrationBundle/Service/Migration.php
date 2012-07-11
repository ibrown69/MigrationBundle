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
     * @param int $timestamp Timestamp
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

        $query = "INSERT IGNORE INTO `{$this->tableName}` (`timestamp`, `active`) VALUES (:timestamp, '0');";
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

        $query = "SELECT `id`, `timestamp` FROM {$this->tableName} WHERE `active` = 0 ORDER BY `timestamp` ASC";
        $statement = $this->dbal->prepare($query);
        $statement->execute();

        while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
            $filename = realpath($this->migrationsDir . '/' . preg_replace('/[^\d]/', '', $row['timestamp']) . '.sql');

            if (file_exists($filename)) {
                $this->dbal->beginTransaction();
                $this->dbal->exec(file_get_contents($filename));
                $this->markActive($row['id'], true);
                $this->dbal->commit();
            }
        }
    }

    /**
     * Mark migration as active/inactive.
     *  
     * @param int  $id     Migration id
     * @param bool $active Status
     */
    protected function markActive($id, $active = true) {
        $query = "UPDATE {$this->tableName} SET `active` = :active WHERE id = :id";

        $statement = $this->dbal->prepare($query);
        $statement->bindValue(':active', (int)$active);
        $statement->bindValue(':id', $id, \PDO::PARAM_INT);
        $statement->execute();
    }
}
