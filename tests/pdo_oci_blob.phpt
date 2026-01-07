--TEST--
PDO_OCI: BLOB insert and fetch test
--EXTENSIONS--
pdo
pdo_oci
--SKIPIF--
<?php
require(getenv('PDO_TEST_DIR').'/pdo_test.inc');
PDOTest::skip();
?>
--FILE--
<?php
require_once(getenv('PDO_TEST_DIR').'/pdo_test.inc');

$db = PDOTest::factory();
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->setAttribute(PDO::ATTR_AUTOCOMMIT, false);

try {
    $db->exec("DROP TABLE test_blob");
} catch (PDOException $e) {
    // Table may not exist, ignore
}
// Create table
$db->exec("CREATE TABLE test_blob (id NUMBER, picture BLOB)");

for ($id = 1; $id < 3; $id++) {
    $db->beginTransaction();

    // Sample image data (replace with actual paths or data)
    // For testing, we'll use dummy data
    $imageData = file_get_contents(dirname(__FILE__) .'/test' . $id . '.gif');

    // Insert and bind output LOB
    $lob = null;
    $stmt = $db->prepare("INSERT INTO test_blob (id, picture) VALUES (:id, EMPTY_BLOB()) RETURNING picture INTO :picture");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':picture', $lob, PDO::PARAM_LOB);
    $stmt->execute();

    if (is_resource($lob)) {
        fwrite($lob, $imageData);
        fclose($lob);
    }

    $db->commit();
}

// Select and fetch BLOB using fetchAll
echo "Using fetchAll:\n";
$stm = $db->query("SELECT picture FROM test_blob");
foreach ($stm->fetchAll(PDO::FETCH_NUM) as $row) {
    if (is_resource($row[0])) {
        rewind($row[0]);
        $blob = stream_get_contents($row[0]);
    } else {
        $blob = $row[0];
    }
    echo "Blob length: " . strlen($blob) . "\n";
}

echo "-----------------------------------\n";

// Select and fetch BLOB using fetch in loop
echo "Using fetch in loop:\n";
$stmt = $db->query("SELECT picture FROM test_blob");
while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
    if (is_resource($row[0])) {
        rewind($row[0]);
        $blob = stream_get_contents($row[0]);
    } else {
        $blob = $row[0];
    }
    echo "Blob length: " . strlen($blob) . "\n";
}
?>
--CLEAN--
<?php
require_once(getenv('PDO_TEST_DIR').'/pdo_test.inc');
$db = PDOTest::test_factory(getenv('PDO_OCI_TEST_DIR').'/common.phpt');
$db->exec("begin
             execute immediate 'drop table test_blob';
             exception when others then
               if sqlcode <> -942 then
                 raise;
               end if;
           end;");
?>
--EXPECT--
Using fetchAll:
Blob length: 2523
Blob length: 35
-----------------------------------
Using fetch in loop:
Blob length: 2523
Blob length: 35
