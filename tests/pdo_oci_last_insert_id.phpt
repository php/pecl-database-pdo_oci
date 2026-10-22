--TEST--
PDO_OCI: lastInsertId() returns CURRVAL of a sequence
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
$db = PDOTest::factory();
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$db->exec("begin
             execute immediate 'drop sequence test_pdo_oci_last_insert_id';
             exception when others then
               if sqlcode <> -2289 then
                 raise;
               end if;
           end;");
$db->exec("create sequence test_pdo_oci_last_insert_id start with 42");

function last_id(PDO $db, ?string $name) {
    try {
        var_dump($db->lastInsertId($name));
    } catch (PDOException $e) {
        preg_match('~ORA-\d+~', $e->getMessage(), $match);
        echo trim($e->getCode() . " " . ($match[0] ?? "")) . "\n";
    }
}

echo "Without a name:\n";
last_id($db, null);

echo "Before NEXTVAL:\n";
last_id($db, 'test_pdo_oci_last_insert_id');

$db->query("select test_pdo_oci_last_insert_id.nextval from dual")->fetchAll();

echo "After NEXTVAL:\n";
last_id($db, 'test_pdo_oci_last_insert_id');
last_id($db, 'TEST_PDO_OCI_LAST_INSERT_ID');
last_id($db, '"TEST_PDO_OCI_LAST_INSERT_ID"');
$schema = $db->query("select sys_context('USERENV', 'CURRENT_SCHEMA') from dual")->fetchColumn();
last_id($db, "$schema.test_pdo_oci_last_insert_id");

echo "Not a name:\n";
last_id($db, 'test_pdo_oci_last_insert_id.currval + 1 from dual --');
last_id($db, 'no_such_sequence');

echo "Silent:\n";
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
var_dump($db->lastInsertId());
var_dump($db->errorCode());

$db->exec("drop sequence test_pdo_oci_last_insert_id");

?>
--EXPECT--
Without a name:
IM001
Before NEXTVAL:
HY000 ORA-08002
After NEXTVAL:
string(2) "42"
string(2) "42"
string(2) "42"
string(2) "42"
Not a name:
HY000 ORA-44004
HY000 ORA-02289
Silent:
bool(false)
string(5) "IM001"
