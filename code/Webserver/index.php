<?php
$page_content_1 = "<html>
<head>
<meta http-equiv='content-type' content='text/html; charset=utf-8'></meta>
</head>
<body>
<div id='container' align='center' width=* height=*>
<div id='input' border='1px' border-color='black' border-radius='6px';>
<form method='get' action='index.php'>
<input type='text' name='text' value='";

$page_content_2 = "' autofocus></input>
</br>
<input type='submit'>
<input type='hidden', name='action', value='text'>
<input type='hidden', name='source', value='browser_basic'>
</input1>
</form>
</br>
<p>0-9   A-Z</br>Ä  Ö  Ü  -  .  (  )  !  :  /  \"  ,  =  Å  Ø</p>
</br>
<a href='./direct.html'>Fancy Variante</a>
</br>
<a href='./index.php?help=1'>Help</a>
</div>
</div>
</body></html>";

$help = "action=text    text=(String)</br>
    Sets a text, fills the rest with blanks</br></br>
action=char    position=(integer)    char=(String)    [rotate=(String)]</br>
    writes a single character. only first character is used. If rotate equals \"1\" or \"true\", the display will rotate. </br></br>
action=clear</br>
    Fills the display with blanks. Blank Characters won't turn again</br></br>
action=reset</br>
    Resets the display. All Characters will rotate</br></br>
action=rotate</br>
	Starts the Display</br></br>
action=rawchar    pos=(integer)    text=(String)</br>
	This is legacy, don't use it (Only there because momo is to lazy to update his Script)</br></br>
clear=(string)</br>
	This is legacy, don't use it (Only there because momo is to lazy to update his Script)</br></br>
action=raw    data=(string)</br>
	You don't want to use this. It's only for debugging";

$highest = 79;	//höchste vergebene Adresse
$mapping = array(	0=>0, 1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>6, 7=>7, 8=>8, 9=>9,
					10=>10,	11=>11, 12=>12, 13=>13, 14=>14, 15=>15, 16=>16, 17=>17, 18=>18, 19=>19,
					20=>20, 21=>21, 22=>22, 23=>23, 24=>24, 25=>25, 26=>26, 27=>27, 28=>28, 29=>29,
					30=>30, 31=>31, 32=>32, 33=>33, 34=>34, 35=>35, 36=>36, 37=>37, 38=>38, 39=>39,
					40=>40, 41=>41, 42=>42, 43=>43, 44=>44, 45=>45, 46=>46, 47=>47, 48=>48, 49=>49,
					50=>58, 51=>51, 52=>52, 53=>53, 54=>54, 55=>55, 56=>56, 57=>57, 58=>50, 59=>59,
					60=>60, 61=>61, 62=>62, 63=>63, 64=>64, 65=>65, 66=>66, 67=>67, 68=>68, 69=>69,
					70=>70, 71=>71, 72=>72, 73=>73, 74=>74, 75=>75, 76=>76, 77=>77, 78=>78, 79=>79);


function write($hex){
	file_put_contents("/dev/ttyAMA0", pack('H*', $hex), (FILE_APPEND | LOCK_EX));
}





function to_hex($char){
	$temp = "20";
	$in  = array(	" "=>"20", "0"=>"21", "1"=>"22", "2"=>"23", "3"=>"24", "4"=>"25", "5"=>"26", "6"=>"27", "7"=>"28", "8"=>"29", "9"=>"2A",
					
					"a"=>"2B", "A"=>"2B", "b"=>"2C", "B"=>"2C", "c"=>"2D", "C"=>"2D", "d"=>"2E", "D"=>"2E", "e"=>"2F", "E"=>"2F", "f"=>"30",
					"F"=>"30", "g"=>"31", "G"=>"31", "h"=>"32", "H"=>"32", "i"=>"33", "I"=>"33", "j"=>"34", "J"=>"34", "k"=>"35", "K"=>"35",
					"l"=>"36", "L"=>"36", "m"=>"37", "M"=>"37", "n"=>"38", "N"=>"38", "o"=>"39", "O"=>"39", "p"=>"3A", "P"=>"3A", "q"=>"3B",
					"Q"=>"3B", "r"=>"3C", "R"=>"3C", "s"=>"3D", "S"=>"3D", "t"=>"3E", "T"=>"3E", "u"=>"3F", "U"=>"3F", "v"=>"40", "V"=>"40",
					"w"=>"41", "W"=>"41", "x"=>"42", "X"=>"42", "y"=>"43", "Y"=>"43", "z"=>"44", "Z"=>"44",
					
					utf8_decode("ä")=>"45", utf8_decode("Ä")=>"45", utf8_decode("ö")=>"46", utf8_decode("Ö")=>"46",
					utf8_decode("ü")=>"47", utf8_decode("Ü")=>"47",
					
					"-"=>"48", "."=>"49", "("=>"4A", ")"=>"4B", "!"=>"4C", ":"=>"4D", "/"=>"4E", "\""=>"4F", ","=>"50", "="=>"51",
					utf8_decode("å")=>"52", utf8_decode("Å")=>"52", utf8_decode("ø")=>"53", utf8_decode("Ø")=>"53");
					
					if(array_key_exists($char, $in)){
						$temp = $in[$char];
					}
					else{
						$temp = "20";
					}
					return $temp;
}

function to_array($str){
	return str_split($str, 1);
}

function reset_a(){
	write("82");
}

function char_to_command($char, $pos){
	global $mapping;
	$addr = $mapping[$pos];
	if($addr > 127){
			$command = "C8";
		}
	else{
		$command = "88";
	}
	$command = $command . str_pad(dechex((float) $addr), 2, '0', STR_PAD_LEFT) . to_hex($char);
	return $command;
}

function rotate(){
	write("81");
}

function finish_t($text){
	if(isset($_GET["source"]) && $_GET["source"] == "browser_basic"){
		global $page_content_1;
		global $page_content_2;
		echo ($page_content_1);
		echo ($_GET["text"]);
		echo ($page_content_2);
	}
	else{
		echo($text);
	}
	exit(0);
}

function finish(){
	if(isset($_GET["source"]) && $_GET["source"] == "browser_basic"){
		global $page_content_1;
		global $page_content_2;
		echo ($page_content_1);
		echo ($_GET["text"]);
		echo ($page_content_2);
	}
	exit(0);
}

function finish_n(){
	global $page_content_1;
	global $page_content_2;
	echo ($page_content_1);
	echo ($page_content_2);
	exit(0);
}

function action_char($position, $char){					// write single char
	$array = to_array($char);
	$command = char_to_command($array[0], $position);
	write($command);
}

function action_text($text){
	global $highest;
	$array = to_array($text);
	$position = 0;
	$offset = 0;
	if(isset($_GET["offset"])){
		$offset = (integer) $_GET["offset"];
	}
	while($position < $offset){
		action_char($position, " ");
		$position++;
	}
	foreach ($array as $char){
		action_char($position, $char);
		$position = $position + 1;
	}
	while($position <= $highest){
		action_char($position, " ");
		$position = $position + 1;
	}
}

function main(){
//	phpinfo();
	global $help;
	if(isset($_GET["help"])){
		echo($help);
		finish();
	}
	
	if(isset($_GET["clear"])){		// write raw hex to interface
		reset_a();
		finish();
	}
	
	if(isset($_GET["action"]) && $_GET["action"] != ""){
		$action = $_GET["action"];
		
		if($action == "text"){			// write text, append blank
			if(isset($_GET["text"])){
				action_text($_GET["text"]);
				rotate();
				finish();
			}
			else{
				finish_t("argument text is missing");
			}
		}
		
		else if($action == "char"){		// write single char
			if(isset($_GET["position"]) && ($_GET["position"] != "") && isset($_GET["char"]) && ($_GET["char"]) != ""){
				$pos = (integer) $_GET["position"];
				action_char($pos, $_GET["char"]);
				if(isset($_GET["rotate"]) && ($_GET["rotate"] == "true" || $_GET["rotate"] == "1")){
					rotate();
				}
				finish();
			}
			else{
				finish_t("argument \"position\" or \"char\" is missing or \"char\" is not 1 long");
			}
		}
		
		else if($action == "clear"){	// fill with blank
			action_text(" ");
			rotate();
			finish();
		}
		
		else if($action == "reset"){	// reset modules
			reset_a();
			finish();
		}
		
		else if($action == "rotate"){	// reset modules
			rotate();
			finish();
		}
		
		else if($action == "rawchar"){		// legacy, don't use
			global $mapping;
			$pos = (integer) $_GET["pos"];
			$addr = $mapping[$pos];
			$text = $_GET["text"];
			if($addr > 127){
				$command = "C8";
			}
			else{
				$command = "88";
			}
			$command = $command . str_pad(dechex((float) $addr), 2, '0', STR_PAD_LEFT) . $text . "81";
			write($command);
			finish();
		}
		
		else if($action == "raw"){		// write raw hex to interface
			if(isset($_GET["data"])){
				$data = $_GET["data"];
				write($data);
				finish();
			}
			else{
				finish_t("argument \"data\" is missing");
			}
		}
	}
	finish_n();
}

main();
?>
