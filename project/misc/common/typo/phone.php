<?php
/*
Les numéros de téléphone se composent de plusieurs groupes de chiffres séparés entre eux par une espace, sans point ni trait d'union.

Numéros ordinaires
S'ils sont destinés uniquement à une diffusion nationale, les numéros en France s'écrivent en général en 5 tranches de deux chiffres :
   01 47 87 11 11

S'ils sont destinés uniquement à une diffusion internationale, on omettra le zéro :
   +33 1 47 87 11 11

S'ils sont destinés à une diffusion nationale et internationale, on placera le zéro entre parenthèses :
   +33 (0)1 47 87 11 11

Références
    Lexique de règles typographiques en usage à l'Imprimerie nationale,
    Imprimerie nationale, Paris, 1990
     
    Yves Perrousseaux, Manuel de typographie française élémentaire
    à l'usage des personnes qui pratiquent la PAO,
    Atelier Perrousseaux Éditeur, Reillanne, 1995
     
    France Télécom, La numérotation à 10 chiffres,
    18-10-1996 
*/
defined('_JEXEC') or die;

$type = 'inter'; /* nat inter both */
$tel = '';
$nat = '';
$inter = '';
$both = '';
$class = 'o-tab-phone';
$class = $class ? ' class="' . $class . '"' : '';
$digit = preg_replace('/\D/', '', $value);
$pos = strpos($value, '+');
$phone = array();
$phone['indic'] = '33';
$phone['zero'] = '(0)';
$phone['separator'] = ' ';
$pattern['fr-FR'][0] = '~([0-9]{2})([0-9]{1})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})~';
$pattern['fr-FR'][1] = '~([0-9]{2})([0-9]{1})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{1})~';

preg_match($pattern['fr-FR'][0], $digit, $matchesA);
preg_match($pattern['fr-FR'][1], $digit, $matchesB);

if ($pos === false && strlen($digit) == 10)
{ /* 05 57 35 38 90 */
    $phone['tel'] = 'tel:+' . $phone['indic'] . ltrim($digit, '0');
    $phone['nat'] = wordwrap($digit, 2, $phone['separator'], true);
    $phone['inter'] = "+" . $phone['indic'] . $phone['separator'] . ltrim(wordwrap($digit, 2, $phone['separator'], true) , '0');
    $phone['both'] = "+" . $phone['indic'] . $phone['separator'] . $phone['zero'] . ltrim(wordwrap($digit, 2, $phone['separator'], true) , '0');
}
else
{
    if ($matchesA[1] == $phone['indic'])
    {
        if ($matchesA[2] == "0")
        { /* +33 (0)5 57 35 38 90 */
            $phone['tel'] = 'tel:+' . $matchesB[0];
            $phone['nat'] = wordwrap($matchesB[2] . $matchesB[3] . $matchesB[4] . $matchesB[5] . $matchesB[6] . $matchesB[7], 2, $phone['separator'], true);
            $phone['inter'] = "+" . $phone['indic'] . $phone['separator'] . ltrim(wordwrap($matchesB[2] . $matchesB[3] . $matchesB[4] . $matchesB[5] . $matchesB[6] . $matchesB[7], 2, $phone['separator'], true) , '0');
            $phone['both'] = "+" . $phone['indic'] . $phone['separator'] . '(' . $matchesB[2] . ')' . ltrim(wordwrap('0' . $matchesB[3] . $matchesB[4] . $matchesB[5] . $matchesB[6] . $matchesB[7], 2, $phone['separator'], true) , '0');
        }
        else
        { /* +33 5 57 35 38 90 */
            $phone['tel'] = 'tel:+' . $matchesA[0];
            $phone['nat'] = wordwrap("0" . $matchesA[2] . $matchesA[3] . $matchesA[4] . $matchesA[5] . $matchesA[6], 2, $phone['separator'], true);
            $phone['inter'] = "+" . $phone['indic'] . $phone['separator'] . ltrim(wordwrap('0' . $matchesA[2] . $matchesA[3] . $matchesA[4] . $matchesA[5] . $matchesA[6], 2, $phone['separator'], true) , '0');
            $phone['both'] = "+" . $phone['indic'] . $phone['separator'] . $phone['zero'] . $matchesA[2] . $phone['separator'] . wordwrap($matchesA[3] . $matchesA[4] . $matchesA[5] . $matchesA[6], 2, $phone['separator'], true);
        }
    }
    else
    {
        $phone['tel'] = 'tel:+' . preg_replace('/\D/', '', preg_replace('/\([0-9]+\)/', '', $value));
        $phone['nat'] = $value;
        $phone['inter'] = $value;
        $phone['both'] = $value;
    }
}

$typo = '<a'.$class.' href="'.$phone['tel'].'"><span class="octo-phone"></span><span class="title">'.$phone[$type].'</span></a>';
?>