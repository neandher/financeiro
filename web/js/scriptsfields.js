function MM_findObj(n, d) { //v4.01
    var p, i, x;
    if (!d) d = document;
    if ((p = n.indexOf("?")) > 0 && parent.frames.length) {
        d = parent.frames[n.substring(p + 1)].document;
        n = n.substring(0, p);
    }
    if (!(x = d[n]) && d.all) x = d.all[n];
    for (i = 0; !x && i < d.forms.length; i++) x = d.forms[i][n];
    for (i = 0; !x && d.layers && i < d.layers.length; i++) x = MM_findObj(n, d.layers[i].document);
    if (!x && d.getElementById) x = d.getElementById(n);
    return x;
}

function YY_checkform() { //v4.69
//copyright (c)1998,2002 Yaromat.com
    var a = YY_checkform.arguments, oo = true, v = '', s = '', err = false, r, o, at, o1, t, i, j, ma, rx, cd, cm, cy, dte, at;
    for (i = 1; i < a.length; i = i + 4) {
        if (a[i + 1].charAt(0) == '#') {
            r = true;
            a[i + 1] = a[i + 1].substring(1);
        } else {
            r = false
        }
        o = MM_findObj(a[i].replace(/\[\d+\]/ig, ""));
        o1 = MM_findObj(a[i + 1].replace(/\[\d+\]/ig, ""));
        v = o.value;
        t = a[i + 2];
        if (o.type == 'text' || o.type == 'password' || o.type == 'hidden') {
            if (r && v.length == 0) {
                err = true
            }
            if (v.length > 0)
                if (t == 1) { //fromto
                    ma = a[i + 1].split('_');
                    if (isNaN(v) || v < ma[0] / 1 || v > ma[1] / 1) {
                        err = true
                    }
                } else if (t == 2) {
                    rx = new RegExp("^[\\w\.=-]+@[\\w\\.-]+\\.[a-z]{2,4}$");
                    if (!rx.test(v))err = true;
                } else if (t == 3) { // date
                    ma = a[i + 1].split("#");
                    at = v.match(ma[0]);
                    if (at) {
                        cd = (at[ma[1]]) ? at[ma[1]] : 1;
                        cm = at[ma[2]] - 1;
                        cy = at[ma[3]];
                        dte = new Date(cy, cm, cd);
                        if (dte.getFullYear() != cy || dte.getDate() != cd || dte.getMonth() != cm) {
                            err = true
                        }
                        ;
                    } else {
                        err = true
                    }
                } else if (t == 4) { // time
                    ma = a[i + 1].split("#");
                    at = v.match(ma[0]);
                    if (!at) {
                        err = true
                    }
                } else if (t == 5) { // check this 2
                    if (o1.length)o1 = o1[a[i + 1].replace(/(.*\[)|(\].*)/ig, "")];
                    if (!o1.checked) {
                        err = true
                    }
                } else if (t == 6) { // the same
                    if (v != MM_findObj(a[i + 1]).value) {
                        err = true
                    }
                }
        } else if (!o.type && o.length > 0 && o[0].type == 'radio') {
            at = a[i].match(/(.*)\[(\d+)\].*/i);
            o2 = (o.length > 1) ? o[at[2]] : o;
            if (t == 1 && o2 && o2.checked && o1 && o1.value.length / 1 == 0) {
                err = true
            }
            if (t == 2) {
                oo = false;
                for (j = 0; j < o.length; j++) {
                    oo = oo || o[j].checked
                }
                if (!oo) {
                    s += '* ' + a[i + 3] + '\n'
                }
            }
        } else if (o.type == 'checkbox') {
            if ((t == 1 && o.checked == false) || (t == 2 && o.checked && o1 && o1.value.length / 1 == 0)) {
                err = true
            }
        } else if (o.type == 'select-one' || o.type == 'select-multiple') {
            if (t == 1 && o.selectedIndex / 1 == 0) {
                err = true
            }
        } else if (o.type == 'textarea') {
            if (v.length < a[i + 1]) {
                err = true
            }
        }
        if (err) {
            s += '* ' + a[i + 3] + '\n';
            err = false
        }
    }
    if (s != '') {
        alert('O Sistema informa:\t\t\t\t\t\n\n' + s)
    }
    document.MM_returnValue = (s == '');
}

function Mascara(objeto, evt, mask) {

    var LetrasU = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    var LetrasL = 'abcdefghijklmnopqrstuvwxyz';
    var Letras = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    var Numeros = '0123456789';
    var Fixos = '().-:/ ';
    var Charset = " !\"#$%&\'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\]^_/`abcdefghijklmnopqrstuvwxyz{|}~";

    evt = (evt) ? evt : (window.event) ? window.event : "";
    var value = objeto.value;
    if (evt) {
        var ntecla = (evt.which) ? evt.which : evt.keyCode;
        tecla = Charset.substr(ntecla - 32, 1);
        if (ntecla < 32) return true;

        var tamanho = value.length;
        if (tamanho >= mask.length) return false;

        var pos = mask.substr(tamanho, 1);
        while (Fixos.indexOf(pos) != -1) {
            value += pos;
            tamanho = value.length;
            if (tamanho >= mask.length) return false;
            pos = mask.substr(tamanho, 1);
        }

        switch (pos) {
            case '#' :
                if (Numeros.indexOf(tecla) == -1) return false;
                break;
            case 'A' :
                if (LetrasU.indexOf(tecla) == -1) return false;
                break;
            case 'a' :
                if (LetrasL.indexOf(tecla) == -1) return false;
                break;
            case 'Z' :
                if (Letras.indexOf(tecla) == -1) return false;
                break;
            case '*' :
                objeto.value = value;
                return true;
                break;
            default :
                return false;
                break;
        }
    }
    objeto.value = value;
    return true;
}

function MaskTelefone(objeto, evt) {
    return Mascara(objeto, evt, '(##) ####-####');
}

function MaskHora(objeto, evt) {
    return Mascara(objeto, evt, '##:##');
}

function MaskData(objeto, evt) {
    return Mascara(objeto, evt, '##/##/####');
}

function MaskIdade(objeto, evt) {
    return Mascara(objeto, evt, '##');
}

function MaskNumber(objeto, evt) {
    return Mascara(objeto, evt, '##');
}

function MaskAno(objeto, evt) {
    return Mascara(objeto, evt, '####');
}

function MaskCEP(objeto, evt) {
    return Mascara(objeto, evt, '##.###-###');
}

function MaskCPF(objeto, evt) {
    return Mascara(objeto, evt, '###.###.###-##');
}

function MaskCPFCNPJ(objeto, evt) {
    return Mascara(objeto, evt, '##############');
}

function MaskInscEst(objeto, evt) {
    return Mascara(objeto, evt, '###.###.###.###');
}

function MaskRG(objeto, evt) {
    return Mascara(objeto, evt, '#.###.###');
}

function Limpar(valor, validos) {
// retira caracteres invalidos da string
    valor = valor + "";

    var result = "";
    var aux;
    for (var i = 0; i < valor.length; i++) {
        aux = validos.indexOf(valor.substring(i, i + 1));
        if (aux >= 0) {
            result += aux;
        }
    }
    return result;
}

function Formata(campo, tammax, teclapres, decimal) {
    var tecla = teclapres.keyCode;
    vr = Limpar(campo.value, "0123456789");
    tam = vr.length;
    dec = decimal

    if (tam < tammax && tecla != 8) {
        tam = vr.length + 1;
    }

    if (tecla == 8) {
        tam = tam - 1;
    }

    if (tecla == 8 || tecla >= 48 && tecla <= 57 || tecla >= 96 && tecla <= 105) {

        if (tam <= dec) {
            campo.value = vr;
        }

        if ((tam > dec) && (tam <= 5)) {
            campo.value = vr.substr(0, tam - 2) + "," + vr.substr(tam - dec, tam);
        }
        if ((tam >= 6) && (tam <= 8)) {
            campo.value = vr.substr(0, tam - 5) + "." + vr.substr(tam - 5, 3) + "," + vr.substr(tam - dec, tam);
        }
        if ((tam >= 9) && (tam <= 11)) {
            campo.value = vr.substr(0, tam - 8) + "." + vr.substr(tam - 8, 3) + "." + vr.substr(tam - 5, 3) + "," + vr.substr(tam - dec, tam);
        }
        if ((tam >= 12) && (tam <= 14)) {
            campo.value = vr.substr(0, tam - 11) + "." + vr.substr(tam - 11, 3) + "." + vr.substr(tam - 8, 3) + "." + vr.substr(tam - 5, 3) + "," + vr.substr(tam - dec, tam);
        }
        if ((tam >= 15) && (tam <= 17)) {
            campo.value = vr.substr(0, tam - 14) + "." + vr.substr(tam - 14, 3) + "." + vr.substr(tam - 11, 3) + "." + vr.substr(tam - 8, 3) + "." + vr.substr(tam - 5, 3) + "," + vr.substr(tam - 2, tam);
        }
    }
}