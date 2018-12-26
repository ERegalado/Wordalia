<?php 

function parseWord($word){
	//Initialize
	$wordArray = array(
		'word'			=>	$word,
		'definition'	=>	'',
		'word_type'		=>	'',
		'example'		=>	'',
		'translation'	=>	''
	);
	//Get MW key
	$inputJSON = file_get_contents('fb_config.json');
	$json = json_decode($inputJSON, true);
	
	try{
		//1. Get the definition from the 'learners' dictionary and the translation from the 'spanish' dictionary
		// $result1 = grab_xml_definition($word,dictionary1, dictionary1Key);		
		// $result2 = grab_xml_definition($word,dictionary2, dictionary2Key);		
		$result1 = grab_xml_definition($word,$json['MW']['dictionary1'], $json['MW']['dictionary1Key']);
		$result2 = grab_xml_definition($word,$json['MW']['dictionary2'], $json['MW']['dictionary2Key']);
		$textXML = $result1;
		libxml_use_internal_errors(true); //If the XML is not valid, handle the error internally
		$xml = simplexml_load_string($result1);
		$transXML = simplexml_load_string($result2);
		
		//Continue only if an entry was found
		if (isset($xml->entry)):
			//2. Get the word type (Easiest one)
			$wordArray['word_type'] = $xml->entry->fl;
			
			//3. Get the definitions
			//3.1. Get the first definition	
			$wDef1 = '';
			$wDef2 = ''; //Initialize
			$wDef1 = trim($xml->entry->def->dt);
			if ( ($wDef1 == '' || $wDef1 == ':') && isset($xml->entry->def->dt->un))$wDef1 = $xml->entry->def->dt->un;
			if (($wDef1 == '' || $wDef1 == ':') && isset($xml->entry->def->dt->sx))$wDef1 = $xml->entry->def->dt->sx;
			//3.2. Attempt to get second def
			if (count($xml->entry->def->dt)>1){		
				$wDef2 = $xml->entry->def->dt[1];
				if (($wDef2 == '' || $wDef2 == ':') && isset($xml->entry->def->dt[1]->un))$wDef2 = $xml->entry->def->dt[1]->un;		
				if (($wDef2 == '' || $wDef2 == ':') && isset($xml->entry->def->dt[1]->sx))$wDef2 = $xml->entry->def->dt[1]->sx;		
			}
			$wordArray['definition'] = ($wDef2=='')?$wDef1: $wDef1.' (2) '.$wDef2;
			
			//4. Get the examples (Parse as text)
			$viPos = strpos($textXML,'<vi>');
			$cviPos = strpos($textXML,'</vi>');
			$wordArray['example'] = strip_tags(substr($textXML,$viPos ,$cviPos-$viPos));
			/*
			$viPos2 = strpos($textXML,'<vi>',$cviPos);
			$ex2=''
			if ($viPos2) $ex2 = substr($textXML,$viPos2 ,strpos($textXML,'</vi>',$viPos2)-$viPos2);*/
			
			//5. Get the translation
			$wordArray['translation'] = ''; //Initialize
			if ($transXML){
				if (isset($transXML->entry->def->dt))$wordArray['translation'] = $transXML->entry->def->dt;
				if (gettype($wordArray['translation'])=='object')$wordArray['translation']= $transXML->entry->def->dt->{'ref-link'};
			}			
		endif;
	}finally{
		//6. Return the results
		return $wordArray;
	}

}


// This function grabs the definition of a word in XML format.
function grab_xml_definition ($word, $ref, $key)
{
		
	$uri = "https://www.dictionaryapi.com/api/v1/references/" . urlencode($ref) . "/xml/" . 
				urlencode($word) . "?key=" . urlencode($key);
					return file_get_contents($uri);
};

	/*
	public function xml_parsing(){
		$appleXML = '<?xml version="1.0" encoding="utf-8" ?>
<entry_list version="1.0">
	<entry id="apple"> <hw highlight="yes">ap*ple</hw> <sound><wav>apple001.wav</wav></sound> <pr>ˈæpəl</pr> <fl>noun</fl> <in><il>plural</il> <if>ap*ples</if></in> <def><dt>:a round fruit with red, yellow, or green skin and firm white flesh <wsgram>count</wsgram> <vi>crisp juicy <it>apples</it></vi> <vi>a bad/rotten <it>apple</it> [=an apple that has rotted and cannot be eaten]</vi> <wsgram>noncount</wsgram> <vi>a piece of <it>apple</it></vi> <un>often used before another noun <vi><it>apple</it> pie</vi> <vi><it>apple</it> juice</vi> <vi><it>apple</it> trees</vi></un> <dx>see color picture on page C5</dx> <snote>In figurative use, a <phrase>bad apple</phrase> or <phrase>rotten apple</phrase> is a bad member of a group who causes problems for the rest of the group. <vi>A few <it>bad apples</it> cheated on the test, and now everyone has to take the test again.</vi> <vi>One <it>rotten apple</it> ruined the day for the rest of us.</vi></snote> <dx>see also <dxt>adam\'s apple</dxt> <dxt>crab apple</dxt></dx></dt></def> <dro><dre>compare apples to apples/oranges</dre> <dx>see <dxt>compare</dxt></dx></dro> <dro><dre>the apple of someone\'s eye</dre> <def><dt>:a person or thing that someone loves very much <vi>His daughter is <it>the apple of his eye</it>.</vi></dt></def></dro> <dro><dre>upset the apple cart</dre> <dx>see <dxt>upset</dxt></dx></dro></entry>
	<entry id="apple-cheeked"> <hw>apple–cheeked</hw> <pr>ˈæpəlˌtʃi:kt</pr> <fl>adjective</fl> <def><dt>:having red or pink cheeks <vi><it>apple-cheeked</it> youngsters</vi></dt></def></entry>
	<entry id="apple pie"> <hw>apple pie</hw> <fl>noun</fl> <in><il>plural</il> <if>⁓ pies</if></in> <def><gram>count</gram> <dt>:a sweet pie made with apples</dt></def> <dro><dre>(as) American as apple pie</dre> <def><dt>:very or typically American <vi>Baseball is <it>as American as apple pie</it>.</vi></dt></def></dro> <dro><dre>in apple-pie order</dre> <def><sl>informal</sl> <dt>:arranged neatly or perfectly :in perfect order <vi>Everything in the cupboard was (arranged) <it>in apple-pie order</it>.</vi></dt></def></dro></entry>
	<entry id="apple polisher"> <hw>apple polisher</hw> <fl>noun</fl> <in><il>plural</il> <if>⁓ -ers</if></in> <def><gram>count</gram> <sl>US</sl> <sl>informal + disapproving</sl> <dt>:a person who tries to get the approval and friendship of someone in authority by praise, flattery, etc. <vi>an executive surrounded by <it>apple polishers</it></vi></dt></def></entry>
	<entry id="Adam\'s apple"> <hw>Adam\'s apple</hw> <fl>noun</fl> <in><il>plural</il> <if>⁓ apples</if></in> <def><gram>count</gram> <dt>:the lump that sticks out in the front of a person\'s neck, that is usually larger in men than in women, and that moves when a person talks or swallows</dt></def></entry>
	<entry id="Big Apple"> <hw>Big Apple</hw> <fl>noun</fl> <dro><dre>the Big Apple</dre> <def><sl>informal</sl> <dt><un>used as a name for New York City <vi>She moved to <it>the Big Apple</it> after she graduated.</vi></un></dt></def></dro></entry>
	<entry id="candy apple"> <hw>candy apple</hw> <fl>noun</fl> <in><il>plural</il> <if>⁓ apples</if></in> <def><gram>count</gram> <sl>US</sl> <dt>:an apple that is covered with a sugary mixture that becomes hard</dt></def></entry>
	<entry id="crab apple"> <hw>crab apple</hw> <fl>noun</fl> <in><il>plural</il> <if>⁓ apples</if></in> <def><gram>count</gram> <dt>:a small, sour apple or the kind of tree that produces it</dt></def></entry>
	<entry id="American[2]"> <hw hindex="2">American</hw> <altpr>əˈmerəkən</altpr> <fl>adjective</fl> <def><sn>1</sn> <dt>:of or relating to the U.S. or its citizens <vi><it>American</it> culture/government/history</vi> <vi>the <it>American</it> people</vi> <vi>their <it>American</it> friends</vi></dt> <sn>2</sn> <dt>:of or relating to North America, South America, or the people who live there <vi>the <it>American</it> continents</vi> <vi>a tropical <it>American</it> tree</vi></dt></def> <dro><dre>(as) American as apple pie</dre> <dx>see <dxt>apple pie</dxt></dx></dro></entry>
	<entry id="bad[1]"> <hw highlight="yes" hindex="1">bad</hw> <sound><wav>bad00001.wav</wav></sound> <pr>ˈbæd</pr> <fl>adjective</fl> <in><if>worse</if> <sound><wav>worse001.wav</wav><wav>ill00002.wav</wav></sound> <pr>ˈwɚs</pr></in> <in><if>worst</if> <sound><wav>worst001.wav</wav><wav>ill00003.wav</wav></sound> <pr>ˈwɚst</pr></in> <def><sn>1 a</sn> <dt>:low or poor in quality <vi>a <it>bad</it> repair job</vi> <vi><it>bad</it> work</vi> <vi>The house is in <it>bad</it> condition/shape.</vi></dt> <sn>b</sn> <dt>:not correct or proper <vi><it>bad</it> manners</vi> <vi><it>bad</it> [=<it>incorrect, faulty</it>] grammar</vi> <vi>a letter written in <it>bad</it> French</vi> <vi><it>bad</it> spelling</vi> <vi>a <it>bad</it> check [=a check that cannot be cashed]</vi> <dx>see also <dxt>bad language</dxt></dx></dt> <sn>2 a</sn> <dt>:not pleasant, pleasing, or enjoyable <vi>He had a <it>bad</it> day at the office.</vi> <vi>I was having a <it>bad</it> dream.</vi> <vi>She made a very <it>bad</it> impression on her future colleagues.</vi> <vi>The food tastes <it>bad</it>.</vi> <vi>The flower smells <it>bad</it>.</vi> <vi>He has <it>bad</it> breath. [=breath that smells bad]</vi> <vi>We\'ve been having <it>bad</it> weather lately.</vi> <vi>The medicine left a <it>bad</it> taste in his mouth.</vi> <vi>It feels <it>bad</it> [=<it>uncomfortable, painful</it>] to stretch out my arm.</vi> <vi>I look <it>bad</it> in this hat. = This hat looks <it>bad</it> on me.</vi> <vi>That hat doesn\'t look <it>bad</it> on you. [=that hat looks good on you]</vi></dt> <sn>b</sn> <dt>:having, marked by, or relating to problems, troubles, etc. <vi>good and <it>bad</it> news</vi> <vi>They have remained together in good times and <it>bad</it> (times).</vi> <vi>It\'s a <it>bad</it> time for business right now.</vi> <vi>a <it>bad</it> omen</vi> <vi><it>bad</it> luck/fortune</vi> <vi>Things are looking pretty <it>bad</it> for us at this point.</vi> <vi>I have a <it>bad</it> feeling about this.</vi> <dx>see also <dxt>bad blood</dxt> <dxt>bad news</dxt></dx></dt> <sn>c</sn> <dt>:not adequate or suitable <vi>I couldn\'t take a picture because the lighting was <it>bad</it>.</vi> <vi>It\'s a <it>bad</it> day for a picnic.</vi> <vi>She made a <it>bad</it> marriage.</vi> <vi>Is this a <it>bad</it> moment/time to have a word with you?</vi></dt> <sn>d</sn> <dt>:not producing or likely to produce a good result <vi>a <it>bad</it> deal</vi> <vi>a <it>bad</it> risk</vi> <vi>a <it>bad</it> idea/plan</vi> <vi>The plan has its good points and its <it>bad</it> points.</vi> <vi>a <phrase>bad debt</phrase> [=a debt that will not be paid]</vi> <vi>a <phrase>bad loan</phrase> [=a loan that will not be repaid]</vi></dt> <sn>e</sn> <dt>:expressing criticism or disapproval <vi>The movie got <it>bad</it> reviews.</vi></dt> <sn>3 a</sn> <dt>:not healthy :marked or affected by injury or disease <vi>His health is pretty <it>bad</it>. = He\'s in pretty <it>bad</it> health.</vi> <vi>The patient was pretty <it>bad</it> [=<it>ill, sick</it>] last week and even <it>worse</it> yesterday but is doing better now.</vi> <vi>He came home early because he was feeling pretty <it>bad</it>. [=he wasn\'t feeling well]</vi> <vi>My father has a <it>bad</it> back/leg. [=a back/leg that is always or often painful]</vi> <vi>She has <it>bad</it> eyesight/hearing.</vi> <vi><it>bad</it> teeth</vi></dt> <sn>b</sn> <dt>:causing harm or trouble <vi>a <it>bad</it> diet</vi> <vi>a <it>bad</it> influence</vi> <vi><phrase>bad cholesterol</phrase> [=a type of cholesterol that can cause serious health problems when there is too much of it in your blood]</vi> <un>often + <it>for</it> <vi>Eating too much can be <it>bad for</it> you. = It can be <it>bad for</it> you to eat too much.</vi> <vi>Eating all that candy is <it>bad for</it> your teeth.</vi> <vi>Watching too much TV is <it>bad for</it> children.</vi></un></dt> <sn>4 a</sn> <dt>:not morally good or right :morally evil or wrong <vi>a <it>bad</it> person</vi> <vi><it>bad</it> conduct/behavior</vi> <vi>a man of <it>bad</it> character</vi> <vi><it>bad</it> intentions/deeds</vi> <vi>It\'s hard to tell the good guys from the <phrase>bad guys</phrase> in this movie.</vi> <dx>see also <dxt>bad faith</dxt></dx></dt> <sn>b</sn> <dt>:not behaving properly <vi>a <it>bad</it> dog</vi> <vi>I\'m afraid your son has been a very <it>bad</it> [=<it>naughty</it>] boy.</vi> <dx>see also <dxt>bad boy</dxt></dx></dt> <sn>5 a</sn> <dt>:not skillful :not doing or able to do something well <vi>a <it>bad</it> musician</vi> <vi>a <it>bad</it> doctor</vi> <vi>She was pretty <it>bad</it> in that movie. [=she did not act well]</vi> <un>often + <it>at</it> <vi>a doctor who\'s <it>bad at</it> treating nervous patients</vi> <vi>He\'s very/really <it>bad at</it> expressing his true feelings.</vi></un></dt> <sn>b</sn> <dt>:having a tendency not to do something <un>+ <it>about</it> <vi>He\'s <it>bad about</it> getting to work on time. [=he often fails to get to work on time]</vi> <vi>I\'m very <it>bad about</it> remembering people\'s birthdays. [=I often forget people\'s birthdays]</vi></un></dt> <sn>6 a</sn> <dt>:not happy or pleased :feeling regret or guilt about something <vi>I <phrase>feel bad</phrase> about what happened. [=I regret what happened]</vi> <vi>She <it>felt bad</it> that she forgot to call. = She <it>felt bad</it> about forgetting to call.</vi></dt> <sn>b</sn> <dt>:not cheerful or calm <vi>She\'s in a <it>bad</it> mood. [=an angry mood]</vi> <vi>He has a <it>bad</it> temper. [=he\'s bad-tempered; he becomes angry easily]</vi></dt> <sn>7</sn> <dt>:serious or severe <vi>She\'s in <it>bad</it> trouble.</vi> <vi>He has a <it>bad</it> cough/cold.</vi> <vi>That bruise looks <it>bad</it>: you\'d better see a doctor about it.</vi> <vi>How <it>bad</it> is the pain?</vi></dt> <sn>8</sn> <dt>:no longer good to eat or drink :not fresh <vi>the smell of <it>bad</it> fish</vi> <vi>Is the milk still good or has it <phrase>gone bad</phrase>? [=<it>spoiled</it>]</vi></dt> <sn>9</sn> <sin><if>bad*der</if></sin> <sin><if>bad*dest</if></sin> <ssl>chiefly US</ssl> <ssl>informal</ssl> <sn>a</sn> <dt>:very good <vi>He\'s the <it>baddest</it> guitar player you\'ll ever hear!</vi></dt> <sn>b</sn> <dt>:very tough or dangerous <vi>Don\'t mess around with him. He\'s a <it>bad</it> dude.</vi></dt></def> <dro><dre>a bad job</dre> <dx>see <dxt>job</dxt></dx></dro> <dro><dre>a bad lot</dre> <dx>see <dxt>lot</dxt></dx></dro> <dro><dre>bad apple</dre> <dx>see <dxt>apple</dxt></dx></dro> <dro><dre>come to a bad end</dre> <dx>see <dxt>end</dxt></dx></dro> <dro><dre>from bad to worse</dre> <def><dt>:from a bad state or condition to an even worse state or condition <vi>The company has been struggling for years, and things have recently gone <it>from bad to worse</it>.</vi></dt></def></dro> <dro><dre>in a bad way</dre> <def><dt>:in a bad condition <vi>Without enough funding, public services are <it>in a</it> pretty <it>bad way</it> right now.</vi> <vi>The patient was <it>in a bad way</it> last week but is doing better now.</vi></dt></def></dro> <dro><dre>in someone\'s bad books</dre> <dx>see <dxt>book</dxt></dx></dro> <dro><dre>not bad</dre> <def><dt>:fairly good or quite good <vi>All things considered, she\'s <it>not</it> a <it>bad</it> singer. [=she\'s a pretty good singer]</vi> <vi>“How are you?” “<it>Not</it> (too/so) <it>bad</it>, thanks. And you?”</vi></dt></def></dro> <dro><dre>too bad</dre> <def><sn>1</sn> <dt><un>used to show that you are sorry or feel bad about something <vi>It\'s <it>too bad</it> [=<it>unfortunate</it>] that John and Mary are getting divorced. = It\'s <it>too bad</it> about John and Mary getting divorced. [=I\'m sorry to hear that John and Mary are getting divorced]</vi> <vi>“I won\'t be able to come to the party.” “(That\'s) <it>Too bad</it>. I was hoping you\'d be there.”</vi></un></dt> <sn>2</sn> <dt><un>used in an ironic way to show that you are not sorry or do not feel bad about something <vi>“But I need your help!” “(That\'s just) <it>Too bad</it>.”</vi></un></dt></def></dro> <dro><dre>with bad grace</dre> <dx>see <dxt>grace</dxt></dx></dro> <uro><ure>bad*ness</ure> <fl>noun</fl> <gram>noncount</gram> <utxt><vi>the <it>badness</it> of his behavior</vi> <vi>the <it>badness</it> [=<it>severity</it>] of his injuries</vi> <vi>There\'s more goodness than <it>badness</it> in him.</vi></utxt></uro></entry>
</entry_list>';

	$helloXML = '<?xml version="1.0" encoding="utf-8" ?>
<entry_list version="1.0">
	<entry id="hello"> 
		<hw highlight="yes">hel*lo</hw> 
		<sound><wav>hello001.wav</wav></sound> 
		<pr>həˈloʊ</pr> 
		<fl>noun</fl> 
		<in><il>plural</il> <if>hel*los</if></in> 
		<def><sn>1</sn> <dt><un>used as a greeting <vi><it>Hello</it> there! How are you?</vi> <vi><it>Hello</it>, my name is Linda.</vi></un></dt> <sn>2</sn> <sgram>count</sgram> <dt>:the act of saying the word <it>hello</it> to someone as a greeting <vi>We exchanged <it>hellos</it>. [=we said hello to each other]</vi> <vi>They welcomed us with a warm <it>hello</it>.</vi></dt> <sn>3</sn> <dt><un>used when you are answering the telephone <vi><it>Hello</it>. Who\'s this? [=who is calling?]</vi> <vi><it>Hello</it>. May I speak to Linda, please?</vi></un></dt> <sn>4</sn> <dt><un>used to get someone\'s attention <vi><it>Hello</it>? Is anybody here?</vi></un></dt> <sn>5</sn> <dt><un>used to express surprise <vi>Well, <it>hello</it>! What do we have here?</vi></un></dt></def>
	</entry>
</entry_list>';

	$translationXML = '<?xml version="1.0" encoding="utf-8" ?>
<entry_list version="1.0">
	<entry id="hello"> <hw>hello</hw> <pr>həˈlo:, hɛ-</pr> <fl>interjection</fl> <def> <dt>:¡hola!</dt> </def><ref-link>hello</ref-link><ref-link>hello</ref-link></entry>
</entry_list>';

	$translationXML = '<?xml version="1.0" encoding="utf-8" ?>
<entry_list version="1.0">
	<entry id="apple"> <hw>apple</hw> <pr>ˈæp<it>ə</it>l</pr> <fl>noun</fl> <def> <dt>:<ref-link>manzana</ref-link> <gl>feminine</gl></dt> </def><ref-link>apple</ref-link><ref-link>apple</ref-link></entry>
	<entry id="Adam\'s apple"> <hw>Adam\'s apple</hw> <pr>ˈædəmz</pr> <fl>noun</fl> <def> <dt>:<ref-link>nuez</ref-link> <gl>feminine</gl> de Adán</dt> </def><ref-link>Adam\'s apple</ref-link><ref-link>Adam\'s apple</ref-link></entry>
</entry_list>';

	$textXML = $appleXML;
	//Even if the answer contains more than one entry, the response is automatically directed to the first entry.
	//$xml = simplexml_load_string($helloXML);
	$xml = simplexml_load_string($appleXML);
	$transXML = simplexml_load_string($translationXML);
	echo '<h1>'.$xml->entry->fl.'</h1>';	
	
	//echo '<h1>'.$appXML->entry->fl.'</h1>';
	//echo '<h1>'.$appXML->entry->def->dt.'</h1>';
		
	echo '<pre>';
	
	//print_r($xml);
	//print_r($appXML);
	//print_r($transXML);
	
	echo '</pre>';
	
	
	//Get the word type
	$wType = $xml->entry->fl;
	//Get the first definition
	
	$wDef = '';
	$wDef1 = '';
	$wDef2 = ''; //Initialize
	$wDef1 = $xml->entry->def->dt;
	if ($wDef1 == '')$wDef1 = $xml->entry->def->dt->un;
	//Attempt to get second def
	if (count($xml->entry->def->dt)>1){		
		$wDef2 = $xml->entry->def->dt[1];
		if ($wDef2 == '')$wDef2 = $xml->entry->def->dt[1]->un;		
	}
	$wDef = ($wDef2=='')?$wDef1: $wDef1.' (2) '.$wDef2;

	echo '<h1>'.$wDef.'</h1>';	
	
	//Get the examples (Parse as text)
	$viPos = strpos($textXML,'<vi>');
	$cviPos = strpos($textXML,'</vi>');
	$ex1 = substr($textXML,$viPos ,$cviPos-$viPos);
	/*
	$viPos2 = strpos($textXML,'<vi>',$cviPos);
	$ex2=''
	if ($viPos2) $ex2 = substr($textXML,$viPos2 ,strpos($textXML,'</vi>',$viPos2)-$viPos2);
	echo '<h1>'.$ex1.'</h1>';
	
	//Get the translation
	$trans = ''; //Initialize
	$trans = $transXML->entry->def->dt;
	if (gettype($trans)=='object')$trans= $transXML->entry->def->dt->{'ref-link'};
	echo '<h1>'.$trans.'</h1>';
	
	/*
		'word'			=>	$word,
		'definition'	=>	'',
		'word_type'		=>	$xml->entry->fl,
		'example'		=>	'',
		'translation'	=>	''
		
	}*/
	

/*  DEPRECATED
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
*/

?>