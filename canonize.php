<? /*

Canonical names library script for Intellect Board 2

(c) 2007, XXXX Pro, United Open Project
Visit us online: http://intboard.ru
*/

function canonize_name($name) {
  $name = str_replace(' ','',strtolower(str_replace("\t",'',$name)));
  $name = strtr($name,"016������������i����","olbabe3ukopcyxblhnmt");
  $name = str_replace("�","lo",$name);
  $name = str_replace("�","bl",$name);
  return $name;
}
