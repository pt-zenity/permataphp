<?php
/**
 * objData - Database abstraction layer
 * Pure PHP PDO wrapper
 */
defined('main') or die('Restricted access');

class objData
{
    private static ?PDO $pdo = null;

    public static function Connect(
        string $host,
        string $user,
        string $pass,
        string $database,
        int    $port = 3306
    ): void {
        try {
            $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";
            self::$pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            // Jangan expose detail koneksi ke output
            error_log('DB Connect Error: ' . $e->getMessage());
            // Lanjutkan tanpa koneksi (beberapa endpoint tidak perlu DB langsung)
        }
    }

    public static function getPDO(): ?PDO
    {
        return self::$pdo;
    }

    /**
     * Browse / SELECT
     * @param string $table    nama tabel (bisa include JOIN: "agen a left join ...")
     * @param string $columns  kolom yang diambil
     * @param string $where    kondisi WHERE (raw string, bukan binding)
     * @param array  $joins    array string JOIN tambahan (unused, untuk kompatibilitas)
     * @param string $groupby  GROUP BY clause
     * @param string $orderby  ORDER BY clause
     * @param int    $limit    LIMIT
     * @return array           array of rows
     */
    public static function Browse(
        string $table,
        string $columns = '*',
        string $where   = '',
        mixed  $joins   = '',
        string $groupby = '',
        string $orderby = '',
        int    $limit   = 0
    ): array {
        if (self::$pdo === null) return [];

        $sql = "SELECT {$columns} FROM {$table}";

        // tambahkan JOIN jika ada (format lama: string atau array)
        if (!empty($joins)) {
            if (is_array($joins)) {
                $sql .= ' ' . implode(' ', $joins);
            } else {
                $sql .= ' ' . $joins;
            }
        }

        if (!empty($where))   $sql .= " WHERE {$where}";
        if (!empty($groupby)) $sql .= " GROUP BY {$groupby}";
        if (!empty($orderby)) $sql .= " ORDER BY {$orderby}";
        if ($limit > 0)       $sql .= " LIMIT {$limit}";

        try {
            $stmt = self::$pdo->query($sql);
            return $stmt ? $stmt->fetchAll() : [];
        } catch (PDOException $e) {
            error_log('DB Browse Error: ' . $e->getMessage() . ' | SQL: ' . $sql);
            return [];
        }
    }

    /**
     * GetRow - Ambil satu baris dari hasil Browse
     * Kompatibel dengan pola while($row = objData::GetRow($db))
     */
    private static array $cursors = [];

    public static function GetRow(array &$rows): array|false
    {
        if (empty($rows)) return false;
        $row = array_shift($rows);
        return $row !== null ? $row : false;
    }

    /**
     * Rows - Hitung jumlah baris
     */
    public static function Rows(array $rows): int
    {
        return count($rows);
    }

    /**
     * Insert
     */
    public static function Insert(string $table, array $data): int|false
    {
        if (self::$pdo === null || empty($data)) return false;

        $cols   = implode(', ', array_keys($data));
        $places = implode(', ', array_fill(0, count($data), '?'));
        $sql    = "INSERT INTO {$table} ({$cols}) VALUES ({$places})";

        try {
            $stmt = self::$pdo->prepare($sql);
            $stmt->execute(array_values($data));
            return (int) self::$pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log('DB Insert Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update - INSERT jika tidak ada, UPDATE jika sudah ada
     * (sesuai perilaku asli: upsert-style)
     */
    public static function Update(string $table, array $data, string $where = ''): bool
    {
        if (self::$pdo === null || empty($data)) return false;

        if (!empty($where)) {
            // Cek apakah baris sudah ada
            $checkSql = "SELECT 1 FROM {$table} WHERE {$where} LIMIT 1";
            try {
                $checkStmt = self::$pdo->query($checkSql);
                $exists    = $checkStmt && $checkStmt->fetchColumn();
            } catch (PDOException $e) {
                $exists = false;
            }

            if ($exists) {
                // UPDATE
                $sets = implode(', ', array_map(fn($k) => "{$k} = ?", array_keys($data)));
                $sql  = "UPDATE {$table} SET {$sets} WHERE {$where}";
            } else {
                // INSERT
                $cols   = implode(', ', array_keys($data));
                $places = implode(', ', array_fill(0, count($data), '?'));
                $sql    = "INSERT INTO {$table} ({$cols}) VALUES ({$places})";
            }
        } else {
            // Tanpa where: selalu INSERT
            $cols   = implode(', ', array_keys($data));
            $places = implode(', ', array_fill(0, count($data), '?'));
            $sql    = "INSERT INTO {$table} ({$cols}) VALUES ({$places})";
        }

        try {
            $stmt = self::$pdo->prepare($sql);
            return $stmt->execute(array_values($data));
        } catch (PDOException $e) {
            error_log('DB Update Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Execute raw SQL (untuk migration, dll)
     */
    public static function Execute(string $sql): bool
    {
        if (self::$pdo === null) return false;
        try {
            self::$pdo->exec($sql);
            return true;
        } catch (PDOException $e) {
            error_log('DB Execute Error: ' . $e->getMessage());
            return false;
        }
    }
}
