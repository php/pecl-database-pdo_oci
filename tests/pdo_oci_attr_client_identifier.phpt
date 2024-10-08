--TEST--
PDO_OCI: Attribute: Setting session client identifier
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

$query = 'select client_identifier from v$session where sid = sys_context(\'USERENV\', \'SID\')';

$dbh = PDOTest::factory();

$stmt = $dbh->query($query);
$row = $stmt->fetch();
echo 'CLIENT_IDENTIFIER NOT SET: ';
var_dump($row['client_identifier']);

var_dump($dbh->setAttribute(PDO::OCI_ATTR_CLIENT_IDENTIFIER, "some client identifier"));

$stmt = $dbh->query($query);
$row = $stmt->fetch();
echo 'CLIENT_IDENTIFIER SET: ';
var_dump($row['client_identifier']);

var_dump($dbh->setAttribute(PDO::OCI_ATTR_CLIENT_IDENTIFIER, "something else!"));

$stmt = $dbh->query($query);
$row = $stmt->fetch();
echo 'CLIENT_IDENTIFIER RESET: ';
var_dump($row['client_identifier']);

var_dump($dbh->setAttribute(PDO::OCI_ATTR_CLIENT_IDENTIFIER, null));

$stmt = $dbh->query($query);
$row = $stmt->fetch();
echo 'CLIENT_IDENTIFIER NULLED: ';
var_dump($row['client_identifier']);

echo "Done\n";

?>
--EXPECT--
CLIENT_IDENTIFIER NOT SET: NULL
bool(true)
CLIENT_IDENTIFIER SET: string(22) "some client identifier"
bool(true)
CLIENT_IDENTIFIER RESET: string(15) "something else!"
bool(true)
CLIENT_IDENTIFIER NULLED: NULL
Done
