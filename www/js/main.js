// function LoaderExtension(naja, loaderSelector) {
//     naja.addEventListener('init', function () {
//         this.loader = document.querySelector(loaderSelector);
//     }.bind(this));
//
//     naja.addEventListener('start', showLoader.bind(this));
//     naja.addEventListener('complete', hideLoader.bind(this));
//
//     function showLoader() {
//         this.loader.style.display = 'block';
//     }
//
//     function hideLoader() {
//         this.loader.style.display = 'none';
//     }
//
//     return this;
// }


document.addEventListener('DOMContentLoaded', () => {
	naja.initialize();
	console.log(naja);
	onLoadHeader();
});

// const xHeader = document.getElementById("my-header");
// console.log(xHeader.classList.add("pin-header"));

function setCookie(cname, cvalue, exdays) {
	var d = new Date();
	d.setTime(d.getTime() + (exdays*24*60*60*1000));
	var expires = "expires="+ d.toUTCString();
	document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
	var name = cname + "=";
	var decodedCookie = decodeURIComponent(document.cookie);
	var ca = decodedCookie.split(';');
	for(var i = 0; i <ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0) == ' ') {
			c = c.substring(1);
		}
		if (c.indexOf(name) == 0) {
			return c.substring(name.length, c.length);
		}
	}
	return "";
}

function setHeaderCookie(state)
{
	setCookie("pin-header", state, 1000);
}

function getHeaderCookie()
{
	return getCookie("pin-header");
}

function toggleBool(state)
{
	if(state == "true")
	{
		return false;
	}
	else
	{
		return true;
	}
}

function onLoadHeader()
{
	var state = getHeaderCookie();
	console.log(state);
	pinHeader(state);

}

function pinHeader(state)
{
	var header = document.getElementById("my-header");

	if(state == true || state == "")
	{
		header.classList.add("fixed-top");
	}
	else
	{
		header.classList.remove("fixed-top");
	}
}

function toggleHeaderTop() {
	var state = getHeaderCookie();
	var newState = toggleBool(state);


	setHeaderCookie(newState);
	pinHeader(newState);
	// console.log("Header new state:"+newState);
}
