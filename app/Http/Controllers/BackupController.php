<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BackupController extends Controller
{
    public function download(): StreamedResponse
    {
        $databaseName = config('database.connections.mysql.database', 'stagedecore_db');
        $filename = 'stagedecore_backup_' . date('Y-m-d_H-i-s') . '.sql';

        return response()->streamDownload(function () use ($databaseName) {
            $handle = fopen('php://output', 'w');

            fwrite($handle, "-- Stage Decoration Booking & Management System SQL Backup\n");
            fwrite($handle, "-- Generated at: " . date('Y-m-d H:i:s') . "\n");
            fwrite($handle, "-- Database: {$databaseName}\n\n");
            fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n\n");

            // Fetch all tables
            $tables = DB::select('SHOW TABLES');
            $tableKey = 'Tables_in_' . $databaseName;

            foreach ($tables as $t) {
                $tableName = $t->$tableKey ?? current((array) $t);

                // Table structure
                $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
                $createSql = $createTable[0]->{'Create Table'} ?? '';

                fwrite($handle, "-- --------------------------------------------------------\n");
                fwrite($handle, "-- Table structure for `{$tableName}`\n");
                fwrite($handle, "-- --------------------------------------------------------\n");
                fwrite($handle, "DROP TABLE IF EXISTS `{$tableName}`;\n");
                fwrite($handle, $createSql . ";\n\n");

                // Table data
                $rows = DB::table($tableName)->get();
                if ($rows->isNotEmpty()) {
                    fwrite($handle, "-- Dumping data for `{$tableName}`\n");
                    foreach ($rows as $row) {
                        $rowArray = (array) $row;
                        $columns = array_map(fn($col) => "`{$col}`", array_keys($rowArray));
                        $values = array_map(function ($val) {
                            if (is_null($val)) {
                                return 'NULL';
                            }
                            return "'" . addslashes((string) $val) . "'";
                        }, array_values($rowArray));

                        $insertSql = "INSERT INTO `{$tableName}` (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ");\n";
                        fwrite($handle, $insertSql);
                    }
                    fwrite($handle, "\n");
                }
            }

            fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
            fwrite($handle, "-- End of backup\n");

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'application/sql',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
