<?php
page_header(lang('Table') . ": " . htmlspecialchars($_GET["table"]));

$result = mysql_query("SHOW COLUMNS FROM " . idf_escape($_GET["table"]));
if (!$result) {
	echo "<p class='error'>" . lang('Unable to show the table definition') . ": " . mysql_error() . ".</p>\n";
} else {
	echo "<table border='1' cellspacing='0' cellpadding='2'>\n";
	while ($row = mysql_fetch_assoc($result)) {
		echo "<tr><th>" . htmlspecialchars($row["Field"]) . "</th><td>$row[Type]" . ($row["Null"] == "YES" ? " <i>NULL</i>" : "") . "</td></tr>\n";
	}
	echo "</table>\n";
	mysql_free_result($result);
	echo '<p><a href="' . htmlspecialchars($SELF) . 'create=' . urlencode($_GET["table"]) . '">' . lang('Alter table') . "</a></p>\n";
	
	echo "<h3>" . lang('Indexes') . "</h3>\n";
	$indexes = indexes($_GET["table"]);
	if ($indexes) {
		echo "<table border='1' cellspacing='0' cellpadding='2'>\n";
		foreach ($indexes as $index) {
			ksort($index["columns"]);
			echo "<tr><td>$index[type]</td><td><i>" . implode("</i>, <i>", $index["columns"]) . "</i></td></tr>\n";
		}
		echo "</table>\n";
	}
	echo '<p><a href="' . htmlspecialchars($SELF) . 'indexes=' . urlencode($_GET["table"]) . '">' . lang('Alter indexes') . "</a></p>\n";
	
	$foreign_keys = foreign_keys($_GET["table"]);
	if ($foreign_keys) {
		echo "<h3>" . lang('Foreign keys') . "</h3>\n";
		echo "<table border='1' cellspacing='0' cellpadding='2'>\n";
		foreach ($foreign_keys as $foreign_key) {
			echo "<tr><td><em>" . implode("</em>, <em>", $foreign_key[2]) . "</em></td><td>" . (strlen($foreign_key[0]) && $foreign_key[0] !== $_GET["db"] ? "<strong>" . htmlspecialchars($foreign_key[0]) . "</strong>." : "") . htmlspecialchars($foreign_key[1]) . "(<em>" . implode("</em>, <em>", $foreign_key[3]) . "</em>)</td></tr>\n";
		}
		echo "</table>\n";
	}
}

if (mysql_get_server_info() >= 5) {
	$result = mysql_query("SHOW TRIGGERS LIKE '" . mysql_real_escape_string($_GET["table"]) . "'");
	if (mysql_num_rows($result)) {
		echo "<h3>" . lang('Triggers') . "</h3>\n";
		echo "<table border='0' cellspacing='0' cellpadding='2'>\n";
		while ($row = mysql_fetch_assoc($result)) {
			echo "<tr valign='top'><th>$row[Timing]</th><th>$row[Event]</th><td><pre>" . htmlspecialchars($row["Statement"]) . "</pre></td></tr>\n";
		}
		echo "</table>\n";
	}
	mysql_free_result($result);
}
