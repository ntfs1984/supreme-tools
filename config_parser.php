/*
This tool allows work with HTML-style config file, with some differences of HTML and XML
Only one-level configs like "key" = "value"
Tag called as parameter name
Why not classic INI ?
Because this method allows to use some big raw_data between tags.
Usage:
Let's say we have .conf file:
<parameter1>Value1</parameter1>
<parameter2>Value2</parameter2>
<parameter3>Value3</parameter3>

$array = read_config_to_array(PATH_TO_FILE); - Will return key-based array with keys and their values
$array = read_config_to_array(PATH_TO_FILE, "parameter1"); - Will return the same key-based array, but will stop, when match first parameter. This can save some time in a case of big file.
$parameter = read_parameter(PATH_TO_FILE, "parameter2"); - Will return string with value. "Value2" in our case.
write_parameter(PATH_TO_FILE,"parameter2","abcxyz"); - Will replace\add parameter and at it to config. Please pay attention, there is also writing <last_updated> parameter, you can easily comment this line, otherwise also unixtime of operation will saved.
*/
  
<?php
function read_config_to_array($path, $seek_for_first="") {
	$c = file_get_contents($path);
	$read_value=false;
	$read_tag=false;
	$arr = array();
	for ($i = 0; $i <= strlen($c)-1; $i++) {
		if (($c[$i]=="<") and ($c[$i+1]!="/")) {
			$tagger = true;$valuer=false;$tag="";
			}
		if (($c[$i]==">") and ($tagger==true)) {
			$tagger=false;$valuer=true;$value="";
			}
		if (($valuer==true) and ($c[$i]=="<") and ($c[$i+1]=="/")) {
			$valuer=false;$arr[$tag]=$value;
			if (($seek_for_first!="")and($tag==$seek_for_first)) {
				return $arr;
				}
			}	
	if (($tagger==true)and($c[$i]!="<")) {
		$tag.=$c[$i];
		}
	if (($valuer==true)and($c[$i]!=">")) {
	$value.=$c[$i];
		}
	}
return $arr;	
}

function read_parameter($path, $parameter_name) {
	$arr = read_config_to_array($path, $parameter_name);
	return $arr[$parameter_name];
}

function write_parameter($path, $parameter_name, $parameter_value) {
	$arr = read_config_to_array($path);
	$arr[$parameter_name]=$parameter_value;
	$arr["last_updated"]=time();
	$out="";
	foreach ($arr as $key => $value) {
		$out.="<$key>$value</$key>\n";	
	}
	file_put_contents($path, $out);
}
