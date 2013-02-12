<?php

$host_db = "localhost"; // nom de votre serveur
$user_db = "root"; // nom d'utilisateur de connexion à  votre bdd
$password_db = ""; // mot de passe de connexion à  votre bdd
$bdd_db = "wordpress"; // nom de votre bdd

function generateur($length=8, $possible='$=@#23456789bcdfghjkmnpqrstvwxyz')
{
    $password = '';

    $possible_length = strlen($possible) - 1;


    while ($length--)
    {
        $except = substr($password, -$possible_length / 2);

        for ($n = 0 ; $n < 5 ; $n++)
        {
            $char = $possible{mt_rand(0, $possible_length)};

            if (strpos($except, $char) === false)
            {
                break;
            }
        }

        $password .= $char;
    }

    return $password;
}

?>