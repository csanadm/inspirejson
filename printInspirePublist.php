<?php
function printInspirePublist($url,$Nmaxauth)
{
  if($Nmaxauth<1) $Nmaxauth = 1; // MAX OF AUTHORS TO BE PRINTED

  // OPEN FILE AND DECODE JSON CONTENTS
  $file = file_get_contents($url);
  if($file === FALSE) return ;
  $json = json_decode($file);

  // DETERMINE NUMBER OF RECORDS ASSOCIATED WITH QUERY
  $Ntotalrec = $json->{"hits"}->{"total"};
  $Nremainingrec = $Ntotalrec;

  // START TO PROCESS RECORDS AND PRINT AS HTML LIST
  echo "<ol>\n";
  while($Nremainingrec>0)
  {
    // PROCESS RECORDS OF CURRENT JSON DATASET
    $Nthisrec = count($json->{"hits"}->{"hits"});
    for($irec=0;$irec<$Nthisrec;$irec++)
    {
      $record = $json->{"hits"}->{"hits"}[$irec];                       // RECORD CONTENTS
      $id = $record->{"id"};                                            // INSPIRE ID
      $metadata = $record->{"metadata"};                                // ALL METADATA
      $cited = $metadata->{"citation_count"};                           // CITATION COUNT
      $indcited = $metadata->{"citation_count_without_self_citations"}; // INDEPENDENT CITATIONS
      $authors = $metadata->{"authors"};                                // AUTHOR OBJECT

      // CREATE PRINTABLE AUTHOR LIST
      $Nauth = count($authors);
      $authlist = "";
      if($Nauth > $Nmaxauth)
      {
        $authlist = $authors[0]->{"full_name"}." <i>et al.</i>";
      }
      else
      {
        for($iauth = 0; $iauth < $Nauth; $iauth++)
        {
          $thisauthor = $authors[$iauth]->{"full_name"};
          $authlist .= ($iauth==0?"":", ").$thisauthor;
        }
      }

      $title = $metadata->{"titles"}[0]->{"title"};                     // PAPER TITLE
      $texkey = $metadata->{"texkeys"}[0];                              // PAPER TEXKEY
      $eprint = $metadata->{"arxiv_eprints"}[0]->{"value"};             // EPRINT ID
      $arXclass = $metadata->{"arxiv_eprints"}[0]->{"categories"}[0];   // ARXIV CLASS
      $doctype = $metadata->{"document_type"}[0];                       // DOCUMENT TYPE
      $reportnumber = $metadata->{"report_numbers"}[0]->{"value"};      // REPORT NUMBER
      $publinfo = $metadata->{"publication_info"};                      // PUBLICATION INFO OBJECT
      $publother = $publinfo[0]->{"pubinfo_freetext"};                  // PUBLICATION INFO FREE TEXT
      $journal = $publinfo[0]->{"journal_title"};                       // JOURNAL
      $volume = $publinfo[0]->{"journal_volume"};                       // VOLUME
      $year = $publinfo[0]->{"year"};                                   // YEAR
      $page = $publinfo[0]->{"page_start"};                             // PAGE
      $artid = $publinfo[0]->{"artid"};                                 // PAPER ID
      if($page == "") $page = $artid;

      // PRINT RECORD
      echo "<li>\n";
      echo "<b><a href=\"http://inspirehep.net/literature/$id\">$title</a></b>,<br>\n";
      echo "$authlist,<br>\n";
      if($journal != "") echo "$journal <b>$volume</b> ($year) $page";
      elseif($doctype == "thesis") echo "PhD thesis";
      elseif($reportnumber != "") echo "$reportnumber";
      else echo "$doctype".($publother!=""?", $publother":" [INSPIRE record #$id]");
      if($eprint!="") echo ", [<a href=\"http://arxiv.org/abs/$eprint\">arXiv:$eprint</a>]";
      if($cited>0) echo ", <a href=\"https://inspirehep.net/literature?q=refersto:recid:$id\">cited $cited</a>";
      echo "<br>\n";
      echo "</li>\n";
    }

    // CHECK IF ALL RECORDS DONE
    $Nremainingrec -= $Nthisrec;
    if($Nremainingrec==0) break;
    else
    {
      $file = file_get_contents($url);
      $json = json_decode($file);
    }
  }
  echo "</ol>\n";
}
