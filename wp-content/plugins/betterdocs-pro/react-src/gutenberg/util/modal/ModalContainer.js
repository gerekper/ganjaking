import React, { useState } from "react";
import Modal from "react-modal";

const { useSelect } = wp.data;

const ModalContainer = () => {
	const [isOpen, toggleOpen] = useState(false);
	const onButtonClick = () => {
		toggleOpen(!isOpen);
	};

	const onExportClick = () => {
		const content = useSelect((select) =>
			select("core/editor").getEditedPostContent()
		);
	};

	return (
		<div>
			<button onClick={onButtonClick}>EB Cloud</button>
			<Modal isOpen={isOpen} style={customStyles}>
				<h1>Cloud items here</h1>
				<button onClick={() => onExportClick()}>Export</button>
			</Modal>
		</div>
	);
};

// Styles for modal
const customStyles = {
	content: {
		top: "50%",
		left: "50%",
		right: "auto",
		bottom: "auto",
		marginRight: "-50%",
		transform: "translate(-50%, -50%)",
	},
};

export default ModalContainer;
