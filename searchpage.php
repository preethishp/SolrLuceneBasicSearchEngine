<?php

 include 'SpellCorrector.php';

header('Content-Type: text/html; charset=utf-8');

$limitperpage = 10;
$queryString = isset($_REQUEST['q']) ? $_REQUEST['q'] : false;
$queryString = strtolower($queryString);
$resultsForDefault = false;
$resultsForPageRank = false;
$pageRankActive = false;
$defaultActive = false;
//$default_status = 'unchecked';
//$pagerank_status = 'unchecked';
if ($queryString)
{
 
  require_once('Apache/Solr/Service.php');

  $solr = new Apache_Solr_Service('localhost', 8983, '/solr/finalcore1');
  
  
  if (get_magic_quotes_gpc() == 1)
  {
    $queryString = stripslashes($queryString);
  }

  if(isset($_GET['ranking'])){
      if($_GET['ranking'] == 'default'){
        $defaultActive = true;
      }
      else{
        $pageRankActive = true;
      }
  }
$correctQuery = "";
 
  if(strpos($queryString, " ")){
    $partArr = explode(" ",$queryString);
    $sizeOfPartArr = count($partArr);
    $c = 0;
    
    while($c < $sizeOfPartArr){
      
      $correctWord = SpellCorrector::correct($partArr[$c]);
      if($c != 0){
      $correctQuery .= " ";
    }
      $correctQuery.=$correctWord;
      $c++;
    }
  }
  else{ 
  $correctQuery = SpellCorrector::correct($queryString);
 }



  $pageRankParams = array('sort'=>'pageRankFile desc');

  try
  {
    $resultsForDefault = $solr->search($correctQuery, 0, $limitperpage);
    
  }
  catch (Exception $e)
  {
  
    die("<html><head><title>DEFAULT SEARCH EXCEPTION</title><body><pre>{$e->__toString()}</pre></body></html>");
  }

  try
  {
    $resultsForPageRank = $solr->search($correctQuery, 0, $limitperpage, $pageRankParams);
  }
  catch(Exception $excep){

    die("<html><head><title>PAGE RANK SEARCH EXCEPTION</title><body><pre>{$excep->__toString()}</pre></body></html>");
  }
}

?>
<html>
  <head>
    <title>Solr Client</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.10.2/jquery.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.js"></script>
<style>
body{
  width:600px;
}
#greenify{
  color: #009900;
}
#bigify{
  font-size:1.2em;
}
.enlarge{
  font-size:1.2em;
}
</style>
   
  </head>
  <body>
    <form  accept-charset="utf-8" method="get">
      <div class="ui-widget">
      <label for="q">Search:</label>
      <input id="q" name="q" type="text" value="<?php echo htmlspecialchars($queryString, ENT_QUOTES, 'utf-8'); ?>"/>
      <input type ="radio" id="default" name="ranking" value="default" checked/>Default 
      <input type="radio" id="pagerank" name="ranking" value="pagerank"/>PageRank
      <input type="submit"/>
      </div>
    </form>
<?php
  
   $csvArr = array();
if (($csvHandle = fopen("mapABCNewsFile.csv", "r")) !== FALSE) {
    while (($line = fgetcsv($csvHandle, ",")) !== FALSE) {
        $csvArr[$line[0]] = $line[1];
    }
    fclose($csvHandle);
}

if (($csvHandle = fopen("mapFoxNewsFile.csv", "r")) !== FALSE) {
    while (($line= fgetcsv($csvHandle, ",")) !== FALSE) {
        
        $csvArr[$line[0]] = $line[1];
    }
    fclose($csvHandle);
}

if($defaultActive){

if ($resultsForDefault)
{
   $total = (int) $resultsForDefault->response->numFound;
  $start = min(1, $total);
  $end = min($limitperpage, $total);
  
if(strcmp($correctQuery,$queryString) != 0){
  $_REQUEST['q'] = $queryString;

?>
    <div class = "enlarge">Showing results for: <a href=<?php echo "http://localhost/solr-php-client-master/searchpage.php?q=".urlencode($correctQuery)."&ranking=".getRanking()?>><?php echo $correctQuery; ?></a></div>
    <div class="enlarge">Search Instead for: <a href=<?php echo "http://localhost/solr-php-client-master/searchpage.php?q=".urlencode($queryString)."&ranking=".getRanking()?>><?php echo $queryString; ?></a></div>
    <br/>   

   <?php } ?> 
    <div><b>Default Results <?php echo $start; ?> - <?php echo $end;?> of <?php echo $total; ?>:</b></div>
    <ol>
<?php

 

  
  foreach ($resultsForDefault->response->docs as $docRecord)
  {
?>
      <li>
       
<?php
    
    foreach ($docRecord as $docfield => $docvalue)
    {

      if($docfield == "id")
      {

            $parts = explode("/",$docvalue);
            $content = file_get_contents($docvalue);

            $sizeParts = count($parts);
            $fileName = $parts[$sizeParts-1];


    }
    if($docfield == "title"){
      
?>
        <a href=<?php echo htmlspecialchars($csvArr[$fileName], ENT_NOQUOTES, 'utf-8'); ?> target='_blank' id="bigify"><?php echo $docvalue; ?></a><br/>
        <a href=<?php echo htmlspecialchars($csvArr[$fileName], ENT_NOQUOTES, 'utf-8'); ?> target='_blank' id="greenify"><?php echo $csvArr[$fileName]; ?></a>

        <?php 
          if($storeVal == ""){
        ?>
          <p>...<?php echo htmlspecialchars($correctQuery, ENT_NOQUOTES, 'utf-8'); ?> News  Videos Categories Pictures ... Recent news about <?php echo $correctQuery; ?> has .... </p>
        <?php 
      }
          else{
        ?> 
        <p>...<?php echo htmlspecialchars($storeVal, ENT_NOQUOTES, 'utf-8'); ?>... </p>
        
<?php
}
    }

    if($docfield == "description"){



?>


<?php
         // echo 'link'.$id;

 

$term = $correctQuery;
$regex =  '/[A-Z][^\\.>\/=%{}:"]*[\\s]+('.$term.')[\\s]+[^\\.\/<=%{}:"]*/i';
$snippet = "null";
if (preg_match($regex, $content, $match)==1)
{ 
$match[0] = str_replace("nbsp;", "", $match[0]);
$match[0] = str_replace("quot;", "", $match[0]);
$snippet = html_entity_decode($match[0], ENT_QUOTES | ENT_XML1 | ENT_COMPAT | ENT_HTML5, 'UTF-8');   
}
else
{
  if(strpos( $term, ' ') >=0 )
  {
    $parts = preg_split("/[\s]+/", $term);
    foreach($parts as $str)
    {
      $term = $str;
      $regex =  '/[A-Z][^\\.>\/=%{}:"]*[\\s]+('.$term.')[\\s]+[^\\.\/<=%{}:"]*/i';

      if(preg_match($regex, $content, $matches)==1)
      {
        $match[0] = str_replace("nbsp;", "", $matches[0]);
        $match[0] = str_replace("quot;", "", $matches[0]);        
        $snippet = html_entity_decode($matches[0], ENT_QUOTES | ENT_XML1 | ENT_COMPAT | ENT_HTML5, 'UTF-8');
        break;
      }
  
    }
  }

}
?>

<?php
$regex =  '/[A-Z][^\\.>\/=%{}:"]*[\\s]+('.$term.')[\\s]+[^\\.\/<=%{}:"]*/i';
$snippet1 = "null";
if (preg_match($regex, $snippet, $match)==1)
{ 
$match[0] = str_replace("nbsp;", "", $match[0]);
$match[0] = str_replace("quot;", "", $match[0]);
$snippet1 = html_entity_decode($match[0], ENT_QUOTES | ENT_XML1 | ENT_COMPAT | ENT_HTML5, 'UTF-8');   
$storeVal = $snippet1;
}

?>



        
<?php
    }
  }
?>
        
      </li>
<?php
  }
?>
    </ol>
<?php
}
}
?>

<?php
if($pageRankActive){
if ($resultsForPageRank)
{
  

  $totalpr = (int) $resultsForPageRank->response->numFound;
  $startpr = min(1,$totalpr);
  $endpr = min($limitperpage, $totalpr);
?>
    <div><b>PageRank Results <?php echo $startpr; ?> - <?php echo $endpr;?> of <?php echo $totalpr; ?>:</b></div>
    <ol>
<?php
  
  foreach ($resultsForPageRank->response->docs as $docRecord)
  {
?>
      <li>
        
<?php
    
    foreach ($docRecord as $docfield => $docvalue)
    {
      if($docfield == "id")
      {

            $parts = explode("/",$docvalue);
            $sizeParts = count($parts);
            $fileName = $parts[$sizeParts-1];

?>
            
            <a href=<?php echo htmlspecialchars($csvArr[$fileName], ENT_NOQUOTES, 'utf-8'); ?> target='_blank'>Document Link</a>
            <p>ID: <?php echo htmlspecialchars($fileName , ENT_NOQUOTES, 'utf-8'); ?></p>          
<?php
    }
    if($docfield == "description"){



?>
        <p>Description: <?php echo htmlspecialchars($docvalue, ENT_NOQUOTES, 'utf-8'); ?> </p>
<?php
    }
  }
?>
        
      </li>
<?php
  }
?>
    </ol>
<?php
}
}
?>

<?php
function getRanking(){
  if(defaultActive){
    return "default";
  }
  else{
    return "pagerank";
  }
}

?>



<script>
/*$('#q').autocomplete({

  source: function(request, response){
    $.ajax({
      url: 'searchpage.php',
      dataType: "json",
      type: 'get',
      data: {
        suggestq : request.term
      }
    });
  }

}); */

$(function() {
    $( "#q" ).autocomplete({
        source: 'autocomplete.php',
        minLength: 1
    });
});


</script>


  </body>
</html>
