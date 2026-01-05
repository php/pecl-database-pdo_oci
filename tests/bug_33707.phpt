--TEST--
PDO OCI Bug #33707 (Errors in select statements not reported)
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
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);

$rs = $db->query('select blah from a_table_that_does_not_exist');
var_dump($rs);
var_dump($db->errorInfo());
?>
--EXPECTF--
bool(false)
array(3) {
  [0]=>
  string(5) "HY000"
  [1]=>
  int(942)
  [2]=>
  string(%d) "OCIStmtExecute: ORA-00942: table or view %Sdoes not exist
 (%s:%d)"
}

