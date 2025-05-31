--TEST--
PDO OCI Bug #44301 (Segfault when an exception is thrown on persistent connections)
--EXTENSIONS--
pdo
pdo_oci
--XFAIL--
This test is currently failing in CI.
--SKIPIF--
<?php
require(getenv('PDO_TEST_DIR').'/pdo_test.inc');
PDOTest::skip();
?>
--FILE--
<?php
putenv("PDO_OCI_TEST_ATTR=" . serialize(array(PDO::ATTR_PERSISTENT => true)));
require(getenv('PDO_TEST_DIR').'/pdo_test.inc');
$db = PDOTest::test_factory(getenv('PDO_OCI_TEST_DIR').'/common.phpt');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    $stmt = $db->prepare('SELECT * FROM no_table');
    $stmt->execute();
} catch (PDOException $e) {
    print $e->getMessage();
}
$db = null;
?>
--EXPECTF--
SQLSTATE[HY000]: General error: 942 OCIStmtExecute: ORA-00942: table or view %sdoes not exist
 (%s%epdo_oci%eoci_statement.c:%d)
