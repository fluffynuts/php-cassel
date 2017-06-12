<html>
<head>
<title>N-Tier cascading select test page</title>
<style>
select {
	width: 200px;
}
h3 {
	text-align: center;
}
</style>
</head>
<body>
<h3>N-Tier cascading select test page</h3>
<table border="0">
<tr><td>
This select:</td></tr><tr><td>
<?
include_once("cassel.php");
// parent
$sel = new Cassel(array(
	"id" => "parent",
	"cascades" => array(
					1 => array(
						"one" => array(
							"child0" => array(
								"101" => "one-zero-one",
								"102" => "one-zero-two",
								"103" => "one-zero-three",
								),
							"child1" => array(
								"111" => "one-one-one",
								"112" => "one-one-two",
								"113" => "one-one-three",
								),
							),
						),
					2 => "two",
					3 => array(
						"three" => array(
							"child0" => array(
								"201" => "two-zero-one",
								"202" => "tow-zero-two",
								),
							"child1" => array(
								"213" => "two-one-three",
								"214" => "two-one-four",
								"215" => "two-one-five",
								),
							),
						),
					4 => "four",
					"__default__" => array(
						"child0" => array(
								"d01" => "default-zero-one",
								"d02" => "default-zero-two",
								"d03" => "default-zero-three",
							),
						"child1" => array(
								"d14" => "default-one-four",
								"d15" => "default-one-five",
							),
						),
		),
	"defaults" => array(
			1	=>	array(
						"child0"	=>	"103",
					),
			2	=>	array(
						"child1"	=>	"112",
					),
		),
	));
	$sel->render();
?>
</td><td>
 controls this one:
</td></tr>
<tr><td></td><td>
<select id="child0">
	<option value="foo">Foo</option>
</select>
</td></tr>
<tr><td></td><td>
and this one:
</td></tr>
<tr><td></td><td>
<?
	$sel = new Cassel(array(
		"id"	=>	"child1",
		"cascades" => array(
			1 => "child-one",
			2 => "child-two",
			"213" => array(
					"two-one-three" => array(
						"child2" => array(
							"c2131" => "c-213-one",
							"c2132" => "c-213-two",
						),
					),
				),
			"__default__" => array(
				"child2" => array(
					"cd01" => "child2-default-01",
					"cd02" => "child2-default-02",
					"cd03" => "child2-default-03",
				),
			),
		),
	));
	$sel->render();
?>
</td><td>which controls this one:</td></tr>
<tr><td></td><td></td><td>
<select id="child2">
	<option value="bar">Bar</option>
	<option value="quux">Quux</option>
</select>
</td></tr>
</table>
</body>
</html>
