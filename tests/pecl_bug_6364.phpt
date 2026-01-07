--TEST--
PECL PDO_OCI Bug #6364 (segmentation fault on stored procedure call with OUT binds)
--EXTENSIONS--
pdo
pdo_oci
--SKIPIF--
<?php
if (getenv('SKIP_ASAN')) die('xleak leaks memory under asan');
require(getenv('PDO_TEST_DIR').'/pdo_test.inc');
PDOTest::skip();
?>
--FILE--
<?php

require_once(getenv('PDO_TEST_DIR').'/pdo_test.inc');
$dbh = PDOTest::factory();

$dbh->exec("begin
              execute immediate 'drop table test6364';
              exception when others then
                if sqlcode <> -942 then
                  raise;
                end if;
            end;");

$dbh->exec ("create table test6364 (c1 varchar2(10), c2 varchar2(10), c3 varchar2(10), c4 varchar2(10), c5 varchar2(10))");

$dbh->exec ("create or replace procedure test6364_sp(p1 IN varchar2, p2 IN varchar2, p3 IN varchar2, p4 OUT varchar2, p5 OUT varchar2) as begin insert into test6364 (c1, c2, c3) values (p1, p2, p3); p4 := 'val4'; p5 := 'val5'; end;");

$stmt = $dbh->prepare("call test6364_sp('p1','p2','p3',?,?)");

$out_param1 = "a";
$out_param2 = "a";

$stmt->bindParam(1, $out_param1,PDO::PARAM_STR, 1024);
$stmt->bindParam(2, $out_param2,PDO::PARAM_STR, 1024);

$stmt->execute() or die ("Execution error: " . var_dump($dbh->errorInfo()));

var_dump($out_param1);
var_dump($out_param2);

foreach ($dbh->query("select * from test6364") as $row) {
    var_dump($row);
}

print "Done\n";
?>
--CLEAN--
<?php
require_once(getenv('PDO_TEST_DIR').'/pdo_test.inc');
$db = PDOTest::test_factory(getenv('PDO_OCI_TEST_DIR').'/common.phpt');
$db->exec("begin
             execute immediate 'drop table test6364';
             exception when others then
               if sqlcode <> -942 then
                 raise;
               end if;
           end;");
$db->exec("DROP PROCEDURE test6364_sp");
?>
--EXPECT--
string(4) "val4"
string(4) "val5"
array(10) {
  ["c1"]=>
  string(2) "p1"
  [0]=>
  string(2) "p1"
  ["c2"]=>
  string(2) "p2"
  [1]=>
  string(2) "p2"
  ["c3"]=>
  string(2) "p3"
  [2]=>
  string(2) "p3"
  ["c4"]=>
  NULL
  [3]=>
  NULL
  ["c5"]=>
  NULL
  [4]=>
  NULL
}
Done
