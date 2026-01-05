--TEST--
PDO OCI Bug #41996 (Problem accessing Oracle ROWID)
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
$db = PDOTest::test_factory(getenv('PDO_OCI_TEST_DIR').'/common.phpt');

$stmt = $db->prepare('SELECT rowid FROM dual');
$stmt->execute();
$row = $stmt->fetch();
var_dump(strlen($row[0]) > 0);
?>
--EXPECT--
bool(true)

