<?php

/**
 * MySQL에 접속하여 결과를 배열로 가져옵니다.
 */
function mysql_get_list($query) {
	
	global $plasma_config;
	
	$conn = mysql_connect($plasma_config['mysql']['server'], $plasma_config['mysql']['username'], $plasma_config['mysql']['password']);
	mysql_select_db($plasma_config['mysql']['database'], $conn);

	$result = mysql_query($query, $conn);

	$i = 0;
	while ($row = mysql_fetch_array($result)) {
		foreach ($row as $key => $data) {
			$return[$i]->$key = $data;
		}
		$i++;
	}

	mysql_close($conn);
	
	return $return;
}