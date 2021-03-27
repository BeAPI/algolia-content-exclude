/**
 * WordPress dependencies
 */
import { registerPlugin } from '@wordpress/plugins';

/**
 * Internal dependencies
 */
import render from './render';

registerPlugin( 'algolia-content-exclude', {
	render,
} );
