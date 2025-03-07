import domReady from '@wordpress/dom-ready';
import { createRoot } from '@wordpress/element';

import { AdminInterface } from './admin-interface';

domReady(() => {
	const root = createRoot(document.getElementById('rkv-site-guide-settings'));

	root.render(<AdminInterface />);
});
