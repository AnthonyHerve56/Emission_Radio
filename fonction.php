<?php

function afficherMatch(){
    $matchs = array(
        array("equipe1" => "PSG", "equipe2" => "OM", "date" => "2026-10-01","image" => "images/match_1.jpeg"),
        array("equipe1" => "Lyon", "equipe2" => "Marseille", "date" => "2026-10-02","image" => "images/match_2.jpg"),
        array("equipe1" => "Monaco", "equipe2" => "Nice", "date" => "2026-10-03","image" => "images/match_3.jpg")
    );

    echo "<h2>Prochains matchs :</h2>";
    echo "<ul class='match-list'>";
    foreach ($matchs as $match) {
        echo "<li class='match-item'>";
        echo "<a href='match.php?equipe1=" . urlencode($match['equipe1']) . "&equipe2=" . urlencode($match['equipe2']) . "&date=" . urlencode($match['date']) . "'>";
        echo "<img src='" . $match['image'] . "' alt='" . $match['equipe1'] . " vs " . $match['equipe2'] . "'>";
        echo "<div class='match-info'>";
        echo "<span class='match-equipes'>" . $match['equipe1'] . " <strong>VS</strong> " . $match['equipe2'] . "</span>";
        echo "<span class='match-date'>📅 " . $match['date'] . "</span>";
        echo "</div>";
        echo "</a>";
        echo "</li>";
    }
    echo "</ul>";
    echo "</table>";
}




?>