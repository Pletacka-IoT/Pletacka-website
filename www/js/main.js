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
	// naja.registerExtension(LoaderExtension, '#loader');
	naja.initialize();
	console.log(naja);
});

naja.addEventListener('before', (event) => {
    console.log(event.request);
});

naja.snippetHandler.addEventListener('afterUpdate', (event) => {
    if (event.snippet.id === 'snippet--sensorTable') {
		//window.alert(event.content);
		console.log("Tabulka");
    }
});

