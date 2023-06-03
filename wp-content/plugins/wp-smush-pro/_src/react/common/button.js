import React from "react";
import classnames from "classnames";

export default function Button(
	{
		id = "",
		text = "",
		color = "",
		dashed = false,
		icon = '',
		loading = false,
		ghost = false,
		disabled = false,
		href = "",
		target = "",
		className = "",
		onClick = () => false,
	}
) {
	function handleClick(e) {
		e.preventDefault();

		onClick();
	}

	function textTag() {
		const iconTag = icon ? <span className={icon} aria-hidden="true"/> : "";
		return (
			<span className={classnames({"sui-loading-text": loading})}>
				{iconTag} {text}
			</span>
		);
	}

	function loadingIcon() {
		return loading
			? <span className="sui-icon-loader sui-loading" aria-hidden="true"/>
			: "";
	}

	let HtmlTag, props;
	if (href) {
		HtmlTag = 'a';
		props = {href: href, target: target};
	} else {
		HtmlTag = 'button';
		props = {
			disabled: disabled,
			onClick: e => handleClick(e)
		};
	}
	const hasText = text && text.trim();

	return (
		<HtmlTag
			{...props}
			className={classnames(className, "sui-button-" + color, {
				"sui-button-onload": loading,
				"sui-button-ghost": ghost,
				"sui-button-icon": !hasText,
				"sui-button-dashed": dashed,
				"sui-button": hasText
			})}
			id={id}
		>
			{textTag()}
			{loadingIcon()}
		</HtmlTag>
	);
}
