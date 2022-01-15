"use strict";

function getDirectoryList(serverCaller, cont, root, functionRelease = false) {
 
    /* send an ajax request */
    $.post(serverCaller, { dir: root }, function(data) {
        console.log(data);
            $(cont).append(data); /* append the data to the div */
            if(!functionRelease) /* check for the first run */
                $(cont).find('UL:hidden').show();
            else /* subsequent calls will slide the list with animation */
                $(cont).find('UL:hidden').slideDown({ duration: 500, easing: null }); 
    });

    if (!functionRelease) {
        getDirectoryContents(serverCaller, cont);
    }
}

function getDirectoryContents(serverCaller, cont) {
    $(cont).on(`click`, `li a`, function () { /* monitor the click event on links */

        var entry = $(this).parent(); /* get the parent element of the link */

        if (entry.hasClass(`folder`)) { /* check if it has folder as class name */
            if (entry.hasClass(`collapsed`)) { /* check if it is collapsed */
                entry.find(`UL`).remove(); /* if there is any UL remove it */
                getDirectoryList(serverCaller, entry, $(this).attr(`rel`), true); /* initiate Ajax request */
                entry.removeClass(`collapsed`).addClass(`expanded`); /* mark it as expanded */
            } else { /* if it is expanded already */
                entry.find(`UL`).slideUp({ duration: 500, easing: null }); /* collapse it */
                entry.removeClass(`expanded`).addClass(`collapsed`); /* mark it as collapsed */
            }
        } else { /* clicked on file */
            $(`#selected_file`).text(`File:  ${$(this).attr("rel")}`);
        }
        return false;
    });
}

/*
* Gets query parameter from URL based on name
* @param {string} name of query parameter
* @returns {string} value of query parameter
*/
function getParameterByName(name) {
    const url = window.location.href;
    name = name.replace(/[\[\]]/g, `\\$&`);
    const regex = new RegExp(`[?&]` + name + `(=([^&#]*)|&|#|$)`),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return ``;
    return decodeURIComponent(results[2].replace(/\+/g, ` `));
}

/*
 * Escape any characters on client side to HTML entities
 */
if (typeof escapeHtmlEntities == 'undefined') {
    let escapeHtmlEntities = function (text) {
        return text.replace(/[\u00A0-\u2666<>\&]/g, function (c) {
            return '&' +
                (escapeHtmlEntities.entityTable[c.charCodeAt(0)] || '#' + c.charCodeAt(0)) + ';';
        });
    };

    /*
     *all HTML4 entities as defined here: http://www.w3.org/TR/html4/sgml/entities.html
     */
    escapeHtmlEntities.entityTable = {
        34: 'quot',
        38: 'amp',
        39: 'apos',
        60: 'lt',
        62: 'gt',
        160: 'nbsp',
        161: 'iexcl',
        162: 'cent',
        163: 'pound',
        164: 'curren',
        165: 'yen',
        166: 'brvbar',
        167: 'sect',
        168: 'uml',
        169: 'copy',
        170: 'ordf',
        171: 'laquo',
        172: 'not',
        173: 'shy',
        174: 'reg',
        175: 'macr',
        176: 'deg',
        177: 'plusmn',
        178: 'sup2',
        179: 'sup3',
        180: 'acute',
        181: 'micro',
        182: 'para',
        183: 'middot',
        184: 'cedil',
        185: 'sup1',
        186: 'ordm',
        187: 'raquo',
        188: 'frac14',
        189: 'frac12',
        190: 'frac34',
        191: 'iquest',
        192: 'Agrave',
        193: 'Aacute',
        194: 'Acirc',
        195: 'Atilde',
        196: 'Auml',
        197: 'Aring',
        198: 'AElig',
        199: 'Ccedil',
        200: 'Egrave',
        201: 'Eacute',
        202: 'Ecirc',
        203: 'Euml',
        204: 'Igrave',
        205: 'Iacute',
        206: 'Icirc',
        207: 'Iuml',
        208: 'ETH',
        209: 'Ntilde',
        210: 'Ograve',
        211: 'Oacute',
        212: 'Ocirc',
        213: 'Otilde',
        214: 'Ouml',
        215: 'times',
        216: 'Oslash',
        217: 'Ugrave',
        218: 'Uacute',
        219: 'Ucirc',
        220: 'Uuml',
        221: 'Yacute',
        222: 'THORN',
        223: 'szlig',
        224: 'agrave',
        225: 'aacute',
        226: 'acirc',
        227: 'atilde',
        228: 'auml',
        229: 'aring',
        230: 'aelig',
        231: 'ccedil',
        232: 'egrave',
        233: 'eacute',
        234: 'ecirc',
        235: 'euml',
        236: 'igrave',
        237: 'iacute',
        238: 'icirc',
        239: 'iuml',
        240: 'eth',
        241: 'ntilde',
        242: 'ograve',
        243: 'oacute',
        244: 'ocirc',
        245: 'otilde',
        246: 'ouml',
        247: 'divide',
        248: 'oslash',
        249: 'ugrave',
        250: 'uacute',
        251: 'ucirc',
        252: 'uuml',
        253: 'yacute',
        254: 'thorn',
        255: 'yuml',
        402: 'fnof',
        913: 'Alpha',
        914: 'Beta',
        915: 'Gamma',
        916: 'Delta',
        917: 'Epsilon',
        918: 'Zeta',
        919: 'Eta',
        920: 'Theta',
        921: 'Iota',
        922: 'Kappa',
        923: 'Lambda',
        924: 'Mu',
        925: 'Nu',
        926: 'Xi',
        927: 'Omicron',
        928: 'Pi',
        929: 'Rho',
        931: 'Sigma',
        932: 'Tau',
        933: 'Upsilon',
        934: 'Phi',
        935: 'Chi',
        936: 'Psi',
        937: 'Omega',
        945: 'alpha',
        946: 'beta',
        947: 'gamma',
        948: 'delta',
        949: 'epsilon',
        950: 'zeta',
        951: 'eta',
        952: 'theta',
        953: 'iota',
        954: 'kappa',
        955: 'lambda',
        956: 'mu',
        957: 'nu',
        958: 'xi',
        959: 'omicron',
        960: 'pi',
        961: 'rho',
        962: 'sigmaf',
        963: 'sigma',
        964: 'tau',
        965: 'upsilon',
        966: 'phi',
        967: 'chi',
        968: 'psi',
        969: 'omega',
        977: 'thetasym',
        978: 'upsih',
        982: 'piv',
        8226: 'bull',
        8230: 'hellip',
        8242: 'prime',
        8243: 'Prime',
        8254: 'oline',
        8260: 'frasl',
        8472: 'weierp',
        8465: 'image',
        8476: 'real',
        8482: 'trade',
        8501: 'alefsym',
        8592: 'larr',
        8593: 'uarr',
        8594: 'rarr',
        8595: 'darr',
        8596: 'harr',
        8629: 'crarr',
        8656: 'lArr',
        8657: 'uArr',
        8658: 'rArr',
        8659: 'dArr',
        8660: 'hArr',
        8704: 'forall',
        8706: 'part',
        8707: 'exist',
        8709: 'empty',
        8711: 'nabla',
        8712: 'isin',
        8713: 'notin',
        8715: 'ni',
        8719: 'prod',
        8721: 'sum',
        8722: 'minus',
        8727: 'lowast',
        8730: 'radic',
        8733: 'prop',
        8734: 'infin',
        8736: 'ang',
        8743: 'and',
        8744: 'or',
        8745: 'cap',
        8746: 'cup',
        8747: 'int',
        8756: 'there4',
        8764: 'sim',
        8773: 'cong',
        8776: 'asymp',
        8800: 'ne',
        8801: 'equiv',
        8804: 'le',
        8805: 'ge',
        8834: 'sub',
        8835: 'sup',
        8836: 'nsub',
        8838: 'sube',
        8839: 'supe',
        8853: 'oplus',
        8855: 'otimes',
        8869: 'perp',
        8901: 'sdot',
        8968: 'lceil',
        8969: 'rceil',
        8970: 'lfloor',
        8971: 'rfloor',
        9001: 'lang',
        9002: 'rang',
        9674: 'loz',
        9824: 'spades',
        9827: 'clubs',
        9829: 'hearts',
        9830: 'diams',
        338: 'OElig',
        339: 'oelig',
        352: 'Scaron',
        353: 'scaron',
        376: 'Yuml',
        710: 'circ',
        732: 'tilde',
        8194: 'ensp',
        8195: 'emsp',
        8201: 'thinsp',
        8204: 'zwnj',
        8205: 'zwj',
        8206: 'lrm',
        8207: 'rlm',
        8211: 'ndash',
        8212: 'mdash',
        8216: 'lsquo',
        8217: 'rsquo',
        8218: 'sbquo',
        8220: 'ldquo',
        8221: 'rdquo',
        8222: 'bdquo',
        8224: 'dagger',
        8225: 'Dagger',
        8240: 'permil',
        8249: 'lsaquo',
        8250: 'rsaquo',
        8364: 'euro'
    };
}

function calculate_percentage(amount, percentage) {

    // IN CASE PERCENTAGE SIGN HAS BEEN PASSED ALONG, STRIP IT
    let splitter = percentage.split("%");
    let purePercentage = splitter[0];

    let onePercent = amount / purePercentage;

    let valueOfPercentage = onePercent * purePercentage;

    return valueOfPercentage;
}

/*
* Gets query parameter from URL based on name
* @param {string} name of query parameter
* @returns {string} value of query parameter
*/
function getParameterByName(name) {
  const url = window.location.href;
  name = name.replace(/[\[\]]/g, `\\$&`);
  const regex = new RegExp(`[?&]` + name + `(=([^&#]*)|&|#|$)`),
	  results = regex.exec(url);
  if (!results) return null;
  if (!results[2]) return ``;
  return decodeURIComponent(results[2].replace(/\+/g, ` `));
}

function in_array(needle, haystack) {
    for(var i in haystack) {
        if(haystack[i] == needle) return true;
    }
    return false;
}
