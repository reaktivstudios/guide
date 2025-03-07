/**
 * WordPress Dependencies
 */
import { useEntityRecords } from '@wordpress/core-data';
import { Spinner } from '@wordpress/components';

/**
 * External dependencies.
 */
import { createBrowserRouter, RouterProvider } from 'react-router-dom';
/**
 * Internal Dependencies
 */
import './admin.scss';
import { AdminToolbar } from './toolbar';
import { AdminContent } from './content';

export const AdminInterface = () => {
	// Get the site guide posts.
	const {
		records: posts,
		hasResolved,
		status,
	} = useEntityRecords('postType', 'site-guide', {
		per_page: -1,
	});

	if (!hasResolved) return <Spinner />;

	// Convoluted router used so we can create history and craft
	const router = createBrowserRouter([
		{
			element: <AdminToolbar posts={posts} status={status} />,
			loader: async ({ request }) => {
				const url = new URL(request.url);
				const articleSlug = url.searchParams.get('article');
				return articleSlug;
			},
			children: [
				{
					path: '*',
					element: <AdminContent posts={posts} status={status} />,
					loader: async ({ request }) => {
						const url = new URL(request.url);
						const articleSlug = url.searchParams.get('article');
						return articleSlug;
					},
				},
			],
		},
	]);

	return <RouterProvider router={router} basename="wp-admin/admin.php" />;
};
