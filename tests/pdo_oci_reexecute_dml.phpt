--TEST--
PDO_OCI: Re-executing non-query statements with and without closeCursor()
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
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$dbh->exec('CREATE TABLE test_reexecute_dml (id NUMBER PRIMARY KEY, name VARCHAR2(20))');

$insert = $dbh->prepare('INSERT INTO test_reexecute_dml (id, name) VALUES (:id, :name)');
$insert->execute([':id' => 1, ':name' => 'a']);
$insert->execute([':id' => 2, ':name' => 'b']);
$insert->closeCursor();
$insert->execute([':id' => 3, ':name' => 'c']);

$update = $dbh->prepare('UPDATE test_reexecute_dml SET name = :name WHERE id = :id');
$update->execute([':id' => 1, ':name' => 'a1']);
$update->execute([':id' => 2, ':name' => 'b1']);
$update->closeCursor();
$update->execute([':id' => 2, ':name' => 'b2']);
var_dump($update->rowCount());

$alter = $dbh->prepare("ALTER SESSION SET NLS_DATE_FORMAT = 'YYYY-MM-DD'");
$alter->execute();
$alter->execute();
$alter->closeCursor();
$alter->execute();
echo "alter session: ok\n";

$block = $dbh->prepare('BEGIN :value := :value + 1; END;');
$value = 0;
$block->bindParam(':value', $value, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT, 10);
$block->execute();
$block->execute();
$block->closeCursor();
$block->execute();
echo "PL/SQL: $value\n";

$select = $dbh->prepare('SELECT name FROM test_reexecute_dml WHERE id = :id');
foreach ([1, 2, 3] as $id) {
    $select->execute([':id' => $id]);
    echo $select->fetchColumn(), "\n";
}

?>
--CLEAN--
<?php
require_once(getenv('PDO_TEST_DIR').'/pdo_test.inc');
$dbh = PDOTest::factory();
$dbh->exec('DROP TABLE test_reexecute_dml');
?>
--EXPECT--
int(1)
alter session: ok
PL/SQL: 3
a1
b2
c
