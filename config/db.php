<?php
        $pdo = new PDO('mysql:dbname=jeu_combat;host=127.0.0.1', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
