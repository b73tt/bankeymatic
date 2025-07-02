function convert(fromtime) {
	let times = [1, 7, 14, 30.5, 91.3125, 365.25];
	let daily = document.getElementById("conversion" + fromtime).value / fromtime;

	for (let time in times) {
		if (times[time] == fromtime) continue;
		document.getElementById("conversion" + times[time]).value = (daily*times[time]).toFixed(2);
	}
}
