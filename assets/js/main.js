// Main file


var images = document.getElementsByTagName('img');
var editable = document.querySelectorAll('.editable');

if(editable.length >= 1) editable = editable[0];

for(i in images){
    images[i].onclick = function(e){
        var src = e.target.src;
        var title = e.target.title;
        var alt = e.target.alt;

        alert(e.target.width + ' x ' + e.target.height + ' - source: '+e.target.src);

        //e.target.src = "assets/file/1/3.jpg";
    }

    images[i].oncontextmenu = function(e){
        var i = e.target;
        alert(i.title);
        return false;
    }
}







//Formate

editable.onkeyup = function(e){
    
        editable.innerHTML = gf_bold(editable.innerHTML);
        editable.innerHTML = gf_italic(editable.innerHTML);
        editable.innerHTML = gf_tached(editable.innerHTML);

        return true;
    
}






































/* getElementById
 *
 */
function _(e) {
    return document.getElementById(e);
}

/* _GFORMAT */

//var str = "Este é um teste de *negrito* e não de negritissse* ou outro assim como *este trecho que estou digitando* estará em negrito também.";
//alert(str.match(/(\*\w)(\w+|.+)(\w\*)/g));

function gf_bold(str){ 
    var tmp = str.match(/(\*\w)(\w+|.+)(\w\*)/g);
    for(i in tmp){
        str = str.replace(tmp[i], tmp[i].replace(/\*(.+)\*/g, '<b>$1</b>'));
    }
    return str;
}

function gf_italic(str){ 
    var tmp = str.match(/(\_\w)(\w+|.+)(\w\_)/g);
    for(i in tmp){
        str = str.replace(tmp[i], tmp[i].replace(/\_(.+)\_/g, '<i>$1</i>'));
    }
    return str;
}

function gf_tached(str){ 
    var tmp = str.match(/(\-\w)(\w+|.+)(\w\-)/g);
    for(i in tmp){
        str = str.replace(tmp[i], tmp[i].replace(/\-(.+)\-/g, '<s>$1</s>'));
    }
    return str;
}



function _gformat(txt, searc, subst) {

    var init = -1;
    var fim = 0;
    var cursor = 0;
    var result = '';

    for (i = 0; i < txt.length; i++) {
        if (txt[i] === searc && init === -1 && fim === 0)
            init = i;
        if (txt[i] === searc && init !== -1 && init < i) {
            fim = i;
            var temp = subst[0] + txt.substr((1 + init), (fim - init) - 1) + subst[1];
            result += txt.substr(cursor, (init - cursor)) + temp;

            cursor = (1 + fim);
            init = -1;
            fim = 0;
        }
    }
    if (txt.length > cursor)
        result += txt.substr(cursor, (txt.length - cursor));
    return result;
}