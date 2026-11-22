--TEST--
PDO_OCI: Statement garbage collection after the connection is freed
--EXTENSIONS--
pdo
pdo_oci
--SKIPIF--
<?php
require(getenv('PDO_TEST_DIR').'/pdo_test.inc');
if (!str_starts_with(getenv('PDOTEST_DSN'), 'oci:')) {
    die('skip requires an explicit oci: DSN to vary the charset');
}
PDOTest::skip();
?>
--FILE--
<?php
require_once(getenv('PDO_TEST_DIR').'/pdo_test.inc');

class GCRow {
    public PDOStatement $stmt;
}

// PDO treats a doubled semicolon inside an option value as a literal semicolon.
preg_match_all('/(?:[^;]|;;)+/', substr(getenv('PDOTEST_DSN'), 4), $matches);
$options = [];
foreach ($matches[0] as $option) {
    $option = ltrim($option);
    if ($option !== '' && !str_starts_with($option, 'charset=')) {
        $options[] = $option;
    }
}
$dsn = 'oci:' . implode(';', $options);
$privateDsn = 'oci:' . implode(';', [...$options, 'charset=AL32UTF8']);
foreach (['private' => false, 'shared' => false, 'persistent' => true] as $mode => $persistent) {
    foreach (['prepared', 'executed', 'clob', 'failed lob bind'] as $case) {
        $db = new PDO(
            $mode === 'shared' ? $dsn : $privateDsn,
            getenv('PDOTEST_USER'),
            getenv('PDOTEST_PASS'),
            [PDO::ATTR_PERSISTENT => $persistent],
        );
        $sql = match ($case) {
            'clob' => "SELECT TO_CLOB('value') FROM dual",
            'failed lob bind' => 'SELECT :value FROM pdo_oci_gc_missing_table',
            default => 'SELECT 1 FROM dual',
        };
        $stmt = $db->prepare($sql);
        if ($case === 'failed lob bind') {
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
            $stmt->bindValue(':value', '', PDO::PARAM_LOB);
            if ($stmt->execute() !== false) {
                throw new Exception('Expected execution to fail');
            }
        } elseif ($case !== 'prepared') {
            $stmt->execute();
        }

        // PDO exposes the FETCH_INTO target to GC starting with PHP 8.3.15.
        if (PHP_VERSION_ID >= 80315) {
            $row = new GCRow();
            $row->stmt = $stmt;
            $stmt->setFetchMode(PDO::FETCH_INTO, $row);
        }
        $dbRef = WeakReference::create($db);
        $stmtRef = WeakReference::create($stmt);
        unset($db, $stmt, $row);
        gc_collect_cycles();

        echo "$mode $case: ";
        var_dump($dbRef->get() === null && $stmtRef->get() === null);
    }
}

// Releasing one statement must leave the other statement's connection usable.
$db = new PDO($dsn, getenv('PDOTEST_USER'), getenv('PDOTEST_PASS'), [PDO::ATTR_PERSISTENT => true]);
$first = $db->prepare('SELECT 1 FROM dual');
$second = $db->prepare('SELECT 2 FROM dual');
unset($first, $db);
$second->execute();
var_dump((int) $second->fetchColumn());
unset($second);
?>
--EXPECT--
private prepared: bool(true)
private executed: bool(true)
private clob: bool(true)
private failed lob bind: bool(true)
shared prepared: bool(true)
shared executed: bool(true)
shared clob: bool(true)
shared failed lob bind: bool(true)
persistent prepared: bool(true)
persistent executed: bool(true)
persistent clob: bool(true)
persistent failed lob bind: bool(true)
int(2)
