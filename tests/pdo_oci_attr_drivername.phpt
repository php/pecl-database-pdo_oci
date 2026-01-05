--TEST--
PDO_OCI: Attribute: verify driver name
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
var_dump($dbh->getAttribute(PDO::ATTR_DRIVER_NAME));

echo "Done\n";
?>
--EXPECT--
string(3) "oci"
Done

