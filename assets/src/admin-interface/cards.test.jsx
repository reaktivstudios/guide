import React from 'react';
import { render, screen, fireEvent } from '@testing-library/react';
import '@testing-library/jest-dom';
import { AdminCards, AdminCard } from './cards';
import { addQueryArgs } from '@wordpress/url';
import { useNavigate } from 'react-router-dom';

jest.unmock('react');

// Mock the @wordpress/components module
jest.mock('@wordpress/components', () => {
    const React = require('react');
    return {
        Card: jest.fn(({ children, onClick }) => React.createElement('div', { onClick }, children)),
        CardHeader: jest.fn(({ children }) => React.createElement('div', null, children)),
        CardBody: jest.fn(({ dangerouslySetInnerHTML }) => React.createElement('div', { dangerouslySetInnerHTML })),
        CardFooter: jest.fn(({ children }) => React.createElement('div', null, children)),
        __experimentalHeading: jest.fn(({ href, children }) => React.createElement('a', { href }, children)),
    };
});

// Mock the @wordpress/html-entities module
jest.mock('@wordpress/html-entities', () => ({
    decodeEntities: jest.fn((str) => str),
}));

// Mock the @wordpress/i18n module
jest.mock('@wordpress/i18n', () => ({
    __: jest.fn((str) => str),
    isRTL: jest.fn(() => false), // Mock isRTL function
}));

// Mock the @wordpress/url module
jest.mock('@wordpress/url', () => ({
    addQueryArgs: jest.fn(),
}));

// Mock the react-router-dom module
jest.mock('react-router-dom', () => ({
    useNavigate: jest.fn(),
    useLocation: jest.fn(() => ({
        pathname: '/some-path',
    })),
}));

describe('AdminCard', () => {
    it('should render card with correct content', () => {
        const post = {
            id: 1,
            link: 'https://example.com/post-1',
            title: { rendered: 'Post 1' },
            content: { raw: 'Content 1' },
            modified: '2023-10-01T12:00:00',
        };

        const onClick = jest.fn();
        render(<AdminCard post={post} onClick={onClick} />);

        const date = new Date(post.modified);
        const options = {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: 'numeric',
            minute: 'numeric',
            second: 'numeric',
            hour12: true,
        };
        const formattedDate = new Intl.DateTimeFormat('en-US', options).format(date);

        const cardFooter = screen.getByText((content, element) => {
            return element.tagName.toLowerCase() === 'div' && content.includes(formattedDate);
        });

        expect(cardFooter).toBeInTheDocument();
    });
});

describe('AdminCards', () => {
    it('should render AdminCard components for each post and handle clicks correctly', () => {
        const posts = [
            { slug: 'post-1', title: { rendered: 'Post 1' }, content: { raw: '<p>Content 1</p>' }, modified: '2023-10-01T12:34:56Z' },
            { slug: 'post-2', title: { rendered: 'Post 2' }, content: { raw: '<p>Content 2</p>' }, modified: '2023-10-02T12:34:56Z' },
        ];

        const navigate = jest.fn();
        useNavigate.mockReturnValue(navigate);

        render(<AdminCards posts={posts} />);

        posts.forEach((post, index) => {
            const cardTitle = screen.getByText(post.title.rendered);
            expect(cardTitle).toBeInTheDocument();

            const cardContent = screen.getByText((content, element) => {
                return element.tagName.toLowerCase() === 'p' && content.includes(post.content.raw.replace(/<[^>]+>/g, ''));
            });
            expect(cardContent).toBeInTheDocument();

            const date = new Date(post.modified);
            const options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: 'numeric',
                minute: 'numeric',
                second: 'numeric',
                hour12: true,
            };
            const formattedDate = new Intl.DateTimeFormat('en-US', options).format(date);

            const cardFooter = screen.getByText((content, element) => {
                return element.tagName.toLowerCase() === 'div' && content.includes(formattedDate);
            });
            expect(cardFooter).toBeInTheDocument();

            fireEvent.click(cardTitle);
            expect(navigate).toHaveBeenCalledWith(
                addQueryArgs('/admin', {
                    page: 'site-guide',
                    article: post.slug,
                })
            );
        });
    });
});
