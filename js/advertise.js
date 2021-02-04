function gonow(what){
var selectedopt=what.options[what.selectedIndex]
if (document.getElementById && selectedopt.getAttribute("target")=="newwin")
window.open(selectedopt.value)
else
window.location=selectedopt.value
}