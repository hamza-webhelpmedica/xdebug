--TEST--
Test for bug #1247: xdebug.show_local_vars dumps variables with *uninitialized* values (< PHP 7.2 || no opcache)
--SKIPIF--
<?php
require __DIR__ . '/../utils.inc';
if ( ! ( runtime_version('7.2', '<') || !opcache_active() ) ) {
	echo "skip < PHP 7.2 || !opcache loaded needed\n";
}
?>
--INI--
xdebug.default_enable=1
xdebug.collect_params=4
html_errors=0
xdebug.dump.GET=
xdebug.dump.POST=
xdebug.show_local_vars=1
--GET--
getFoo=bar
--POST--
postBar=baz
--FILE--
<?php
function test()
{
	$a = 42;
	hex2bin("4");
	$b = 4;

	return $a + $b;
}

function testDirect()
{
	$c = 56;
	trigger_error('test');
	$d = 11;

	return $c + $d;
}

test();
testDirect();
?>
--EXPECTF--
Warning: hex2bin(): %s in %sbug01247.php on line 5

Call Stack:
%w%f %w%d   1. {main}() %sbug01247.php:0
%w%f %w%d   2. test() %sbug01247.php:20
%w%f %w%d   3. hex2bin('4') %sbug01247.php:5


Variables in local scope (#2):
  $a = 42
  $b = *uninitialized*


Notice: test in %sbug01247.php on line 14

Call Stack:
%w%f %w%d   1. {main}() %sbug01247.php:0
%w%f %w%d   2. testDirect() %sbug01247.php:21
%w%f %w%d   3. trigger_error('test') %sbug01247.php:14


Variables in local scope (#2):
  $c = 56
  $d = *uninitialized*
