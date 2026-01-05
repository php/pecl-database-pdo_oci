--TEST--
PDO_OCI: Insert BOOLEAN values into BOOLEAN column (Oracle 21c+)
--EXTENSIONS--
pdo
pdo_oci
--SKIPIF--
<?php
require(getenv('PDO_TEST_DIR').'/pdo_test.inc');
$db = PDOTest::factory();

$version = $db->getAttribute(PDO::ATTR_SERVER_VERSION);
preg_match('/^(\d+)\./', $version, $matches);
$majorVersion = (int)$matches[1];
if ($majorVersion < 21) {
    die('skip BOOLEAN type supported from Oracle Database 21c');
}

PDOTest::skip();
?>
--FILE--
<?php

require_once(getenv('PDO_TEST_DIR').'/pdo_test.inc');
$db = PDOTest::factory();

// Drop table if exists (ignore error if it doesn't exist)
try {
    $db->exec("DROP TABLE test_bool");
} catch (PDOException $e) {
    // Ignore
}

// Create table
$db->exec("CREATE TABLE test_bool (id NUMBER, bool_val BOOLEAN)");

// Insert true
$stmt = $db->prepare("INSERT INTO test_bool (id, bool_val) VALUES (1, :val)");
$val = true;
$stmt->bindParam(':val', $val, PDO::PARAM_BOOL);
$stmt->execute();

// Insert false
$stmt = $db->prepare("INSERT INTO test_bool (id, bool_val) VALUES (2, :val)");
$val = false;
$stmt->bindParam(':val', $val, PDO::PARAM_BOOL);
$stmt->execute();

// Fetch and dump
$stmt = $db->query("SELECT id, bool_val FROM test_bool ORDER BY id");
var_dump($stmt->fetchAll(PDO::FETCH_ASSOC));

// Cleanup
$db->exec("DROP TABLE test_bool");

?>
--EXPECT--
array(2) {
  [0]=>
  array(2) {
    ["ID"]=>
    string(1) "1"
    ["BOOL_VAL"]=>
    string(1) "1"
  }
  [1]=>
  array(2) {
    ["ID"]=>
    string(1) "2"
    ["BOOL_VAL"]=>
    string(1) "0"
  }
}
