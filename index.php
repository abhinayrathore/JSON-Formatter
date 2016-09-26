<?php
class JsonHandler {
	protected static $_messages = array(
		JSON_ERROR_NONE => 'No error has occurred',
		JSON_ERROR_DEPTH => 'The maximum stack depth has been exceeded',
		JSON_ERROR_STATE_MISMATCH => 'Invalid or malformed JSON',
		JSON_ERROR_CTRL_CHAR => 'Control character error, possibly incorrectly encoded',
		JSON_ERROR_SYNTAX => 'Syntax error',
		JSON_ERROR_UTF8 => 'Malformed UTF-8 characters, possibly incorrectly encoded'
	);

	public static function encode($value, $options = 0) {
		$result = json_encode($value, $options);

		if($result)  {
			return $result;
		}

		throw new RuntimeException(static::$_messages[json_last_error()]);
	}

	public static function decode($json, $assoc = false) {
		$result = json_decode($json, $assoc);

		if($result) {
			return $result;
		}

		throw new RuntimeException(static::$_messages[json_last_error()]);
	}
	
	public static function indent($json) {
		$result      = '';
		$pos         = 0;
		$strLen      = strlen($json);
		$indentStr   = '  ';
		$newLine     = "\n";
		$prevChar    = '';
		$outOfQuotes = true;

		for ($i=0; $i<=$strLen; $i++) {
			// Grab the next character in the string.
			$char = substr($json, $i, 1);

			// Are we inside a quoted string?
			if ($char == '"' && $prevChar != '\\') {
				$outOfQuotes = !$outOfQuotes;

			// If this character is the end of an element,
			// output a new line and indent the next line.
			} else if(($char == '}' || $char == ']') && $outOfQuotes) {
				$result .= $newLine;
				$pos --;
				for ($j=0; $j<$pos; $j++) {
					$result .= $indentStr;
				}
			}

			// Add the character to the result string.
			$result .= $char;

			// If the last character was the beginning of an element,
			// output a new line and indent the next line.
			if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
				$result .= $newLine;
				if ($char == '{' || $char == '[') {
					$pos ++;
				}

				for ($j = 0; $j < $pos; $j++) {
					$result .= $indentStr;
				}
			}

			$prevChar = $char;
		}
		return $result;
	}
}

$inputJson = $_REQUEST['input'];
$outputJson = '';
$error = '';

if (isset($inputJson) && !empty($inputJson)) {
	try {
		$outputJson = JsonHandler::encode(JsonHandler::decode($inputJson));
		$outputJson = JsonHandler::indent($outputJson);
	} catch (Exception $e) {
		$error = $e->getMessage();
	}
}
?>
<!doctype html>
<html>
	<head>
		<title>JSON Formatter</title><meta name="viewport" content="width=device-width, initial-scale=1"><link rel="stylesheet" href="c/m.css">
	</head>
	<body>
		<main>
			<h1>JSON Formatter</h1>
			<form method="POST" action="" id="form">
				<label for="input">Enter JSON below, then click the &ldquo;Format&rdquo; button to validate and get a beautified version of JSON:</label>
				<textarea name="input" id="input" required><?=$inputJson;?></textarea>
				<label>Indentation: <select id="indent"><option value=2 selected>2</option><option value=4>4</option><option value=6>6</option><option value=8>8</option></select></label>
				<button type="submit" class="primary">Format</button>
				<button type="reset">Reset</button>
			</form>
			<div role="alert" id="error" tabindex=-1><?=$error;?></div>
			<pre id="output" tabindex=-1><?=$outputJson;?></pre>
		</main>
		<footer>© <a href="http://goo.gl/c1qyo">Abhinay Rathore</a> • <a href="http://goo.gl/LF0OTP">LinkedIn</a> • <a href="https://goo.gl/frKzyo">GitHub</a></footer>
		<script src="j/m.js"></script>
	</body>
</html>