/**
 * Filters the posts.
 *
 * Includes:
 * - Search field
 * - Reset button
 */

import { useState } from 'react';

import { TextControl, __experimentalHStack as HStack } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { Icon, close } from '@wordpress/icons';

const Filter = ({ onChangeSearch }) => {
	const [searchTerm, setSearchTerm] = useState('');

	return (
		<div className="guide-filters">
			<HStack className="filter-hstack">
				<div className="search-wrap">
					<TextControl
						className="search-field"
						label={__('Search articles', 'rkv-guide')}
						value={searchTerm}
						__next40pxDefaultSize
						onChange={(value) => {
							setSearchTerm(value);
							onChangeSearch(value);
						}}
					/>
					<button
						className="search-reset"
						onClick={() => {
							setSearchTerm('');
							onChangeSearch('');
						}}
						aria-label={__('Reset', 'rkv-guide')}
					>
						<Icon icon={close} />
					</button>
				</div>
			</HStack>
		</div>
	);
};

export { Filter };
