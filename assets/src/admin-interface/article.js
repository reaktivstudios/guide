/**
 * WordPress Dependencies
 */
import { __ } from '@wordpress/i18n';
import { tag, link } from '@wordpress/icons';
import { Icon } from '@wordpress/components';
import { decodeEntities } from '@wordpress/html-entities';
import { useEntityRecords } from '@wordpress/core-data';

export const AdminArticle = ({ currentPost }) => {
	const { hasResolved: termsHaveResolved, records: typeTerms } = useEntityRecords(
		'taxonomy',
		'guide-type',
		{
			per_page: -1,
		}
	);

	if (!currentPost > 0) {
		return <div className="single"></div>;
	}

	return (
		<div className="single">
			<h1 className="article-title">{decodeEntities(currentPost.title.rendered)}</h1>
			<div className="article-meta">
				{currentPost.link && (
					<a href={currentPost.link}>
						<Icon icon={link} />
						{__('Link', 'rkv')}
					</a>
				)}
				{termsHaveResolved && (
					<ul className="guide-type-terms">
						{currentPost['guide-type'].map((typeID) => {
							return (
								<li key={typeID}>
									<Icon icon={tag} />
									{typeTerms.find((term) => term.id === typeID).name}
								</li>
							);
						})}
					</ul>
				)}
			</div>
			<div
				dangerouslySetInnerHTML={{
					__html: currentPost.content.raw.trim(),
				}}
				className="single-content"
			></div>
		</div>
	);
};
