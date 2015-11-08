<?php
/**
* @version $Id: ExpatvsHtmlSax.php,v 1.3 2004/06/02 14:33:38 hfuecks Exp $
* Shows HTMLSax in a race against Expat. Note that HTMLSax performance
* gets slower on PHP < 4.3.0 or if parser options are being used
*/
require_once('XML/HTMLSax3.php');

function getmicrotime(){ 
    list($usec, $sec) = explode(" ",microtime()); 
    return ((float)$usec + (float)$sec); 
}

// The PHP general RDF feed
$doc = file_get_contents('http://news.php.net/group.php?group=php.general&format=rdf');

/* Simple handler that does nothing */
class MyHandler {
    function openHandler(& $parser,$name,$attrs) {}
    function closeHandler(& $parser,$name) {}
    function dataHandler(& $parser,$data) {}
}
$handler=& new MyHandler();


$parser = xml_parser_create();
xml_set_object($parser, $handler);
xml_set_element_handler($parser, 'openHandler', 'closeHandler' );
xml_set_character_data_handler($parser, 'dataHandler' );

echo ('<pre>');
// Time Expat
$start = getmicrotime();
xml_parse($parser, $doc);
$end = getmicrotime();
echo ( "Expat took:\t\t".(getmicrotime()-$start)."<br />" );

$start = getmicrotime();
$parser =& new XML_HTMLSax3();
$parser->set_object($handler);
$parser->set_element_handler('openHandler','closeHandler');
$parser->set_data_handler('dataHandler');

// Time HTMLSax
$start = getmicrotime();
$parser->parse($doc);
echo ( "HTMLSax took:\t\t".(getmicrotime()-$start) );
echo ('</pre>');
?>