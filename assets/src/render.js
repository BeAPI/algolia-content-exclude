/**
 * WordPress dependencies
 */
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { CheckboxControl } from '@wordpress/components';
import { withSelect, withDispatch } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

/**
 * External dependencies
 */
import { find, isEmpty, map, clone } from 'lodash';

let PluginMetaFields = ( props ) => {
	return (
		<PluginDocumentSettingPanel
			name="alolia-content-exclude"
			title={ __( 'Algolia', 'algolia-content-exclude' ) }
			className="alolia-content-exclude"
		>
			<CheckboxControl
				label={ __(
					'Exclude from Agolia indexing',
					'algolia-content-exclude'
				) }
				checked={ props._algolia_content_exclude }
				onChange={ ( value ) => {
					props.onMetaFieldChange( {
						_algolia_content_exclude: value,
					} );
				} }
			/>
		</PluginDocumentSettingPanel>
	);
};
/**
 * Select the meta field needed
 */
PluginMetaFields = withSelect( ( select ) => {
	/**
	 * Select all the meta needed for the metabox
	 */
	let { _algolia_content_exclude } = select(
		'core/editor'
	).getEditedPostAttribute( 'meta' );

	return {
		_algolia_content_exclude: _algolia_content_exclude || false,
	};
} )( PluginMetaFields );

/**
 * Export the module wrapped into a dispatch that allows us to edit the meta fields
 */
export default withDispatch( ( dispatch ) => {
	return {
		onMetaFieldChange: ( value ) => {
			dispatch( 'core/editor' ).editPost( { meta: value } );
		},
	};
} )( PluginMetaFields );
