export function showSuccessNotice(id, message, dismissible = true) {
	return showNotice(id, message, 'success', dismissible);
}

export function showErrorNotice(id, message, dismissible = true) {
	return showNotice(id, message, 'error', dismissible);
}

export function showInfoNotice(id, message, dismissible = true) {
	return showNotice(id, message, 'info', dismissible);
}

export function showWarningNotice(id, message, dismissible = true) {
	return showNotice(id, message, 'warning', dismissible);
}

export function closeNotice(id) {
	SUI.closeNotice(id);
}

export function showNotice(id, message, type = 'success', dismissible = true) {
	const icons = {
		error: 'warning-alert',
		info: 'info',
		warning: 'warning-alert',
		success: 'check-tick'
	};

	SUI.closeNotice(id);
	SUI.openNotice(id, '<p>' + message + '</p>', {
		type: type,
		icon: icons[type],
		dismiss: {show: dismissible}
	});
}
