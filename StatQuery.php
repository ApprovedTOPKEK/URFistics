<?php

//Include necessary scripts
include_once "VarInclude.php";

function statquery($_region, $_mode, $_userid, $_league){
	global $settings;
	$query = "	SELECT * FROM
				(
					SELECT
						AVG(Score) AS Score,
						AVG(Kills) AS Kills,
						AVG(Deaths) AS Deaths,
						AVG(Assists) AS Assists,
						AVG(Wards) AS Wards,
						AVG(Gold) AS Gold,
						AVG(CS) AS CS,
						AVG(Doubles) AS Doubles,
						AVG(Triples) AS Triples,
						AVG(Quadras) AS Quadras,
						AVG(Pentas) AS Pentas,
						AVG(LargestSpree) AS Spree,
						AVG(Drakes) AS Dragons,
						AVG(Barons) AS Barons
					FROM statistics ".
					whereClause($_region, $_mode, $_userid, $_league).
				") AS t1
				 JOIN
				(
				SELECT TopBan1, TopBan2, TopBan3 FROM
				(
				(SELECT TopBan1 FROM
					(SELECT Ban1 AS TopBan1"
						.from($_region, $_mode, $_userid, $_league)
						." UNION ALL SELECT Ban2"
						.from($_region, $_mode, $_userid, $_league)
						." UNION ALL SELECT Ban3"
						.from($_region, $_mode, $_userid, $_league)
					.") AS x3
					GROUP BY TopBan1
					HAVING TopBan1>0
					ORDER BY COUNT(TopBan1) DESC
				) AS x4
				LEFT JOIN
				(SELECT TopBan2 FROM
					(SELECT Ban2 AS TopBan2"
						.from($_region, $_mode, $_userid, $_league)
						." UNION ALL SELECT Ban1"
						.from($_region, $_mode, $_userid, $_league)
						." UNION ALL SELECT Ban3"
						.from($_region, $_mode, $_userid, $_league)
					.") AS x1
					GROUP BY TopBan2
					HAVING TopBan2>0
					ORDER BY COUNT(TopBan2) DESC
				) AS x2
				ON(x4.TopBan1 < x2.TopBan2)
				LEFT JOIN
				(SELECT TopBan3 FROM
					(SELECT Ban3 AS TopBan3"
					.from($_region, $_mode, $_userid, $_league)
					." UNION ALL SELECT Ban1"
					.from($_region, $_mode, $_userid, $_league)
					." UNION ALL SELECT Ban2"
					.from($_region, $_mode, $_userid, $_league)
					.") AS x5
					GROUP BY TopBan3
					HAVING TopBan3>0
					ORDER BY COUNT(TopBan3) DESC
				) AS x6
				ON(x2.TopBan2 < x6.TopBan3)
				) LIMIT 1
				) AS t2
				JOIN
				(
				SELECT TopSpell1, TopSpell2 FROM
				(
				(SELECT TopSpell1 FROM
					(SELECT Spell1 AS TopSpell1".from($_region, $_mode, $_userid, $_league).
					" UNION ALL SELECT Spell2".from($_region, $_mode, $_userid, $_league)
					.") AS y3
					GROUP BY TopSpell1
					HAVING TopSpell1>0
					ORDER BY COUNT(TopSpell1) DESC
				) AS y4
				LEFT JOIN
				(SELECT TopSpell2 FROM
					(SELECT Spell2 AS TopSpell2".from($_region, $_mode, $_userid, $_league).
					" UNION ALL SELECT Spell1".from($_region, $_mode, $_userid, $_league)
					.") AS y1
					GROUP BY TopSpell2
					HAVING TopSpell2 > 0
					ORDER BY COUNT(TopSpell2) DESC
				) AS y2
				ON(y4.TopSpell1 < y2.TopSPell2)
				) LIMIT 1
				) AS t3;";
				//.highestJoin("Ban1", $_region, $_mode, $_userid, $_league, "t2")
				//.highestJoin("Ban2", $_region, $_mode, $_userid, $_league, "t3")
				//.highestJoin("Ban3", $_region, $_mode, $_userid, $_league, "t4")
				//.highestJoin("Pick", $_region, $_mode, $_userid, $_league, "t5")
				//.highestJoin("Spell1", $_region, $_mode, $_userid, $_league, "t6")
				//.highestJoin("Spell2", $_region, $_mode, $_userid, $_league, "t7").";"; //TODO ITEMS
	//echo $query;
	$res = query($query);
	return empty($res)?$res:$res[0];
}


function highestJoin($column, $r, $m, $uid, $l, $t){
	global $settings;
	return " JOIN(SELECT ".$column." FROM statistics ".whereClause($r, $m, $uid, $l)." GROUP BY ".$column." HAVING COUNT(".$column.") > 1) AS ".$t;
}
function whereClause($rg, $m, $uid, $l){
	$q = "WHERE `Region` = ".$rg." AND `Gamemode` = ".$m;
	if($uid != -1) $q .= " AND `UserId` = ".$uid;
	if($l != -1) $q .= " AND `League` = ".$l;
	return $q;
}
function from($rg, $m, $uid, $l){
	return " FROM statistics ".whereClause($rg, $m, $uid, $l);
}

function highestTwo($c1, $c2, $column, $r, $m, $uid, $l, $t){

	return      "SELECT ".$c1.", ".$c2." FROM ( ";

}
?>