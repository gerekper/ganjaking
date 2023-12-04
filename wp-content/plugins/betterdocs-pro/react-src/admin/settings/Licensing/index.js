import Api from './Api';
import LicenseManager from './LicenseManager';

export { default as ActiveHeading } from './ActiveHeading';
export { default as Heading } from './Heading';
export { default as Instructions } from './Instructions';
export { default as Right } from './Right';

import './licensing.scss';

export default class ManageLicense {
	constructor(config) {
		this.config = { ...window?.wpdeveloperLicenseManagerConfig, ...config };
		this.licenseData = window?.wpdeveloperLicenseData ?? {};
		this.api = new Api();
	}

	render = () => {
		return (
			<LicenseManager
				className={this.config?.classes?.wrapper ?? ''}
				licenseData={this.licenseData}
				config={this.config}
				apiFetch={this.api}
			/>
		)
	}
}