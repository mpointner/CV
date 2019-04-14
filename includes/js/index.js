var closedCategories = 0;
var printClosedCategoriesWarningEnabled = true;
var printChromeWarningEnabled = true;
function openCloseCategory(id) {
	var item = document.getElementById('items'+id);
	var bereich = document.getElementById('bereich'+id);
	var caret = bereich.getElementsByTagName("h2").item(0).getElementsByTagName("i").item(1);
	if(item.style.display == "none") {
		item.style.display = "block";
		bereich.classList.remove("printHidden");
		caret.classList.remove("fa-caret-up");
		caret.classList.add("fa-caret-down");
		closedCategories--;
	} else {
		item.style.display = "none";
		bereich.classList.add("printHidden");
		caret.classList.remove("fa-caret-down");
		caret.classList.add("fa-caret-up");
		closedCategories++;
	}
}
window.onbeforeprint = function () {
	if(printChromeWarningEnabled && !window.chrome) {
		alert('Please note that this site is optimized for printing in Google Chrome and might be printed incorrectly in other browsers!');
		printChromeWarningEnabled = false;
	}
	if(printClosedCategoriesWarningEnabled && closedCategories > 0) {
		alert('Collapsed categories will not be printed, you have to expand them in case you wish to print them!');
		printClosedCategoriesWarningEnabled = false;
	}
}