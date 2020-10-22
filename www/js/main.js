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
});