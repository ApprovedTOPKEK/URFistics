function hideTables(){
	var leagueStats = document.getElementsByClassName("statTable");
	for(var i=0; i<leagueStats.length;i++){leagueStats[i].style.display="none";}
}
var btns = [];
function toggleBtn(outerDiv){
	btns[outerDiv] = (!(outerDiv in btns))?true:(!btns[outerDiv]);
	hideTables();
	for(var key in btns){
		document.getElementById(key).getElementsByClassName("statTable")[0].style.display = (btns[key]==true?"block":"none");
	}
}