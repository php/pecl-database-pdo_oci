--TEST--
PDO OCI Bug #60994 (Reading a multibyte CLOB caps at 8192 characters)
--CREDITS--
Chuck Burgess
ashnazg@php.net
--EXTENSIONS--
mbstring
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
$dbh = PDOTest::factory();
$dbh->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
$dbh->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);

$dbh->exec("begin
              execute immediate 'drop table pdo_oci_bug60994';
              exception when others then
                if sqlcode <> -942 then
                  raise;
                end if;
            end;");
$dbh->exec('CREATE TABLE pdo_oci_bug60994 (id NUMBER, data CLOB, data2 NCLOB)');

$id = null;
$insert = $dbh->prepare('INSERT INTO pdo_oci_bug60994 (id, data, data2) VALUES (:id, :data, :data2)');
$insert->bindParam(':id', $id, \PDO::PARAM_STR);
$select = $dbh->prepare("SELECT data, data2 FROM pdo_oci_bug60994 WHERE id = :id");


echo PHP_EOL, 'Test 1:  j', PHP_EOL;
$string1 = 'abc' . str_repeat('j', 8187) . 'xyz'; // 8193 chars total works fine here (even 1 million works fine, subject to memory_limit)
$id = 1;
$insert->bindParam(':data', $string1, \PDO::PARAM_STR, strlen($string1)); // length in bytes
$insert->bindParam(':data2', $string1, \PDO::PARAM_STR, strlen($string1));
$insert->execute();
$select->bindParam(':id', $id, \PDO::PARAM_STR);
$select->execute();
$row = $select->fetch();
$stream1 = stream_get_contents($row['DATA']);
$start1  = mb_substr($stream1, 0, 10);
$ending1 = mb_substr($stream1, -10);
echo 'size of string1 is ', strlen($string1), ' bytes, ', mb_strlen($string1), ' chars.', PHP_EOL;
echo 'size of stream1 is ', strlen($stream1), ' bytes, ', mb_strlen($stream1), ' chars.', PHP_EOL;
echo 'beg  of stream1 is ', $start1, PHP_EOL;
echo 'end  of stream1 is ', $ending1, PHP_EOL;
if ($string1 != $stream1 || $stream1 != stream_get_contents($row['DATA2'])) {
    echo 'Expected nclob value to match clob value for stream1', PHP_EOL;
}

echo PHP_EOL, 'Test 2:  £', PHP_EOL;
$string2 = 'abc' . str_repeat('£', 8187) . 'xyz'; // 8193 chars total is when it breaks
$id = 2;
$insert->bindParam(':data', $string2, \PDO::PARAM_STR, strlen($string2)); // length in bytes
$insert->bindParam(':data2', $string2, \PDO::PARAM_STR, strlen($string2));
$insert->execute();
$select->bindParam(':id', $id, \PDO::PARAM_STR);
$select->execute();
$row = $select->fetch();
$stream2 = stream_get_contents($row['DATA']);
$start2  = mb_substr($stream2, 0, 10);
$ending2 = mb_substr($stream2, -10);
echo 'size of string2 is ', strlen($string2), ' bytes, ', mb_strlen($string2), ' chars.', PHP_EOL;
echo 'size of stream2 is ', strlen($stream2), ' bytes, ', mb_strlen($stream2), ' chars.', PHP_EOL;
echo 'beg  of stream2 is ', $start2, PHP_EOL;
echo 'end  of stream2 is ', $ending2, PHP_EOL;
if ($string2 != $stream2 || $stream2 != stream_get_contents($row['DATA2'])) {
    echo 'Expected nclob value to match clob value for stream2', PHP_EOL;
}

echo PHP_EOL, 'Test 3:  Җ', PHP_EOL;
$string3 = 'abc' . str_repeat('Җ', 8187) . 'xyz'; // 8193 chars total is when it breaks
$id = 3;
$insert->bindParam(':data', $string3, \PDO::PARAM_STR, strlen($string3)); // length in bytes
$insert->bindParam(':data2', $string3, \PDO::PARAM_STR, strlen($string3));
$insert->execute();
$select->bindParam(':id', $id, \PDO::PARAM_STR);
$select->execute();
$row = $select->fetch();
$stream3 = stream_get_contents($row['DATA']);
$start3  = mb_substr($stream3, 0, 10);
$ending3 = mb_substr($stream3, -10);
echo 'size of string3 is ', strlen($string3), ' bytes, ', mb_strlen($string3), ' chars.', PHP_EOL;
echo 'size of stream3 is ', strlen($stream3), ' bytes, ', mb_strlen($stream3), ' chars.', PHP_EOL;
echo 'beg  of stream3 is ', $start3, PHP_EOL;
echo 'end  of stream3 is ', $ending3, PHP_EOL;
if ($string3 != $stream3 || $stream3 != stream_get_contents($row['DATA2'])) {
    echo 'Expected nclob value to match clob value for stream3', PHP_EOL;
}

echo PHP_EOL, 'Test 4:  の', PHP_EOL;
$string4 = 'abc' . str_repeat('の', 8187) . 'xyz'; // 8193 chars total is when it breaks
$id = 4;
$insert->bindParam(':data', $string4, \PDO::PARAM_STR, strlen($string4)); // length in bytes
$insert->bindParam(':data2', $string4, \PDO::PARAM_STR, strlen($string4));
$insert->execute();
$select->bindParam(':id', $id, \PDO::PARAM_STR);
$select->execute();
$row = $select->fetch();
$stream4 = stream_get_contents($row['DATA']);
$start4  = mb_substr($stream4, 0, 10);
$ending4 = mb_substr($stream4, -10);
echo 'size of string4 is ', strlen($string4), ' bytes, ', mb_strlen($string4), ' chars.', PHP_EOL;
echo 'size of stream4 is ', strlen($stream4), ' bytes, ', mb_strlen($stream4), ' chars.', PHP_EOL;
echo 'beg  of stream4 is ', $start4, PHP_EOL;
echo 'end  of stream4 is ', $ending4, PHP_EOL;
if ($string4 != $stream4 || $stream4 != stream_get_contents($row['DATA2'])) {
    echo 'Expected nclob value to match clob value for stream4', PHP_EOL;
}
?>
--CLEAN--
<?php
require(getenv('PDO_TEST_DIR').'/pdo_test.inc');
$db = PDOTest::test_factory(getenv('PDO_OCI_TEST_DIR').'/common.phpt');
$db->exec("begin
             execute immediate 'drop table pdo_oci_bug60994';
             exception when others then
               if sqlcode <> -942 then
                 raise;
               end if;
           end;");
?>
--EXPECT--
Test 1:  j
size of string1 is 8193 bytes, 8193 chars.
size of stream1 is 8193 bytes, 8193 chars.
beg  of stream1 is abcjjjjjjj
end  of stream1 is jjjjjjjxyz

Test 2:  £
size of string2 is 16380 bytes, 8193 chars.
size of stream2 is 16380 bytes, 8193 chars.
beg  of stream2 is abc£££££££
end  of stream2 is £££££££xyz

Test 3:  Җ
size of string3 is 16380 bytes, 8193 chars.
size of stream3 is 16380 bytes, 8193 chars.
beg  of stream3 is abcҖҖҖҖҖҖҖ
end  of stream3 is ҖҖҖҖҖҖҖxyz

Test 4:  の
size of string4 is 24567 bytes, 8193 chars.
size of stream4 is 24567 bytes, 8193 chars.
beg  of stream4 is abcののののののの
end  of stream4 is のののののののxyz
