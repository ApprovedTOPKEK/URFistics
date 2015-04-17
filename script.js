
//Hide all those tables
function hideTables() {
	var leagueStats = document.getElementsByClassName("statTable");
	for (var i = 0; i < leagueStats.length; i++) {
		leagueStats[i].style.display = "none";
	}
}

//Slide the tables
function toggleBtn(outerDiv) {
	$("#" + outerDiv + " .leagueContent .statTable").toggle("slow");
}

//Simple AJAX, no time for cool JSON Parsing and stuff
function searchSummoner() {
	var sumName = $("#inputBar").val();
	if (sumName.length < 4) return;
	$("#loading").show();
	var region = $("#regionSelector").val();
	$.ajax({
		url: "PlayerStatsCombined.php?username=" + sumName.replace(/\s/g, '') + "&pregion=" + region
	}).done(function (data) {
		$("#loading").hide();
		$("#profileSection").html(data);
		/*var arr1 = [];
		 var arr2 = [];
		 var score = $("#playerStattable").children()[0].children()[1].text()
		 $("#stats .specific").each(function(index){
		 arr1[$(this).next(".statTable").children()[0].children()[1].text()-score] = $(this).attr("id").substr(0, $(this).attr("id").length - 5).toUpperCase();
		 });*/
		$("#var").text(compareScoreWith(parseInt($("#ps").text()), parseInt($("#diamondStats .statTable .as").text()), parseInt($("#platinumStats .statTable .as").text()), parseInt($("#goldStats .statTable .as").text()), parseInt($("#silverStats .statTable .as").text()), parseInt($("#bronzeStats .statTable .as").text()), parseInt($("#unrankedStats .statTable .as").text())));
	});
}

//2Am, can't do it flexible and properly now... I should have started this project sooner... This is horrible...
function compareScoreWith(score, dia, plat, gold, silver, bronze, unr) {
	var tmp = [];//[Math.abs(unr-score), Math.abs(bronze-score), Math.abs(silver-score), Math.abs(gold-score), Math.abs(plat-score), Math.abs(dia-score)];
	tmp[Math.abs(unr - score)] = "UNRANKED";
	tmp[Math.abs(bronze - score)] = "BRONZE";
	tmp[Math.abs(silver - score)] = "SILVER";
	tmp[Math.abs(gold - score)] = "GOLD";
	tmp[Math.abs(plat - score)] = "PLATINUM";
	tmp[Math.abs(dia - score)] = "DIAMOND";
	/*var tmp = [];
	 var i = 0;
	 array.forEach(function(val){
	 tmp[i] = Math.abs(val-score);
	 i++;
	 });*/
	return tmp[Math.min.apply(Math, Object.keys(tmp))];
}

function slbt(x){
	$(".statTable").show("slow");
	$("#tds").text(Math.round(jsonData[x]['Score']));
	$("#tb1").attr("src", "images/c" + jsonData[x]['TopBan1'] + ".png");
	$("#tb2").attr("src", "images/c" + jsonData[x]['TopBan2'] + ".png");
	$("#tb3").attr("src", "images/c" + jsonData[x]['TopBan3'] + ".png");
	$("#ts1").attr("src", "images/s" + jsonData[x]['TopSpell1'] + ".png");
	$("#ts2").attr("src", "images/s" + jsonData[x]['TopSpell1'] + ".png");
	$("#mp").attr("src", "images/c" + jsonData[x]['TopPick'] + ".png");
	$("#pi1").attr("src", "http://ddragon.leagueoflegends.com/cdn/5.6.2/img/item/" + jsonData[x]['PopItem1'] + ".png");
	$("#pi2").attr("src", "http://ddragon.leagueoflegends.com/cdn/5.6.2/img/item/" + jsonData[x]['PopItem2'] + ".png");
	$("#pi3").attr("src", "http://ddragon.leagueoflegends.com/cdn/5.6.2/img/item/" + jsonData[x]['PopItem3'] + ".png");
	$("#tdcs").text(Math.round(jsonData[x]['CS']));
	$("#tdg").text(Math.round(jsonData[x]['Gold']));
	$("#tdw").text(Math.round(jsonData[x]['Wards']));
	$("#tdkda").text(Math.round(jsonData[x]['Kills']) + "/" + Math.round(jsonData[x]['Deaths']) + "/" + Math.round(jsonData[x]['Assists']));
}