--TEST--
Bug #46274 (pdo_pgsql - Segfault when using PDO::ATTR_STRINGIFY_FETCHES and blob)
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
require(getenv('PDO_TEST_DIR').'/pdo_test.inc');
$db = PDOTest::test_factory(getenv('PDO_OCI_TEST_DIR').'/common.phpt');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$db->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);

$db->exec("begin
             execute immediate 'drop table test46274_2';
             exception when others then
               if sqlcode <> -942 then
                 raise;
               end if;
           end;");

$db->beginTransaction();

$db->query('CREATE TABLE test46274_2 (id INT NOT NULL, blob1 BLOB)');

$stmt = $db->prepare("INSERT INTO test46274_2 (id, blob1) VALUES (:id, EMPTY_BLOB()) RETURNING blob1 INTO :foo");

$data = 'foo';
$blob = fopen('php://memory', 'a');
fwrite($blob, $data);
rewind($blob);

$id = 1;
$stmt->bindparam(':id', $id);
$stmt->bindparam(':foo', $blob, PDO::PARAM_LOB);
$stmt->execute();

$data = '';
$blob = fopen('php://memory', 'a');
fwrite($blob, $data);
rewind($blob);

$id = 1;
$stmt->bindparam(':id', $id);
$stmt->bindparam(':foo', $blob, PDO::PARAM_LOB);
$stmt->execute();

$res = $db->query("SELECT blob1 from test46274_2");
// Resource
var_dump($row = $res->fetch());
var_dump(fread($row[0], 1024));
fclose($row[0]);

// Empty string
var_dump($row = $res->fetch());
var_dump(fread($row[0], 1024));
fclose($row[0]);

?>
--CLEAN--
<?php
require(getenv('PDO_TEST_DIR').'/pdo_test.inc');
$db = PDOTest::test_factory(getenv('PDO_OCI_TEST_DIR').'/common.phpt');
$db->exec("begin
             execute immediate 'drop table test46274_2';
             exception when others then
               if sqlcode <> -942 then
                 raise;
               end if;
           end;");
?>
--EXPECTF--
array(2) {
  ["blob1"]=>
  resource(%d) of type (stream)
  [0]=>
  resource(%d) of type (stream)
}
string(3) "foo"
array(2) {
  ["blob1"]=>
  resource(%d) of type (stream)
  [0]=>
  resource(%d) of type (stream)
}
string(0) ""
