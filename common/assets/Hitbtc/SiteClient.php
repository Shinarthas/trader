<?php
/**
 * system-health
 */

try {
   $dom = new DOMDocument();
    $homepage = file_get_contents('https://hitbtc.com/system-health');
    $html = $dom->loadHTML($homepage);

    $dom->preserveWhiteSpace = true;
    $dom->formatOutput = true;

    $tables = $dom->getElementsByTagName('table');
      
    //get all rows from the table
    $rows = $tables->item(0)->getElementsByTagName('tr');
    // get each column by tag name
    $cols = $rows->item(0)->getElementsByTagName('th');
    $row_headers = NULL;
    foreach ($cols as $node) {
        //print $node->nodeValue."\n";
        $row_headers[] = trim($node->nodeValue);
    }
     
    $table = array();
    //get all rows from the table
    $rows = $tables->item(0)->getElementsByTagName('tr');
    foreach ($rows as $row)
    {
        // get each column by tag name
        $cols = $row->getElementsByTagName('td');
        $row = array();
        $i=0;
        foreach ($cols as $node) {
            # code...
            //print $node->nodeValue."\n";
            if($row_headers==NULL)
                $row[] = trim($node->nodeValue);
            else
                $row[$row_headers[$i]] = trim($node->nodeValue);
            $i++;
        }
        $table[] = $row;
    }
    array_shift($table); 
}
catch(Exception $e)
{ }

function SystemHealth($currency) {
    global $table;
    $return = "error";
    $length = count($table);
    for ($i = 0; $i < $length; $i++) {
        $test = explode("Â", trim(utf8_encode($table[$i]["Cryptocurrency"])))[0];
        if($test == $currency) {
            $return = array("Deposit" => $table[$i]["Deposit"], "Pending deposits" => $table[$i]["Pending deposits"], "Transfer" => $table[$i]["Transfer"], "Trading" => $table[$i]["Trading"], "Withdraw" => $table[$i]["Withdraw"], $table[$i]["Pending withdrawals(with / without hash)"] => $table[$i]["Pending withdrawals(with / without hash)"]);
        }
    }
    return $return;
}
?>