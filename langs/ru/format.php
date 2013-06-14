<? function format_word($count,$word1,$word2,$word3,$prefix1="",$prefix2="",$prefix3="") {
        if (!$count) $count=0;
  if (($count % 10)==1 && ($count %100) !=11) $answ1=(($prefix1)?($prefix1." "):("")).$count." ".$word1;
  elseif ($count % 10>1 && $count % 10<5 && ($count % 100 <10 || $count % 100 >20)) $answ1 = (($prefix2)?($prefix2." "):("")).$count." ".$word2;
  else $answ1=(($prefix3)?($prefix3." "):("")).$count." ".$word3;
  return $answ1;
}
?>