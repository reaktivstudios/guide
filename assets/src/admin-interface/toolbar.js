/**
 * WordPress Dependencies
 */
import { __ } from '@wordpress/i18n';
import { chevronLeft, addCard, edit, trash } from '@wordpress/icons';
import {
	__experimentalHStack as HStack,
	Button,
	__experimentalConfirmDialog as ConfirmDialog,
} from '@wordpress/components';
import { useSelect, useDispatch } from '@wordpress/data';
import { useEffect, useState } from '@wordpress/element';
import { store as coreStore, useResourcePermissions } from '@wordpress/core-data';
import { store as noticesStore } from '@wordpress/notices';
import { addQueryArgs } from '@wordpress/url';

/**
 * External
 */
import { useLocation, useNavigate, useLoaderData, Outlet } from 'react-router-dom';

/**
 * Internal
 */
import canUserViewTrash from './canUserViewTrash';

export const AdminToolbar = ({ posts, status }) => {
	// Set current post.
	const [currentPost, setCurrentPost] = useState(false);
	const [canViewTrash, setCanViewTrash] = useState(false);

	// Get the article slug from the Router loader data.
	const articleSlug = useLoaderData();

	// Get the current post from the posts array.
	useEffect(() => {
		if ('ERROR' == status) {
			return setCurrentPost(false);
		}
		const currentPost = posts ? posts.find((post) => post.slug === articleSlug) : false;
		currentPost ? setCurrentPost(currentPost) : setCurrentPost(false);
	}, [articleSlug]);

	// We are going to use these for setting the current Route.
	const navigate = useNavigate();
	const location = useLocation();

	// Determine if the current user can view the trash.
	const resultOfCanUserViewTrash = canUserViewTrash();

	useEffect(() => {
		setCanViewTrash(resultOfCanUserViewTrash);
	}, [resultOfCanUserViewTrash]);

	// Get the rest of the user permissions.
	const { canCreate, canDelete, canUpdate } = useResourcePermissions(
		'site-guide',
		currentPost.id ?? '',
	);

	// Post Trashing.
	const { deleteEntityRecord } = useDispatch(coreStore);
	const { getLastEntityDeleteError } = useSelect(coreStore);
	const { createSuccessNotice, createErrorNotice } = useDispatch(noticesStore);

	const [showConfirmDialog, setShowConfirmDialog] = useState(false);

	const { isDeleting } = useSelect(
		(select) => ({
			isDeleting: currentPost.id
				? select(coreStore).isDeletingEntityRecord('postType', 'page', currentPost.id)
				: false,
		}),
		[currentPost.id],
	);

	const handleConfirm = async () => {
		setShowConfirmDialog(false);
		const success = await deleteEntityRecord('postType', 'site-guide', currentPost.id);
		if (success) {
			// Tell the user the operation succeeded:
			createSuccessNotice('The article was deleted!', {
				type: 'snackbar',
			});
			navigate(
				addQueryArgs(location.pathname, {
					page: 'site-guide',
				}),
			);
		} else {
			// We use the selector directly to get the fresh error *after* the deleteEntityRecord
			// have failed.
			const lastError = getLastEntityDeleteError('postType', 'site-guide', currentPost.id);
			const message =
				(lastError?.message || 'There was an error.') + ' Please refresh the page and try again.';
			// Tell the user how exactly the operation has failed:
			createErrorNotice(message, {
				type: 'snackbar',
			});
		}
	};

	return (
		<>
			<HStack className="site-guide-toolbar" justify="space-between" spacing={4} alignment="center">
				<HStack className="navigation" justify="left">
					<h1>{__('Site Guide', 'rkv-guide')}</h1>

					{canViewTrash && (
						<Button
							icon={trash}
							variant="primary"
							href={addQueryArgs('edit.php', {
								post_status: 'trash',
								post_type: 'site-guide',
							})}
						>
							{__('View Trash', 'rkv-guide')}
						</Button>
					)}

					{!!currentPost && currentPost.id > 0 && (
						<Button
							icon={chevronLeft}
							variant="primary"
							onClick={() => {
								navigate(
									addQueryArgs(location.pathname, {
										page: 'site-guide',
									}),
								);
							}}
						>
							{__('All Articles', 'rkv-guide')}
						</Button>
					)}
				</HStack>
				<HStack className="actions" justify="right">
					{canCreate && (
						<Button
							icon={addCard}
							variant="primary"
							href={addQueryArgs('post-new.php', {
								post_type: 'site-guide',
							})}
						>
							{__('New Article', 'rkv-guide')}
						</Button>
					)}

					{canUpdate && (
						<Button
							icon={edit}
							variant="primary"
							href={addQueryArgs('post.php', {
								post: currentPost.id,
								action: 'edit',
							})}
						>
							{__('Edit Article', 'rkv-guide')}
						</Button>
					)}

					{canDelete && (
						<>
							<Button
								icon={trash}
								isDestructive
								variant="secondary"
								isBusy={isDeleting}
								aria-disabled={isDeleting}
								onClick={isDeleting ? undefined : () => setShowConfirmDialog(true)}
							>
								{__('Delete Article', 'rkv-guide')}
							</Button>
							<ConfirmDialog
								isOpen={showConfirmDialog}
								onConfirm={handleConfirm}
								onCancel={() => setShowConfirmDialog(false)}
								confirmButtonText={__('Move to trash')}
								size="medium"
							>
								{__('Are you sure you want to move this article to the trash?')}
							</ConfirmDialog>
						</>
					)}
				</HStack>
			</HStack>
			<Outlet />
		</>
	);
};
