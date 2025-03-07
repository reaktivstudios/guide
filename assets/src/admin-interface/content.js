/**
 * WordPress Dependencies
 */
import { useState, useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

/**
 * External
 */
import { useLoaderData } from 'react-router-dom';
import { clsx } from 'clsx';
/**
 * Internal dependencies
 */
import { AdminCards } from './cards';
import { AdminArticle } from './article';

export const AdminContent = ({ posts, status }) => {
	// If there are no posts, return early.
	if ('ERROR' == status || posts.length < 1) {
		return <div>{__('No site guide articles to display', 'rkv-site-guide')}</div>;
	}

	const [currentView, setCurrentView] = useState(false);

	// Set current post.
	const [currentPost, setCurrentPost] = useState();

	// Get the article slug from the Router loader data.
	const articleSlug = useLoaderData();

	// Get the current post from the posts array.
	useEffect(() => {
		const currentPost = posts.find((post) => post.slug === articleSlug);
		currentPost ? setCurrentPost(currentPost) : setCurrentPost(false);
	}, [articleSlug]);

	useEffect(() => {
		setCurrentView(currentPost?.id > 0 ? 'article' : 'posts');
	}, [currentPost]);

	useEffect(() => {
		if ('article' === currentView) {
			// Ensure that upon switching to a single article, the scrollbar is at the top.
			// This may not be the case if the article chosen from the card view was at the very bottom.
			window.scrollTo({ top: 0 });
		} else if ('posts' === currentView) {
			// Restore scroll position when navigating back to the cards.
			window.scrollTo({ top: scrollTop });
		}
	}, [currentView]);

	const [scrollTop, setScrollTop] = useState(0);

	useEffect(() => {
		const handleScroll = () => {
			if ('posts' === currentView) {
				setScrollTop(window.scrollY);
			}
		};

		window.addEventListener('scroll', handleScroll);

		return () => {
			window.removeEventListener('scroll', handleScroll);
		};
	}, [currentView]);

	// Wrapper element classnames.
	const wrapperClassNames = clsx('site-guide-view', `viewing-${currentView}`);

	if (undefined === currentView) {
		return <></>;
	}

	return (
		<div className={wrapperClassNames}>
			<AdminCards posts={posts} />
			<AdminArticle currentPost={currentPost} />
		</div>
	);
};
