(function() {
	window.onload = function() {
		var fields = ['pass', 'pass_repeat'];
		for(var i=0; i<fields.length; i++) {
			document.getElementById(fields[i]).value = '';
		}
	}
})();