import apiFetch from "@wordpress/api-fetch";

class Api {
	constructor() {
		this.config = window?.wpdeveloperLicenseManagerConfig;

		// if (this.config?.nonce !== undefined) {
		// 	apiFetch.use(apiFetch.createRootURLMiddleware(rootURL));
		// 	apiFetch.use(apiFetch.createNonceMiddleware(this.config?.nonce));
		// }
	}

	post = async (endpoint, args = {}) => {
		return await this.request({ endpoint, method: "POST", ...args });
	}

	delete = async (endpoint, args = {}) => {
		return await this.request({ endpoint, method: "DELETE", ...args });
	}

	get = async (endpoint, args = {}) => {
		return await this.request({ endpoint, method: "GET", ...args });
	}


	getPath = (path, method = 'GET') => {
		if (this.config.apiType === 'ajax') {
			return `${this.config?.api_url}?action=${this?.config?.action}/${path}`
		}

		return `${this.config?.api_url}${path}`
	}

	request = async ({ endpoint, ...args }) => {
		args.url = this.getPath(endpoint, args?.method);

		if (this.config.apiType === 'ajax' && args?.method !== 'GET') {
			let newData = args?.data != undefined ? { ...args?.data } : {};

			newData = {
				...newData,
				action: `${this?.config?.action}/${endpoint}`,
				_nonce: this?.config?.nonce
			}

			args.headers = {};

			const formData = new FormData();
			for (var key in newData) {
				formData.append(key, newData[key]);
			}

			args.body = formData;
			delete args.data;
		}

		return await apiFetch(args)
			.catch((error) => console.error("Licensing Error: ", error))
	}

	useQuery = (search) => {
		return new URLSearchParams(search);
	}

	getParam = (param, d = null) => {
		const query = this.useQuery(location?.search);
		return query.get(param) || d;
	}
}

export default Api;