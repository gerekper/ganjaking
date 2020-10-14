export default function truncateStringMiddle(str: string) {
	var maxLength = 50;

	if (!str) {
		return str;
	}

	if (str.length > maxLength) {
		return str.substr(0, maxLength * .45) + ' ... ' + str.substr(str.length - (maxLength * .4), str.length);
	}

	return str;
}
