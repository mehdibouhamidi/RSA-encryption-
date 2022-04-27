<?php


/*
 ____   __ __      ___ ___    ___  __ __  ___    ____      ____    ___   __ __  __ __   ____  ___ ___  ____  ___    ____ 
|    \ |  |  |    |   |   |  /  _]|  |  ||   \  |    |    |    \  /   \ |  |  ||  |  | /    ||   |   ||    ||   \  |    |
|  o  )|  |  |    | _   _ | /  [_ |  |  ||    \  |  |     |  o  )|     ||  |  ||  |  ||  o  || _   _ | |  | |    \  |  | 
|     ||  ~  |    |  \_/  ||    _]|  _  ||  D  | |  |     |     ||  O  ||  |  ||  _  ||     ||  \_/  | |  | |  D  | |  | 
|  O  ||___, |    |   |   ||   [_ |  |  ||     | |  |     |  O  ||     ||  :  ||  |  ||  _  ||   |   | |  | |     | |  | 
|     ||     |    |   |   ||     ||  |  ||     | |  |     |     ||     ||     ||  |  ||  |  ||   |   | |  | |     | |  | 
|_____||____/     |___|___||_____||__|__||_____||____|    |_____| \___/  \__,_||__|__||__|__||___|___||____||_____||____|
                                                                                                                         


*/


////retourner le PGCD de deux nombre
function pgcd($a, $b)
{
    if ($b == 0)
        return $a;
    return pgcd($b, $a % $b);
}


////retourner les coefficient de Bézout
function euclide($a, $b)
{
    list($PGCD, $U, $V) = coef_bezout($a, $b, 1, 0, 0, 1);
    return [$PGCD, $U, $V];
}


function inverseModulaire($e, $m)
{
    //return extendted_euclide($e, $m, 0, 1);
    //return modInverse($e, $m);

    /////tester l'inverse
    for ($x = 1; $x < $m; $x++)
        if ((($e % $m) * ($x % $m)) % $m == 1)
            return $x;
}

//// vérifier si un nombre est premier 
function EstPremier($n)
{

    if ($n <= 1)
        return false;

    for ($i = 2; $i < $n; $i++)
        if ($n % $i == 0)
            return false;

    return true;
}

/// retourner un nombre aléatoire compris entre deux valeurs
function premierAleatoire($inf, $lg)
{
    $intervalle = range($inf, $inf + $lg);
    $valeur = array_rand($intervalle);
    if (EstPremier($valeur)) {
        return $valeur;
    } else {
        return premierAleatoire($inf, $lg);
    }
}

/// retourner un nombre aléatoire premier avec nombre en entrée
function premierAleatoireAvec($n)
{
    $val = array_rand(range(2, $n - 1));
    if (pgcd($val, $n) == 1) {
        return $val;
    } else {
        return premierAleatoireAvec($n);
    }
}

/// calculer le modulaire expo
function expoModulaire($a, $n, $m)
{

    // initialiser res
    $res = 1;

    // Mettre a jour a si a plus 
    // grand ou egal a m
    $a = $a % $m;

    if ($a == 0)
        return 0;

    while ($n > 0) {
        // Si n est impair , multiplier 
        // a avec resultat
        if ($n & 1)
            $res = ($res * $a) % $m;

        // n devera être maintenant pair

        // n = $n/2
        $n = $n >> 1;
        $a = ($a * $a) % $m;
    }
    return $res;
}

function choixCle($inf, $lg)
{
    $p = premierAleatoire($inf, $inf + $lg);
    $q = premierAleatoire($p + 1, $p + $lg + 1);
    $phin = ($p - 1) * ($q - 1);
    $e = premierAleatoireAvec($phin);
    return [$p, $q, $e];
}

/// générer clé publique
function clePublique($p, $q, $e)
{
    $n = $p * $q;
    return [$n, $e];
}

/// générer clé privé
function clePrivee($p, $q, $e)
{
    $phin = ($p - 1) * ($q - 1);
    $n = $p * $q;
    $d = inverseModulaire($e, $phin);
    echo ("\n d : " . $d);
    return  [$n, $d];
}

///  crypté en RSA
function codageRSA($M, $clepublique)
{
    list($n, $e) = $clepublique;

    $res = expoModulaire($M, $e, $n);
    return $res;
}
///  decrypté en RSA
function decodageRSA($M, $cleprivé)
{
    list($n, $d) = $cleprivé;

    $res = expoModulaire($M, $d, $n);

    return $res;
}

///////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////
/// TEST /////:

list($p, $q, $e) = choixCle(0, 2000);
$clepublique = clePublique($p, $q, $e);

list($n, $e) = $clepublique;
echo ("\nles clés générés sont :");
echo ("\np : " . $p . ' q : ' . $q . ' e : ' . $e . ' n: ' . $n);

$cleprivé = clePrivee($p, $q, $e);

list($n, $d) = $clepublique;
//echo ("\n d : " . $d);

$M = 112;
echo ("\nle message clair est : " . $M);
$message_crypte = codageRSA($M, $clepublique);

echo ("\nle message crypté est : " . $message_crypte);

$message_decrypte = decodageRSA($message_crypte, $cleprivé);

echo ("\nle message decrypté est : " . $message_decrypte);





//////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////

function modInverse($a, $m)
{

    for ($x = 1; $x < $m; $x++)
        if ((($a % $m) * ($x % $m)) % $m == 1)
            return $x;
}

function extendted_euclide($a, $b, $t1, $t2)
{
    if ($a < $b) {
        $temp = $a;
        $a = $b;
        $b = $temp;
    }

    $reminder = $a % $b;
    $q = (int)($a / $b);
    $t = $t1 - $t2 * $q;
    $a = $b;
    $b = $reminder;
    $t1 = $t2;
    $t2 = $t;
    if ($b == 0) {
        return $t1;
    } else {
        return extendted_euclide($a, $b, $t1, $t2);
    }
}


function coef_bezout($a, $b, $s1, $s2, $t1, $t2)
{
    if ($a < $b) {
        $temp = $a;
        $a = $b;
        $b = $temp;
    }

    $reminder = $a % $b;
    $q = (int)($a / $b);

    $s = $s1 - $s2 * $q;

    $t = $t1 - $t2 * $q;
    $a = $b;
    $b = $reminder;
    $s1 = $s2;
    $s2 = $s;
    $t1 = $t2;
    $t2 = $t;
    if ($b == 0) {
        return [$a, $t1, $s1];
    } else {
        return coef_bezout($a, $b, $s1, $s2, $t1, $t2);
    }
}
