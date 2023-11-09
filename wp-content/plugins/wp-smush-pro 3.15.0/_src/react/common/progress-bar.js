import React from "react";

export default function ProgressBar(
	{
		progress = 0,
		stateMessage = ''
	}
) {
	progress = Math.ceil(progress);
	const progressPercentage = progress + "%";

	return (
		<React.Fragment>
			<div className="sui-progress-block">
				<div className="sui-progress">
						<span className="sui-progress-icon" aria-hidden="true">
							<span className="sui-icon-loader sui-loading"/>
						</span>

					<div className="sui-progress-text">{progressPercentage}</div>

					<div className="sui-progress-bar">
							<span
								style={{
									transition: progress === 0 ? false : "transform 0.4s linear 0s",
									transformOrigin: "left center",
									transform: `translateX(${progress - 100}%)`,
								}}
							/>
					</div>
				</div>
			</div>
			<div className="sui-progress-state">{stateMessage}</div>
		</React.Fragment>
	);
}
