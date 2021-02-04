function sucker_bold () {
	bs = document.getElementById("nav").getElementsByTagName('B');
	for (i=0; bs[i]; i++) {
		node = bs[i];
		node.onmouseover=function() { this.className+=" over"; }
		node.onmouseout=function() { this.className=this.className.replace(" over", ""); }
	}
}

addLoadEvent(sucker_bold);