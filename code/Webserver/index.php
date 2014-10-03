<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8"></meta>
</head>
<body><?php

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

function to_array($str){
	return str_split($str, 1);
}

function clear(){
	write("82");
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

function main(){
	global $mapping;
	if(isset($_GET["clear"])){	// RESET
		clear();
	}
	
	if(isset($_GET["action"]) && $_GET["action"] == "char"){	// einzelnes Zeichen
		$pos = (integer) $_GET["pos"];
		$addr = $mapping[$pos];
		$text = $_GET["text"];
		if($addr > 127){
				$command = "C8";
			}
			else{
				$command = "88";
			}
			$command = $command . str_pad(dechex((float) $addr), 2, '0', STR_PAD_LEFT) . to_hex($text);
			write($command . "81");
	}
	
	if(isset($_GET["action"]) && $_GET["action"] == "raw"){		// roher Bytecode (Hexformat in ASCII)
		$text = $_GET["text"];
		write($text);
	}
	
	if(isset($_GET["action"]) && $_GET["action"] == "rawchar"){		// roher Bytecode mit Position
		$pos = (integer) $_GET["pos"];
		$addr = $mapping[$pos];
		$text = $_GET["text"];
		if($addr > 127){
				$command = "C8";
			}
			else{
				$command = "88";
			}
			$command = $command . str_pad(dechex((float) $addr), 2, '0', STR_PAD_LEFT) . $text;
			write($command . "81");
	}
	
	else if(isset($_GET["text"])){		// vollständiger Text
		$in_text = utf8_decode($_GET["text"]);
		$array = to_array($in_text);
		$pos = 0;
		$command = "";
		if(isset($_GET["position"]) && $_GET["position"] != ""){
			$pos = $_GET["position"];
		}
		foreach ($array as $char){
			$addr = $mapping[$pos];
			if($addr > 127){
				$command = $command . "C8";
			}
			else{
				$command = $command . "88";
			}
			$command = $command . str_pad(dechex((float) $addr), 2, '0', STR_PAD_LEFT) . to_hex($char);
			$pos = $pos + 1;
		}
		global $highest;
		while($pos <= $highest){
			$addr = $mapping[$pos];
			if($addr > 127){
				$command = $command . "C8";
			}
			else{
				$command = $command . "88";
			}
			$command = $command . str_pad(dechex((float) $addr), 2, '0', STR_PAD_LEFT) . "20";
			$pos = $pos + 1;
		}
		write($command . "81");
	}
}

main();
?>

<div id="container" align="center" width=* height=*>
<div id="input" border="1px" border-color="black" border-radius="6px";>
<form method="get" action="index.php">
<input type="text" name="text" length="4" <?php echo("value=\"" . $_GET["text"] . "\""); ?> autofocus></input>
</br>
<input type="submit"></input1>
</form>
</br>
<p>0-9   A-Z</br>Ä  Ö  Ü  -  .  (  )  !  :  /  "  ,  =  Å  Ø</p>
</br>
<a href="./direct.html">Fancy Variante</a>
</div>
</div>
</body></html>
