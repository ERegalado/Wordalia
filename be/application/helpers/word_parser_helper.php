<?php 

function parseWord($word)
{
	$wordArray = array(
		'word'			=>	$word,
		'definition'	=>	'',
		'word_type'		=>	'',
		'example'		=>	'',
		'translation'	=>	''
	);
	// $wordArray['word']= $word;
	header('Content-type: text/html; charset=UTF-8');
	$result = CallAPI("GET","https://www.merriam-webster.com/dictionary/".$word); //Merriam-webster
	if ($result){
		$posDef = strpos($result,'English Language Learners');
		$def = substr($result,$posDef,strlen($result)-$posDef);
		//Getting the definition -----------------------------------------------------------------------------------
		$posIntro = strpos($def,'intro-colon');
		$def1 = substr($def,$posIntro,strlen($def)-$posIntro);
		$posIntro2 = strpos($def1,'class');
		$def2 = strip_tags(substr($def1,0,$posIntro2));
		$wordArray['definition'] = trim(substr($def2,14,strlen($def2)-14));	
		//Getting the word type -----------------------------------------------------------------------------------
		$posWType1 = strpos($def,'main-attr');
		$wdType1 = substr($def,$posWType1,strlen($def)-$posWType1);
		$posWType2 = strpos($wdType1,'span');
		$wdType2 = strip_tags(substr($wdType1,0,$posWType2));
		$wordArray['word_type'] = trim(substr($wdType2,11,strlen($wdType2)-11));
		//Getting the examples -----------------------------------------------------------------------------------
		$posEx = strpos($result,'Examples of');
		if ($posEx){ //There are some words that do not have example
			$exSubstr = substr($result,$posEx,strlen($result)-$posDef);
			$posEx2 = strpos($exSubstr,'definition-inner-item');
			$exSubstr2 = substr($exSubstr,$posEx2,strlen($exSubstr)-$posEx2);
			$posEx3 = strpos($exSubstr2,'</li>');
			$exSubstr3 = substr($exSubstr2,0,$posEx3);
			$wordArray['example'] = substr(strip_tags($exSubstr3),23); //23 = the string starts in the middle of a tag
		}		
	}
	
	//Get the translation
	$SpTrans = CallAPI("GET","http://www.spanishcentral.com/translate/".$word); //Google Translate is not working!! Trying from Spanish Central (Merriam-webster)
	//We have to match the type of the word. That means that if English definition is a noun, spanish translation must be noun as well. If it's verb, sp. translation must be verb atd.
	if ($SpTrans && $wordArray['word_type']!=''){
		$bodyPos = strpos($SpTrans,'body');
		$afterBod = substr($SpTrans,$bodyPos,strlen($SpTrans)-$bodyPos);
		$posWDType = strpos($afterBod,trim($wordArray['word_type']));
		$tr1 = substr($afterBod,0,$posWDType);
		$posWDType2 = strrpos($tr1,'=');
		$tr2 = substr($tr1,$posWDType2,strlen($tr1)-$posWDType2);
		$tr3 = substr($tr2,7,strlen($tr2)-7);
		$posWDTrans3 = strpos($tr3,'<em>');
		$translation = ($posWDTrans3)?substr($tr3,0,$posWDTrans3):$tr3;
		$wordArray['translation'] = trim(strip_tags($translation));
	}	
	
	return $wordArray;
}


?>