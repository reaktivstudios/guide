/**
 * WordPress Dependencies
 */
import {
	Card,
	CardHeader,
	CardBody,
	CardFooter,
	__experimentalHeading as Heading,
} from '@wordpress/components';
import { decodeEntities } from '@wordpress/html-entities';
import { __ } from '@wordpress/i18n';
import { addQueryArgs } from '@wordpress/url';

/* global Intl */

/**
 * External dependencies.
 */
import { useLocation, useNavigate } from 'react-router-dom';

export const AdminCards = ({ posts }) => {
	const navigate = useNavigate();

	const location = useLocation();

	return (
		<div className="posts">
			{posts.map((post) => (
				<AdminCard
					key={post.id}
					post={post}
					onClick={() => {
						navigate(
							addQueryArgs(location.pathname, {
								page: 'site-guide',
								article: post.slug,
							}),
						);
					}}
				/>
			))}
		</div>
	);
};

export const AdminCard = ({ post, onClick }) => {
	// Nice date formattter.
	const niceDate = (timestamp) => {
		const date = new Date(timestamp);
		// Format the date using Intl.DateTimeFormat
		const options = {
			weekday: 'long', // "Friday"
			year: 'numeric', // "2024"
			month: 'long', // "May"
			day: 'numeric', // "31"
			hour: 'numeric', // "8"
			minute: 'numeric', // "42"
			second: 'numeric', // "58"
			hour12: true, // "PM"
		};
		return new Intl.DateTimeFormat('en-US', options).format(date);
	};

	// Return card.
	return (
		<Card key={post.id} onClick={onClick}>
			<CardHeader className="card-header">
				<Heading level={3} href={post.link}>
					{decodeEntities(post.title.rendered)}
				</Heading>
			</CardHeader>
			<CardBody
				dangerouslySetInnerHTML={{
					__html: post.content.raw.trim(),
				}}
			></CardBody>
			<CardFooter>
				{__('Last Updated: ', 'rkv-guide')}
				{niceDate(post.modified)}
			</CardFooter>
		</Card>
	);
};
