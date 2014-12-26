// Main file



var renderize = _('renderize');

//JSEDIT
var jsedit_image = _('jsedit_image');
var jsedit_image_top = 0;
var jsedit_image_left = 0;
var timer = null;

document.ondragend = function(){initialize()}
document.oncontextmenu = function(e){
    jsedit_image_top = e.target.offsetTop;
    jsedit_image_left = e.target.offsetLeft;
    _('jsedit_tab').style.display = "block";
    reflesh();
    return false;
}
initialize();



// JSEDITOR - BEGIN

_('jsedit_tab_dim').onclick = function(e){
    show('jsedit_image_dim');
    //_('jsedit_image_dim').style.display = "block";
    reflesh();
}

_('jsedit_align_left').onclick = function(e){
    _('jsedit_img_active').style.margin = "10px 10px 10px 0";
    _('jsedit_img_active').style.float = "left";
    reflesh();
}

_('jsedit_align_right').onclick = function(e){
    _('jsedit_img_active').style.margin = "10px 0 10px 10px";
    _('jsedit_img_active').style.float = "right";
    reflesh();
}

_('jsedit_width_1').onclick = function(e){
    _('jsedit_img_active').style.width = "100%";
    reflesh();
}
_('jsedit_width_2').onclick = function(e){
    _('jsedit_img_active').style.width = "50%";
    reflesh();
}
_('jsedit_width_3').onclick = function(e){
    _('jsedit_img_active').style.width = "30%";
    reflesh();
}

 // JSEDIT - LINK
_('jsedit_tab_lnk').onclick = function(){
    show('jsedit_form_link');
    //_('jsedit_form_link').style.display = "block";
    reflesh();
}

_('jsedit_link').onkeyup = function(e){

    //removendo LINK se o valor do input for vazio
    if(e.target.value.length == 0) {
        if(_('jsedit_img_active').parentElement.nodeName == 'A'){
            var tmp = _('jsedit_img_active').cloneNode();
            var container = _('jsedit_img_active').parentElement.parentElement;
            _('jsedit_img_active').parentElement.remove();
            container.appendChild(tmp);
        }
        return false;
    }

    //Inserindo LINK
    if(e.keyCode == 13) {
        var container = _('jsedit_img_active').parentElement;
        var tmp = _('jsedit_img_active').cloneNode();
        _('jsedit_img_active').remove();
        var link = document.createElement('a');
        link.href = e.target.value;
        link.appendChild(tmp);
        container.appendChild(link);
        return false;
    }
}


// JSEDIT - INFORMATIONS
_('jsedit_tab_inf').onclick = function(){
    show('jsedit_image_inf');
    reflesh();
}


_('jsedit_tab_ok').onclick = function(e){
    e.target.parentElement.style.display = "none";
    show();
    if(_('jsedit_img_active') != null)
            _('jsedit_img_active').removeAttribute('id');
}


// JSEDIT - DELETE
_('jsedit_tab_x').onclick = function(){
    show('jsedit_image_modal');
}

_('jsedit_image_delete').onclick = function(){
    show();
    _('jsedit_tab').style.display = "none";
    _('jsedit_img_active').remove();
}


// JSEDIT - SALVE
_('jsedit_tab_dw').onclick = function(){
    if(_('jsedit_img_active') != null)
            _('jsedit_img_active').removeAttribute('id');

    var x = _q('.artigo');
    _('jsedit_content_save').value = x[0].innerHTML;
    _('jsedit_form_save').submit();
}

//esconde todas as janelas e mostra somente a indicada em "res"
function show(res){
    var tmp = _q('.jsedit_image');
    for(var i = 0; i < tmp.length; i++){
        tmp[i].style.opacity = 0;
        tmp[i].style.zIndex = -2;
    }

    var res = _(res);
    if(res != null){
        res.style.opacity = .95;
        res.style.zIndex = 2000;
    }
}

//redimensiona as janelas
function reflesh(){
    timer = setTimeout(function(){doReflesh()}, 300);
}
function doReflesh(){
    jsedit_img_active = _('jsedit_img_active');
    if(jsedit_img_active != null){
        jsedit_image_top = jsedit_img_active.offsetTop;
        jsedit_image_left = jsedit_img_active.offsetLeft;
    }


    _('jsedit_tab').style.top = jsedit_image_top+'px';
    _('jsedit_tab').style.left = jsedit_image_left+'px';

    _('jsedit_image_dim').style.top = (jsedit_image_top + 30)+'px';
    _('jsedit_image_dim').style.left = (jsedit_image_left + 90)+'px';

    _('jsedit_form_link').style.top = (jsedit_image_top + 30)+'px';
    _('jsedit_form_link').style.left = (jsedit_image_left + 50)+'px';

    _('jsedit_image_inf').style.top = (jsedit_image_top + 30)+'px';
    _('jsedit_image_inf').style.left = (jsedit_image_left + 10)+'px';

    _('jsedit_image_modal').style.top = (jsedit_image_top + 30)+'px';
    _('jsedit_image_modal').style.left = (jsedit_image_left + 130)+'px';
}

function initialize(){

    var images = document.getElementsByTagName('img');
    var editable = _q('.editable');

    if(editable.length >= 1) editable = editable[0];

    for(i in images){
        images[i].onclick = function(e){

            jsedit_image_top = e.target.offsetTop;
            jsedit_image_left = e.target.offsetLeft;


            //Atribuindo ID para a imagem atual
            if(_('jsedit_img_active') != null)
                _('jsedit_img_active').removeAttribute('id');
            e.target.id = "jsedit_img_active";

            var src = e.target.src;
            var title = e.target.title;
            var alt = e.target.alt;

            //About
            _('jsedit_image_inf_src').value = src;
            _('jsedit_image_inf_title').value = title;
            _('jsedit_image_inf_alt').value = alt;

            //mostrando o editor de imagem
            _('jsedit_tab').style.display = "block";
            reflesh();

        }

        images[i].oncontextmenu = function(e){
            _('jsedit_tab').style.display = "block";
            return false;
        }
    }

    var btclose = _q('.jsedit_close');
    for(i in btclose){
        btclose[i].onclick = function(){
            show();
        }
    }
}

// JSEDITOR - END




//Formate

renderize.onclick = function(e){

        editable.innerHTML = gf_bold(editable.innerHTML);
        editable.innerHTML = gf_italic(editable.innerHTML);
        editable.innerHTML = gf_tached(editable.innerHTML);

        return false;

}






































/* getElementById
 *
 */
function _(e) {
    return document.getElementById(e);
}

/* Query Select
 *
 */
function _q(e){
    var q = document.querySelectorAll(e);
    if(q != null) return q;
    else return false;
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