export default ({}) => {
	let setViewPortHeight = () => {
		let viewPortHeight = window.innerHeight;
		document.documentElement.style.setProperty("--viewport-height", viewPortHeight + "px");
	}
	window.addEventListener('resize', setViewPortHeight)

	setViewPortHeight()
}