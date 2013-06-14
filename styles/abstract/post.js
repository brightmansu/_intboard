// Copyright (c) 2005 Gram, gram@bg.nnov.ru, http://openproj.ru


////////////// а вот это функции для вставки кодов

function insertcodes() {

        var dir=document.getElementsByTagName('base')[0].href+"/images/";
        HAND = ' cursor: pointer ';

        document.write('<img alt="|" style="width: 12px; height: 26px;"  src="'+dir+'start.png">');
        document.write('<img style="width: 26px; height: 26px;'+HAND+'"  src="'+dir+'bold.png" border=0 alt="'+codes_array[0][1]+'" onClick="custom(0,3);" title="'+codes_array[0][2]+'">');
        document.write('<img style="width: 26px; height: 26px;'+HAND+'"  src="'+dir+'italic.png"  alt="'+codes_array[1][1]+'" onClick="custom(1,3);"  title="'+codes_array[1][2]+'">');
        document.write('<img style="width: 26px; height: 26px;'+HAND+'"  src="'+dir+'underline.png"  alt="'+codes_array[2][1]+'" onClick="custom(2,3);"  title="'+codes_array[2][2]+'">');
        document.write('<img style="width: 26px; height: 26px;'+HAND+'"  src="'+dir+'strikeout.png"  alt="'+codes_array[18][1]+'" onClick="custom(18,3);"  title="'+codes_array[17][2]+'">');
        document.write('<img  alt="|" style="width: 10px; height: 26px;"  src="'+dir+'border.png" >');

        document.write('<select style="vertical-align: 11px" name="ffont" onChange="alterfont(this.options[this.selectedIndex].value, \'font\')"><option value="0">ШРИФТ</option>');
        document.writeln('<option value="Arial" style="font-family:Arial">Arial</option>');
        document.writeln('<option value="Times" style="font-family:Times">Times</option>');
        document.writeln('<option value="Courier" style="font-family:Courier">Courier</option>');
        document.writeln('<option value="Impact" style="font-family:Impact">Impact</option>');
        document.writeln('<option value="Geneva" style="font-family:Geneva">Geneva</option>');
        document.writeln('<option value="Optima" style="font-family:Optima">Optima</option>');
        document.write('</select>');

        document.write('<img style="width: 26px; height: 26px;'+HAND+'"  src="'+dir+'font_small.png"  alt="Малый" onClick="alterfont(1, \'size\');"  title="Тег [size] изменяет размер шрифта. например [size=1]Малый текст[/size]">');
        document.write('<img style="width: 26px; height: 26px;'+HAND+'"  src="'+dir+'font_big.png"  alt="Большой" onClick="alterfont(4, \'size\');"  title="Тег [size] изменяет размер шрифта. например [size=7]Заголовок[/size]">');

        document.writeln('<select style="vertical-align: 9px" name="fcolor" class=select onChange="alterfont(this.options[this.selectedIndex].value, \'color\')"><option value="0">ЦВЕТ</option>');
        document.writeln('<option value="black" style="background:black"> </option>');
        document.writeln('<option value="blue" style="background:blue; "> </option>');
        document.writeln('<option value="red" style="background:red"> </option>');
        document.writeln('<option value="purple" style="background:purple"> </option>');
        document.writeln('<option value="orange" style="background:orange"> </option>');
        document.writeln('<option value="yellow" style="background:yellow"> </option>');
        document.writeln('<option value="gray" style="background:gray"> </option>');
        document.writeln('<option value="green" style="background:green"> </option>');
        document.write('</select>');

        document.write('<img alt="|" style="width: 18px; height: 26px;"  src="'+dir+'end.png" >');
        document.write('<img alt="|" style="width: 12px; height: 26px;"  src="'+dir+'start.png">');
        document.write('<img style="width: 26px; height: 26px;'+HAND+'"  src="'+dir+'quote.png"  alt="'+codes_array[8][1]+'" onClick="custom(8,3);"  title="'+codes_array[8][2]+'">');
        document.write('<img style="width: 26px; height: 26px;'+HAND+'"  src="'+dir+'quote_s.png"  alt="'+codes_array[17][1]+'" onClick="splitQ();"  title="'+codes_array[17][2]+'">');

        document.write('<img style="width: 26px; height: 26px;'+HAND+'"  src="'+dir+'offtop.png"  alt="'+codes_array[7][1]+'" onClick="custom(7,3);" title="'+codes_array[7][2]+'">');
        document.write('<img  alt="|" style="width: 10px; height: 26px;"  src="'+dir+'border.png" >');
        document.write('<img style="width: 26px; height: 26px;'+HAND+'"  src="'+dir+'link.png"  alt="'+codes_array[9][1]+'" onClick="custom(9,2);"  title="'+codes_array[9][2]+'">');
        document.write('<img style="width: 26px; height: 26px;'+HAND+'"  src="'+dir+'mail.png"  alt="'+codes_array[14][1]+'" onClick="custom(14,2);"  title="'+codes_array[14][2]+'">');
        document.write('<img style="width: 26px; height: 26px;'+HAND+'"  src="'+dir+'picture.png"  alt="'+codes_array[10][1]+'" onClick="custom(10,2);"  title="'+codes_array[10][2]+'">');

        document.write('<img alt="|" style="width: 10px; height: 26px;"  src="'+dir+'border.png" >');
        document.write('<img style="width: 26px; height: 26px;'+HAND+'"  src="'+dir+'hr.png"  alt="'+codes_array[15][1]+'" onClick="custom(15,3);" title="'+codes_array[15][2]+'">');
        document.write('<img style="width: 26px; height: 26px;'+HAND+'"  src="'+dir+'center.png"  alt="'+codes_array[12][1]+'" onClick="custom(12,3);"  title="'+codes_array[12][2]+'">');
        document.write('<img style="width: 26px; height: 26px;'+HAND+'"  src="'+dir+'list.png"  alt="'+codes_array[6][1]+'" onClick="custom(6,3);"  title="'+codes_array[6][2]+'">');
        document.write('<img style="width: 26px; height: 26px;'+HAND+'"  src="'+dir+'table.png"  alt="'+codes_array[13][1]+'" onClick="custom(13,3);"  title="'+codes_array[13][2]+'">');
        document.write('<img alt="|" style="width: 26px; height: 26px;"  src="'+dir+'end.png" >');

        var form_cookie = getCookie('IB2XP_form_mode')
        //if (form_cookie != null) document.postform.select.selectedIndex=form_cookie;
        //mode=document.postform.select.selectedIndex+1;

}

function select_onchange(number){
        mode=number
        var form_cookie = getCookie('IB2XP_form_mode');
        form_cookie=mode-1;
        document.cookie="IB2XP_form_mode="+form_cookie+"; expires=Sun, 01-Jan-2034 00:00:00 GMT; path=/;";
}

function c1(num) {
        line1 = "["+codes_array[num][0]+"]";
        line2 = "[/"+codes_array[num][0]+"]";
        if (num == 6) {
                line1 = "[list][*]";
                line2 = "\n[/list]";
        } else if (num == 13) {
                line1 = "[table][tr][td]";
                line2 = "[/td][/tr][/table]";
        }

        if (num==15) line2 = ""
        if (mode==1) alert(codes_array[num][2])
        else if (mode==3) surText(document.postform.p_text,line1, line2)
        else {
                if (codes_array[num][3] == null) AddText(line1)
                else if (codes_array[num][4] == null) {
                        txt=prompt(codes_array[num][3],"")
                        if (txt!=null) AddText(line1+txt+line2)
                } else {
                        txt=prompt(codes_array[num][3],"")
                        if (txt!=null) {
                                txt2=prompt(codes_array[num][4],"")
                                if (txt2!=null) {
                                        if (txt2=="") AddText(line1+txt+line2)
                                        else if (codes_array[num][5] != null) AddText("["+codes_array[num][0]+"="+txt+"]"+txt2+"[/"+codes_array[num][0]+"]")
                                        else AddText("["+codes_array[num][0]+"="+txt2+"]"+txt+"[/"+codes_array[num][0]+"]")
                                }
                        }
                }
        }
        document.postform.p_text.focus();
}

function custom(num, _mode)
{
        mode=_mode;
        c1(num);
}

function AddText(text) {
        if (text!='') insertText(document.postform.p_text, text);
        document.postform.p_text.focus();
}

function pasteN(text) {
  if (text!='')  insertText(document.postform.p_text,"[b]" + text + "[/b]");
}

function insertText(element,text) {
           if (element && element.caretPos) {
                   element.caretPos.text=text;
           } else if (element && element.selectionStart+1 && element.selectionEnd+1) {
                                   element.value=element.value.substring(0,element.selectionStart)+text+element.value.substring(element.selectionEnd,element.value.length);
           } else if (element) {
                   element.value+=text;
           }
   }

function storeCaret(element) {
           if (document.selection && document.selection.createRange) {
               element.caretPos=document.selection.createRange().duplicate();
           }
}


function getCookie(name) {
        var prefix = name + "="
        var StartIndex = document.cookie.indexOf(prefix)
        if (StartIndex == -1)
        return null
        var EndIndex = document.cookie.indexOf(";", StartIndex + prefix.length)
        if (EndIndex == -1)
        EndIndex = document.cookie.length
        return unescape(document.cookie.substring(StartIndex + prefix.length, EndIndex))
}

if (document.selection||document.getSelection) {Q=true} else {var Q=false}


//////////// это для цитаты
function copyQN(name,id) {
  txt='';
  lname=name;
  if (document.getSelection) {txt=document.getSelection()}
  else if (document.selection) {txt=document.selection.createRange().text;}
  if (document.getElementById && !txt && document.getElementById(id).innerText) {txt=document.getElementById(id).innerText;}
  else if (document.getElementById && !txt && document.getElementById(id).textContent) {txt=document.getElementById(id).textContent;}
    txt='[q='+name+']'+txt+'[/q]\n';
}

function splitQ() {
  insertText(document.postform.p_text, '[/q][q='+lname+']');
}

//////////// это для шрифта
function alterfont(theval, thetag) {
        if (theval=='0') return;

        line1='['+thetag+'='+theval+']';
        line2='[/'+thetag+']';
        surText(document.postform.p_text,line1, line2);

        document.postform.ffont.selectedIndex=0;
        document.postform.fsize.selectedIndex=0;
        document.postform.fcolor.selectedIndex=0;
}



function copyQ() {
  txt=''
  if (document.getSelection) {txt=document.getSelection()}
  else if (document.selection) {txt=document.selection.createRange().text;}
  txt='[q]'+txt+'[/q]\n';
}

function pasteQ() {
   if (txt!='') insertText(document.postform.p_text,txt);
}

function surText(element, text1, text2) {
if (element && element.caretPos) {
                  element.caretPos.text = text1 + element.caretPos.text + text2;
          } else if (element && element.selectionStart+1 && element.selectionEnd+1) {
           element.value = element.value.substring(0,element.selectionStart) + text1 + element.value.substring(element.selectionStart,element.selectionEnd) + text2 + element.value.substring(element.selectionEnd,element.value.length);
          } else if (element) {
                  element.value+=text1 + text2;
  }
}