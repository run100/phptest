<?php
/**
 * User: Run
 * Date: 上午8:48 15-3-27
 * File: solr.php
 * Desc: 
 */

$collections = '';
if( empty($argv[1]) ){
    exit("请输入集合名称\r\n");
}

$collections = $argv[1];

$options = array(
    'hostname' => '127.0.0.1',
    'port'     => '8983',
    'path'     => 'solr/'.$collections,
);

//reload


try{

    $client = new SolrClient($options);
    #$client->ping();
    $query = new SolrQuery();

    $query->setStart(0);
    $query->setRows(600);

    #$query->setQuery('平');
    #$query->addField('content');
    #$query->addFilterQuery("content:平");
    $query->setQuery("content:腾讯");
    $queryResponse = $client->query($query);
    $response = $queryResponse->getResponse();
    $docs = $response->response['docs'];

    print_r($docs);
    #print_r($response->response->docs);

}catch(SolrServerException $e){
    var_dump($e->getMessage());
    exit;
}


#var_dump($client);

/*
$options = array
(
    'hostname' => 'localhost',
    'login'    => 'username',
    'password' => 'password',
    'port'     => '8983',
);

$client = new SolrClient($options);
$query = new SolrQuery();
$query->setQuery('lucene');
$query->setStart(0);
$query->setRows(50);
$query->addField('cat')->addField('features')->addField('id')->addField('timestamp');
$query_response = $client->query($query);
$response = $query_response->getResponse();
print_r($response);

$client = new SolrClient($options);
$doc = new SolrInputDocument();

$doc->addField('id', 100);
$doc->addField('title', 'Hello Wolrd');
$doc->addField('description', 'Example Document');
$doc->addField('cat', 'Foo');
$doc->addField('cat', 'Bar');

$response = $client->addDocument($doc);
$client->commit();

$query = new SolrQuery();

$query->setQuery('hello');

$query->addField('id')
->addField('title')
->addField('description')
->addField('cat');

$queryResponse = $client->query($query);

$response = $queryResponse->getResponse();

print_r( $response->response->docs );

*/