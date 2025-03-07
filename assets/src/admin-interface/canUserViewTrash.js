import { useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';

/**
 * There does not appear to be a simple way to determine if a user has access to a Post Type's trash.
 *
 * This returns a boolean based on if the user has access to any of the trashed posts.
 */
const canUserViewTrash = () => {
	// Can the user have access to the trash?
	const canUserHaveAccessToTrash = useSelect((select) => {
		const { getEntityRecords, canUser } = select('core');

		// Get the trashed posts.
		const trashedPosts = getEntityRecords('postType', 'site-guide', {
			status: 'trash',
			per_page: -1,
		});

		// Determine if the user has delete access to any of the posts.
		const canUsertrashedPosts = trashedPosts?.some((post) => {
			const result = canUser('delete', 'site-guide', post.id);
			// Mini return.
			return result;
		});

		// Return.
		return canUsertrashedPosts;
	}, []);

	// Final return.
	return canUserHaveAccessToTrash;
};

export default canUserViewTrash;
