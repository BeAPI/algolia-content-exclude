const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const path = require( 'path' );

module.exports = {
	...defaultConfig,
	entry: {
		metabox: path.resolve( process.cwd(), 'assets/src', 'index.js' ),
	},
	output: {
		filename: 'index.js',
		path: path.resolve( process.cwd(), 'assets/build' ),
	},
};
