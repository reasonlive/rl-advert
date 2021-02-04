function refresh_on(_element_id) {
	 var element = document.getElementById(_element_id);
	 if (element) {
		 element.src = element.src + '?' + (new Date()).getMilliseconds()
	}
}
