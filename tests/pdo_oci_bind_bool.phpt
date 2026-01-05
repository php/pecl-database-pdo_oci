--TEST--
PDO_OCI: Bind boolean parameters
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

$conn = PDOTest::factory();

$stmt = $conn->prepare('SELECT ?, ? FROM DUAL');
$stmt->bindValue(1, true, PDO::PARAM_BOOL);
$stmt->bindValue(2, false, PDO::PARAM_BOOL);
$stmt->execute();
var_dump($stmt->fetchAll(PDO::FETCH_NUM));

?>
--EXPECT--
array(1) {
  [0]=>
  array(2) {
    [0]=>
    string(1) "1"
    [1]=>
    string(1) "0"
  }
}
