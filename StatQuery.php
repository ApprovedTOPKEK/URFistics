<?php

//Include necessary scripts
include_once "VarInclude.php";

/**
 * @param $_region Region for which the averages etc shall be fetched
 * @param $_mode Mode for which the averages etc shall be fetched
 * @param $_userid UserId for which the averages etc shall be fetched (-1 if none)
 * @param $_league League for which the averages etc shall be fetched (-1 if none)
 * @return Result of the query
 *
 * Complicated - probably inefficient - MySQL query to get averages of "standard"-data, and highest appartion frequency of other data. Not going to explain this one, it's a huge headache
 */
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
					".from($_region, $_mode, $_userid, $_league, 15).
				") AS t1
				 JOIN
				(
				SELECT TopBan1, TopBan2, TopBan3 FROM
				(
				(SELECT TopBan1 FROM
					(SELECT Ban1 AS TopBan1"
						.from($_region, $_mode, $_userid, $_league, 16)
						." UNION ALL SELECT Ban2"
						.from($_region, $_mode, $_userid, $_league, 17)
						." UNION ALL SELECT Ban3"
						.from($_region, $_mode, $_userid, $_league, 18)
					.") AS x3
					GROUP BY TopBan1
					HAVING TopBan1>0
					ORDER BY COUNT(TopBan1) DESC
				) AS x4
				LEFT JOIN
				(SELECT TopBan2 FROM
					(SELECT Ban2 AS TopBan2"
						.from($_region, $_mode, $_userid, $_league, 18)
						." UNION ALL SELECT Ban1"
						.from($_region, $_mode, $_userid, $_league, 19)
						." UNION ALL SELECT Ban3"
						.from($_region, $_mode, $_userid, $_league, 20)
					.") AS x1
					GROUP BY TopBan2
					HAVING TopBan2>0
					ORDER BY COUNT(TopBan2) DESC
				) AS x2
				ON(x4.TopBan1 < x2.TopBan2)
				LEFT JOIN
				(SELECT TopBan3 FROM
					(SELECT Ban3 AS TopBan3"
					.from($_region, $_mode, $_userid, $_league,21)
					." UNION ALL SELECT Ban1"
					.from($_region, $_mode, $_userid, $_league, 22)
					." UNION ALL SELECT Ban2"
					.from($_region, $_mode, $_userid, $_league, 23)
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
					(SELECT Spell1 AS TopSpell1".from($_region, $_mode, $_userid, $_league, 24).
					" UNION ALL SELECT Spell2".from($_region, $_mode, $_userid, $_league, 25)
					.") AS y3
					GROUP BY TopSpell1
					HAVING TopSpell1>0
					ORDER BY COUNT(TopSpell1) DESC
				) AS y4
				LEFT JOIN
				(SELECT TopSpell2 FROM
					(SELECT Spell2 AS TopSpell2".from($_region, $_mode, $_userid, $_league, 26).
					" UNION ALL SELECT Spell1".from($_region, $_mode, $_userid, $_league, 27)
					.") AS y1
					GROUP BY TopSpell2
					HAVING TopSpell2 > 0
					ORDER BY COUNT(TopSpell2) DESC
				) AS y2
				ON(y4.TopSpell1 < y2.TopSpell2)
				) LIMIT 1
				) AS t3

				JOIN
				(
				SELECT PopItem1, PopItem2, PopItem3 FROM
				(
				(SELECT PopItem1 FROM
					(SELECT Item0 AS PopItem1"
						. from($_region, $_mode, $_userid, $_league, 28)
						. " UNION ALL SELECT Item1"
						. from($_region, $_mode, $_userid, $_league, 29)
						. " UNION ALL SELECT Item2"
						. from($_region, $_mode, $_userid, $_league, 30)
						. " UNION ALL SELECT Item3"
						. from($_region, $_mode, $_userid, $_league, 31)
						. " UNION ALL SELECT Item4"
						. from($_region, $_mode, $_userid, $_league,  32)
						. " UNION ALL SELECT Item5"
						. from($_region, $_mode, $_userid, $_league, 33)
					. ") AS z3
					GROUP BY PopItem1
					HAVING PopItem1>0
					ORDER BY COUNT(PopItem1) DESC
				) AS z4
				LEFT JOIN
				(SELECT PopItem2 FROM
					(SELECT Item1 AS PopItem2"
						. from($_region, $_mode, $_userid, $_league, 34)
						. " UNION ALL SELECT Item0"
						. from($_region, $_mode, $_userid, $_league, 35)
						. " UNION ALL SELECT Item2"
						. from($_region, $_mode, $_userid, $_league, 36)
						. " UNION ALL SELECT Item3"
						. from($_region, $_mode, $_userid, $_league, 37)
						. " UNION ALL SELECT Item4"
						. from($_region, $_mode, $_userid, $_league, 38)
						. " UNION ALL SELECT Item5"
						. from($_region, $_mode, $_userid, $_league, 39)
					. ") AS z1
					GROUP BY PopItem2
					HAVING PopItem2>0
					ORDER BY COUNT(PopItem2) DESC
				) AS z2
				ON(z4.PopItem1 < z2.PopItem2)
				LEFT JOIN
				(SELECT PopItem3 FROM
					(SELECT Item2 AS PopItem3"
		 .from($_region, $_mode, $_userid, $_league, 40)
		 . " UNION ALL SELECT Item0"
		 . from($_region, $_mode, $_userid, $_league, 41)
		 . " UNION ALL SELECT Item1"
		 . from($_region, $_mode, $_userid, $_league, 42)
		 . " UNION ALL SELECT Item3"
		 . from($_region, $_mode, $_userid, $_league, 43)
		 . " UNION ALL SELECT Item4"
		 . from($_region, $_mode, $_userid, $_league, 44)
		 . " UNION ALL SELECT Item5"
		 . from($_region, $_mode, $_userid, $_league, 45)
		. ") AS z5
					GROUP BY PopItem3
					HAVING PopItem3>0
					ORDER BY COUNT(PopItem3) DESC
				) AS z6
				ON(z2.PopItem2 < z6.PopItem3)
				) LIMIT 1
				) AS t7

				"
		.highestJoin("Pick", "TopPick", $_region, $_mode, $_userid, $_league, "t4").";";

	//echo $query;
	//echo $query."<br /><br /><br /><br /><br />";
	$res = query($query);
	return empty($res)?$res:$res[0];
}

// Some helper functions to not write the same thing over and over again. This file is a freaking mess. Just don't look at it.
function highestJoin($column, $alias, $r, $m, $uid, $l, $t){
	global $settings;
	return " JOIN(SELECT ".$column." AS ".$alias.from($r, $m, $uid, $l, $t)." GROUP BY ".$column." HAVING COUNT(".$column.") > 1) AS ".$t;
}
function whereClause($rg, $m, $uid, $l){
	$q = "WHERE `Region` = ".$rg." AND `Gamemode` = ".$m;
	if($uid != -1) $q .= " AND `UserId` = ".$uid;
	if($l != -1) $q .= " AND `League` = ".$l;
	return $q;
}
function from($rg, $m, $uid, $l, $x){
	return " FROM ".statselect($x).whereClause($rg, $m, $uid, $l);
}

function highestTwo($c1, $c2, $column, $r, $m, $uid, $l, $t){

	return      "SELECT ".$c1.", ".$c2." FROM ( ";

}

function statselect($x){
	return "(SELECT * FROM statistics ORDER BY matchid DESC LIMIT 0, 5000) AS s".$x." ";
}
?>