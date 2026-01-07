--TEST--
PDO_OCI: Integer binding test
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

$sql = 'SELECT :a a, dump(:a) a_type, :b b, dump(:b) b_type, '
  . '4 num, dump(4) num_type, \'4\' str, dump(\'4\') str_type, '
  . '(select 1 from dual where \'04\' = :a) a_where, '
  . '(select 1 from dual where \'04\' = 4) num_where '
  . 'FROM dual';
$num = 4;
$str = '4';

$statement = $conn->prepare($sql);
$statement->bindValue(':a', $num, PDO::PARAM_INT);
$statement->bindValue(':b', $str);
$statement->execute();
$row = $statement->fetch(PDO::FETCH_ASSOC);
print_r($row);
var_dump($row['a_where']);

?>
--EXPECT--
Array
(
    [a] => 4
    [a_type] => Typ=2 Len=2: 193,5
    [b] => 4
    [b_type] => Typ=1 Len=1: 52
    [num] => 4
    [num_type] => Typ=2 Len=2: 193,5
    [str] => 4
    [str_type] => Typ=96 Len=1: 52
    [a_where] => 1
    [num_where] => 1
)
string(1) "1"
