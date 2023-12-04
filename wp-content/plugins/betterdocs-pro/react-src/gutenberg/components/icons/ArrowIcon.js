export default () => {
	return (
		<svg
			aria-hidden="true"
			focusable="false"
			data-prefix="fas"
			data-icon="angle-down"
			class="cat-list-arrow-down svg-inline--fa fa-angle-down fa-w-10"
			role="img"
			xmlns="http://www.w3.org/2000/svg"
			viewBox="0 0 320 512"
		>
			<path
				fill="currentColor"
				d="M143 352.3L7 216.3c-9.4-9.4-9.4-24.6 0-33.9l22.6-22.6c9.4-9.4 24.6-9.4 33.9 0l96.4 96.4 96.4-96.4c9.4-9.4 24.6-9.4 33.9 0l22.6 22.6c9.4 9.4 9.4 24.6 0 33.9l-136 136c-9.2 9.4-24.4 9.4-33.8 0z"
			></path>
		</svg>
	);
};

export const DocIcon = () => {
	return (
		<svg
			xmlns="http://www.w3.org/2000/svg"
			aria-hidden="true"
			width=".86em"
			height="1em"
			style={{
				msTransform: "rotate(360deg)",
				WebkitTransform: "rotate(360deg)",
				transform: "rotate(360deg)",
			}}
			viewBox="0 0 1536 1792"
		>
			<path d="M1468 380q28 28 48 76t20 88v1152q0 40-28 68t-68 28H96q-40 0-68-28t-28-68V96q0-40 28-68T96 0h896q40 0 88 20t76 48zm-444-244v376h376q-10-29-22-41l-313-313q-12-12-41-22zm384 1528V640H992q-40 0-68-28t-28-68V128H128v1536h1280zM384 800q0-14 9-23t23-9h704q14 0 23 9t9 23v64q0 14-9 23t-23 9H416q-14 0-23-9t-9-23v-64zm736 224q14 0 23 9t9 23v64q0 14-9 23t-23 9H416q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h704zm0 256q14 0 23 9t9 23v64q0 14-9 23t-23 9H416q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h704z" />
		</svg>
	);
};

export const SubCatIconRightArrow = () => {
	return (
		<svg
			className="toggle-arrow arrow-right"
			xmlns="http://www.w3.org/2000/svg"
			aria-hidden="true"
			width=".48em"
			height="1em"
			style={{
				msTransform: "rotate(360deg)",
				WebkitTransform: "rotate(360deg)",
				transform: "rotate(360deg)",
			}}
			viewBox="0 0 608 1280"
		>
			<path d="M13 288q0 13 10 23l393 393-393 393q-10 10-10 23t10 23l50 50q10 10 23 10t23-10l466-466q10-10 10-23t-10-23L119 215q-10-10-23-10t-23 10l-50 50q-10 10-10 23z" />
		</svg>
	);
};

export const SubCatIconDownArrow = () => {
	return (
		<svg
			className="toggle-arrow arrow-down"
			xmlns="http://www.w3.org/2000/svg"
			aria-hidden="true"
			width=".8em"
			height="1em"
			style={{
				msTransform: "rotate(360deg)",
				WebkitTransform: "rotate(360deg)",
				transform: "rotate(360deg)",
			}}
			viewBox="0 0 1024 1280"
		>
			<path d="M1011 480q0 13-10 23L535 969q-10 10-23 10t-23-10L23 503q-10-10-10-23t10-23l50-50q10-10 23-10t23 10l393 393 393-393q10-10 23-10t23 10l50 50q10 10 10 23z" />
		</svg>
	);
};
