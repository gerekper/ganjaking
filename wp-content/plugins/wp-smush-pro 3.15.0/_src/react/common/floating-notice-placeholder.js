import React from "react";

export default function FloatingNoticePlaceholder({id = ''}) {
	return <div className="sui-floating-notices">
		<div role="alert"
			 id={id}
			 className="sui-notice"
			 aria-live="assertive">
		</div>
	</div>;
}
