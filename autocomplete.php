<?php
include 'SpellCorrector.php';

if(isset($_GET['term'])){
	$suggestTerm = $_GET['term'];
	$suggestArr = array();
	$suggestTerm = strtolower($suggestTerm);
	if(strpos($suggestTerm," ") == -1){
		$suggestArr[0] = $suggestTerm;
	}
	else{
		$suggestArr = explode(" ",$suggestTerm);
}
	$lenOfSuggestArr = count($suggestArr);

	//var_dump($suggestArr);
	$lenOfStr = strlen($suggestTerm);
	$tobesent = $suggestArr[$lenOfSuggestArr-1];
	//var_dump($tobesent);
	$url = "http://localhost:8983/solr/finalcore1/suggest?indent=on&wt=json&q=".$tobesent;
	
	$json = file_get_contents($url);

	//var_dump($json);
	//var_dump($json);
	$arrayJSON = json_decode($json);

	$arryRaw = $arrayJSON->suggest->suggest->$tobesent->suggestions;
	//var_dump($arryRaw);
	$arrResult = array();
	$maxnoOfSuggestions = 10;
	//var_dump($arryRaw);
	if($lenOfStr == 2){
		$maxnoOfSuggestions = 7;
	}
	elseif ($lenOfStr == 3) {
		$maxnoOfSuggestions = 4;
	}
	elseif ($lenOfStr == 1) {
		$maxnoOfSuggestions = 10;
	}
	else {
		$maxnoOfSuggestions = 4;
	}
	$it = 0;
	foreach($arryRaw as $item){
		if($it < $maxnoOfSuggestions){
			$arrItem = array();
			
			if(strpos($item->term,".")){
				continue;
			}
			if(strpos($item->term,"_")){
				continue;
			}
			if(strpos($item->term,":")){
				continue;
			}
			//if(strcmp(strtolower($item->term),strtolower($suggestTerm)) == 0){
				//continue;
			//}
			if($item->term == "not" || $item->term == "n" || $item->term == "no" || $item->term == "na" || $item->term == "nat" || $item->term == "p" || $item->term == "png" || $item->term == "po" || $item->term == "c" || $item->term == "css" || $item->term == "ca" || $item->term == "w" || $item->term == "n" || $item->term == "n" || $item->term == "b" || $item->term == "br" || $item->term == "r" || $item->term == "ri" || $item->term == "o" || $item->term == "ol" || $item->term == "d" || $item->term == "do" || $item->term == "don" || $item->term == "dona" || $item->term == "t" || $item->term == "tr" || $item->term == "cal" || $item->term=="j" || $item->term == "jo" || $item->term == "h" || $item->term == "ha" || $item->term == "har" || $item->term == "harr" || $item->term == "p" || $item->term == "po" || $item->term == "doesn't" || $item->term == "does" || $item->term == "of" || $item->term == "preethishp" || $item->term == "cali" || $item->term == "calif" || $item->term == "califo" || $item->term == "califor" || $item->term == "californ" || $item->term == "californi" || $item->term == "with" || $item->term == "www" || $item->term == "will" || $item->term == "wil" || $item->term == "f" || $item->term == "for" || $item->term == "from" || $item->term == "donal" || $item->term == "to" || $item->term == "the" || $item->term == "this" || $item->term == "has" || $item->term == "have" || $item->term == "had" || $item->term == "her" || $item->term == "href" || $item->term == "hr" || $item->term == "by" || $item->term == "be" || $item->term == "btn" || $item->term == "braz" || $item->term == "brazi" || $item->term == "newswebsites" || $item->term == "nav" || $item->term == "nao" || $item->term == "js" || $item->term == "jpg" || $item->term == "resourcename" || $item->term == "rect" || $item->term == "don't"){
				continue;
			}

			if($it == 0){
				$it++;
				continue;
			}

			
			if($lenOfSuggestArr == 1){
				$arrItem["label"] = ucfirst($item->term);
				$arrItem["value"] = ucfirst($item->term);
			}
			else{
			$suggest = formatTheString($item->term, $suggestArr);
			$arrItem["label"] = $suggest;
			$arrItem["value"] = $suggest;
		}
			array_push($arrResult, $arrItem);
			$it++;
		}
		else{
			break;
		}
	}
	//var_dump($arrResult);
	//foreach($arryRaw as $key => $item){
	//	$arr[$key] = $item->term;
	//}
	//var_dump($arr);
	echo json_encode($arrResult);
	//var_dump($jsonStr);
	
} 


?>

<?php

$stopWords = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","about",
"above","after","again","against","all","am","an","and","any","are","arent","as","at","be","because","been","before","being","below","between","both","but","by","cant",
"cannot","could","couldnt","did","didn't","do","does","doesn't","doing","don't","down","during","each","few","for","from","further","had","hadn't","has","hasn't","have","haven't","having","he","he'd","he'll","he's","her","here","here's","hers","herself","him","himself","his","how","how's","i","i'd","i'll","i'm","i've","if","in","into","is","isn't","it","it's","its","itself","let's","me","more","most","mustn't","my","myself","no","nor","not","of","off","on","once","only","or","other","ought","our","ours","ourselves","out","over","own","same","shan't","she","she'd","she'll","she's","should","shouldn't","so","some","such","than","that","that's","the","their","theirs","them","themselves","then","there","there's","these","they","they'd","they'll","they're","they've","this","those","through","to","too","under","until","up","very","was","wasn't","we","we'd","we'll","we're","we've","were","weren't","what","what's","when","when's","where","where's","which","while","who",
"who's","whom","why","why's","with","won't","would","wouldn't","you","you'd","you'll","you're","you've","your","yours","yourself","yourselves");

function removeSpace($text){
	return str_replace(" ","",$text);
}

function formatTheString($text, $suggestArr){
	$c = 0;
	$retStr = '';
	$lim = count($suggestArr);
	//var_dump("im here");
	while($c < ($lim-1)){
		
		if($c!= 0){
			$retStr = $retStr." ";
		}

		$retStr= $retStr.ucfirst(SpellCorrector::correct($suggestArr[$c]));
		
		$c++;
	}
	$retStr.=" ";
	$retStr.=ucfirst($text);
	return $retStr;
}

function checkForStop($termOfItem){


	$termConv = trim((string)$termOfItem);

	foreach($stopWords as $itemOfterm){
		$strItem = trim((string)$itemOfterm);
		if(strcmp($termConv,$strItem)==0){
			return true;
		}
	}
	return false;
	
	
}

?>