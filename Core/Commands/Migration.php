<?php

namespace Core\Commands;

use Core\Model;

class Migration
{
    const DATABASE_DIR = __DIR__ . "/../../Database/";

    public static function create()
    {
        Command::yellow('Enter Migration Name: ');
        $name = strtolower(trim(fgets(STDIN)));

        if (empty($name)) exit("Migration name cannot be empty.\n");

        if (str_ends_with($name, '_table')):
            $name = substr($name, 0, -6);
        elseif (str_ends_with($name, '_tables')):
            $name = substr($name, 0, -7);
        elseif (str_ends_with($name, 'table')):
            $name = substr($name, 0, -5);
        elseif (str_ends_with($name, 'tables')):
            $name = substr($name, 0, -6);
        endif;


        $timestamp = time();
        $filename = self::DATABASE_DIR . "{$timestamp}_{$name}_table.php";

        $files = scandir(self::DATABASE_DIR);
        $files = array_diff($files, ['.', '..']);
        foreach ($files as $file):
            $fileName = explode('_', $file, 2);
            if ($fileName[1] === $name . '_table.php') {
                Command::red("Migration already exists.\n");
                exit();
            }
        endforeach;

        file_put_contents($filename, "<?php\n\nreturn [\n    'name' => '$name',\n    'columns' => [\n        'id INT AUTO_INCREMENT PRIMARY KEY',\n        // Define your columns here \n        'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP'\n    ]\n];");

        Command::magenta("\n\nMigration created successfully.\n");
        Command::magenta("\nMigration file: $filename\n\n");
    }

    public static function run()
    {
        $model = new Model();
        // create migration table if not exists
        self::createMigrations();
        $executed_migrations = $model->SelectRow("SELECT migration_name FROM migrations ORDER BY id DESC");
        $files = scandir(self::DATABASE_DIR);

        $files = array_diff($files, ['.', '..', ...array_column($executed_migrations, 'migration_name')]);
        foreach ($files as $file):
            $fileName = explode('_', $file, 2);
            $sql = require self::DATABASE_DIR . $file;

            if (!isset($sql['name']) || !isset($sql['columns'])):
                Command::red("Invalid migration file: $fileName[1]\n");
                exit();
            endif;
        endforeach;


        $batch = $model->SelectRow("SELECT MAX(batch) as batch FROM migrations");
        $batchNo = (int)$batch[0]['batch'] + 1;

        foreach ($files as $file):
            $fileName = explode('_', $file, 2);
            $sql = require self::DATABASE_DIR . $file;

            if ($model->SelectRow("SHOW TABLES LIKE '{$sql['name']}'")) {
                return false;
            }

            $sql = "CREATE TABLE {$sql['name']}\n(" . implode(',', $sql['columns']) . ") ENGINE=InnoDB DEFAULT CHARSET=utf8;";

            if ($model->InsertRow($sql)):
                $model->InsertRow("INSERT INTO migrations (migration_name, batch) VALUES (?, ?)", [$file, $batchNo]);
                Command::yellow("\nRunning migration: $fileName[1]\n");
                Command::green("Migration $fileName[1] ran successfully.\n\n");
            else:
                Command::red("Migration $fileName[1] failed. table already exists.\n\n");
            endif;
        endforeach;
    }

    public static function rollback(): void
    {
        self::createMigrations();
        // get max batch number
        $model = new Model();
        $batch = $model->SelectRow("SELECT MAX(batch) as batch FROM migrations");
        $batchNo = (int)$batch[0]['batch'];

        if ($batchNo === 0) {
            Command::red("No migrations to rollback.\n");
            exit();
        }

        $migrations = $model->SelectRow("SELECT migration_name FROM migrations WHERE batch = ?", [$batchNo]);

        foreach ($migrations as $migration):
            $sql = require self::DATABASE_DIR . $migration['migration_name'];
            $sql = "DROP TABLE {$sql['name']};";
            if ($model->InsertRow($sql)):
                $model->InsertRow("DELETE FROM migrations WHERE migration_name = ?", [$migration['migration_name']]);
                Command::yellow("\nRolling back migration: {$migration['migration_name']}\n");
                Command::green("Migration {$migration['migration_name']} rolled back successfully.\n\n");
            else:
                Command::red("Migration {$migration['migration_name']} failed to rollback.\n\n");
            endif;
        endforeach;

    }

    public static function refresh()
    {
        self::createMigrations();

        $model = new Model();
        while ((int)$model->SelectRow("SELECT MAX(batch) as batch FROM migrations")[0]['batch'] > 0) {
            self::rollback();
        }

        self::run();
    }


    private static function createMigrations()
    {
        $data = [
            'name' => 'migrations',
            'columns' => [
                'id INT AUTO_INCREMENT PRIMARY KEY',
                'migration_name VARCHAR(255) NOT NULL',
                'batch INT NOT NULL',
                'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
            ]
        ];

        (new model)->InsertRow("CREATE TABLE IF NOT EXISTS {$data['name']}\n(" . implode(',', $data['columns']) . ") ENGINE=InnoDB DEFAULT CHARSET=utf8;");

    }
}