--TEST--
PDO_OCI: Attribute: closing a connection in non-autocommit mode commits data
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

// Check connection can be created with AUTOCOMMIT off
putenv('PDOTEST_ATTR='.serialize(array(PDO::ATTR_AUTOCOMMIT=>false)));
$dbh = PDOTest::factory();

$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

print "PDO::ATTR_AUTOCOMMIT: ";
var_dump($dbh->getAttribute(PDO::ATTR_AUTOCOMMIT));

echo "Insert data\n";

$dbh->exec("begin
              execute immediate 'drop table test_pdo_oci_attr_autocommit_3';
              exception when others then
                if sqlcode <> -942 then
                  raise;
                end if;
            end;");

$dbh->exec("create table test_pdo_oci_attr_autocommit_3 (col1 varchar2(20))");

$dbh->exec("insert into test_pdo_oci_attr_autocommit_3 (col1) values ('some data')");

$dbh = null; // close first connection

echo "Second connection should be able to see committed data\n";
$dbh2 = PDOTest::factory();
$s = $dbh2->prepare("select col1 from test_pdo_oci_attr_autocommit_3");
$s->execute();
while ($r = $s->fetch()) {
    echo "Data is: " . $r[0] . "\n";
}

echo "Done\n";

?>
--CLEAN--
<?php
require(getenv('PDO_TEST_DIR').'/pdo_test.inc');
$db = PDOTest::test_factory(getenv('PDO_OCI_TEST_DIR').'/common.phpt');
$db->exec("begin
             execute immediate 'drop table test_pdo_oci_attr_autocommit_3';
             exception when others then
               if sqlcode <> -942 then
                 raise;
               end if;
           end;");
?>
--EXPECT--
PDO::ATTR_AUTOCOMMIT: bool(false)
Insert data
Second connection should be able to see committed data
Done
