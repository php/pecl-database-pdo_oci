--TEST--
Bug #54379 (PDO_OCI: UTF-8 output gets truncated)
--EXTENSIONS--
pdo
pdo_oci
--SKIPIF--
<?php
require(getenv('PDO_TEST_DIR').'/pdo_test.inc');
die('skip not UTF8 DSN');
PDOTest::skip();
?>
--FILE--
<?php
require_once(getenv('PDO_TEST_DIR').'/pdo_test.inc');
$db = PDOTest::test_factory(getenv('PDO_OCI_TEST_DIR').'/common.phpt');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$db->exec("begin
             execute immediate 'drop table test54379';
             exception when others then
               if sqlcode <> -942 then
                 raise;
               end if;
           end;");
$db->exec("CREATE TABLE test54379 (col1 NVARCHAR2(20))");
$db->exec("INSERT INTO test54379 VALUES('12345678901234567890')");
$db->exec("INSERT INTO test54379 VALUES('あいうえおかきくけこさしすせそたちつてと')");
$stmt = $db->prepare("SELECT * FROM test54379");
$stmt->execute();
var_dump($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
--CLEAN--
<?php
require_once(getenv('PDO_TEST_DIR').'/pdo_test.inc');
$db = PDOTest::test_factory(getenv('PDO_OCI_TEST_DIR').'/common.phpt');
$db->exec("begin
             execute immediate 'drop table test54379';
             exception when others then
               if sqlcode <> -942 then
                 raise;
               end if;
           end;");
?>
--EXPECT--
array(2) {
  [0]=>
  array(1) {
    ["col1"]=>
    string(20) "12345678901234567890"
  }
  [1]=>
  array(1) {
    ["col1"]=>
    string(60) "あいうえおかきくけこさしすせそたちつてと"
  }
}
