--TEST--
PDO_OCI: Re-executing a statement after closeCursor()
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

$dbh = PDOTest::factory();
$stmt = $dbh->prepare("SELECT 'A' AS id, 'B' AS val FROM dual");

$stmt->execute();
echo "first: ";
var_dump($stmt->fetchAll(PDO::FETCH_NUM));

$stmt->closeCursor();
$stmt->execute();
echo "after closeCursor: ";
var_dump($stmt->fetchAll(PDO::FETCH_NUM));

$stmt->execute();
echo "after re-execute: ";
var_dump($stmt->fetchAll(PDO::FETCH_NUM));

$dbh->setAttribute(PDO::ATTR_PREFETCH, 0);
foreach (['forward' => PDO::CURSOR_FWDONLY, 'scrollable' => PDO::CURSOR_SCROLL] as $name => $cursor) {
    echo "$name cursor:\n";
    $stmt = $dbh->prepare('SELECT :value FROM dual CONNECT BY LEVEL <= 2', [PDO::ATTR_CURSOR => $cursor]);
    var_dump($stmt->closeCursor());
    $value = 'first';
    $stmt->bindParam(':value', $value);
    $stmt->execute();
    echo $stmt->fetchColumn(), "\n";

    $value = 'second';
    $stmt->execute();
    echo $stmt->fetchColumn(), "\n";
    var_dump($stmt->closeCursor());
    var_dump($stmt->closeCursor());

    $value = 'third';
    $stmt->execute();
    echo implode(', ', $stmt->fetchAll(PDO::FETCH_COLUMN)), "\n";
}

?>
--EXPECT--
first: array(1) {
  [0]=>
  array(2) {
    [0]=>
    string(1) "A"
    [1]=>
    string(1) "B"
  }
}
after closeCursor: array(1) {
  [0]=>
  array(2) {
    [0]=>
    string(1) "A"
    [1]=>
    string(1) "B"
  }
}
after re-execute: array(1) {
  [0]=>
  array(2) {
    [0]=>
    string(1) "A"
    [1]=>
    string(1) "B"
  }
}
forward cursor:
bool(true)
first
second
bool(true)
bool(true)
third, third
scrollable cursor:
bool(true)
first
second
bool(true)
bool(true)
third, third
