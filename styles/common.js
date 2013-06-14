var oldformid=0;
function IsForm() {
f=false;
for (i=0;i<document.forms.length && !f;i++) {
if (document.forms[i].name=="postform") f=true;
}
return f;
}

function moveForm(newid) {
if (oldformid!=newid && IsForm()) {
n=document.getElementById('a'+newid);
o=document.getElementById('a'+oldformid);
ftext=document.forms['postform'].p_text.value;
n.innerHTML=o.innerHTML;
o.innerHTML='';
document.forms['postform'].p_text.value=ftext;
oldformid=newid;
}
return false;
}

function ch_imgs() {
var imgs=document.images;
for (i=0;i<imgs.length;i++) if (imgs[i].name=="itag") {
}
var ilayer=document.getElementById('smiles');
if (ilayer) { ilayer.style.display=''; }
var clayer=document.getElementById('codes');
if (clayer) { clayer.style.display=''; }
}

function nav_keys(e) {
var i,j;
var link=null;
var code;
if (!e) e = window.event;
if (e.keyCode) code = e.keyCode;
else if (e.which) code = e.which;
if (code==37 && e.ctrlKey == true && !focused) link = document.getElementById('PrevLink');
if (code==39 && e.ctrlKey == true && !focused) link = document.getElementById('NextLink');
if (code==38 && e.ctrlKey == true && !focused) link = document.getElementById('UpLink');
if (link && link.href) location.href = link.href;
}

var focused=false;