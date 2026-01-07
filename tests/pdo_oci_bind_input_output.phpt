--TEST--
PDO_OCI: Test input/output parameter binding
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
$dbh->setAttribute(PDO::ATTR_AUTOCOMMIT, false);

$sql = <<<SQL
    begin
        :p := :p + 100;
    end;
SQL;

$stmt = $dbh->prepare($sql);
$p = -1;
$stmt->bindParam(':p', $p, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT, 10);
$stmt->execute();
var_dump($p);

?>
--EXPECT--
string(2) "99"
