/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	Placeholder,
	Spinner,
} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import { megamenuIcon } from './icon';

/**
 * Internal dependencies
 */
import './editor.css';

export default function Edit({ attributes, setAttributes }) {
	const { menuId } = attributes;
	const blockProps = useBlockProps({
		className: 'wp-block-easy-mega-menu-mega-menu',
	});

	const menus = window.emmBlockData?.menus || [];

	const menuOptions = [
		{ value: '', label: __('— Select a menu —', 'easy-mega-menu') },
		...Object.entries(menus).map(([id, menu]) => ({
			value: id,
			label: menu.title || id,
		})),
	];

	return (
		<div {...blockProps}>
			<InspectorControls>
				<PanelBody title={__('Menu Settings', 'easy-mega-menu')}>
					<SelectControl
						label={__('Select Menu', 'easy-mega-menu')}
						value={menuId}
						options={menuOptions}
						onChange={(value) => setAttributes({ menuId: value })}
					/>
				</PanelBody>
			</InspectorControls>

			{!menuId && (
				<Placeholder
					icon={megamenuIcon}
					label={__('Easy Mega Menu', 'easy-mega-menu')}
					instructions={__('Select a mega menu to display.', 'easy-mega-menu')}
				>
					<SelectControl
						value={menuId}
						options={menuOptions}
						onChange={(value) => setAttributes({ menuId: value })}
					/>
				</Placeholder>
			)}

			{menuId && (
				<ServerSideRender
					block="easy-mega-menu/mega-menu"
					attributes={attributes}
					LoadingResponsePlaceholder={() => (
						<div className="emm-block-preview-placeholder">
							<Spinner />
							<p>{__('Loading preview…', 'easy-mega-menu')}</p>
						</div>
					)}
					ErrorResponsePlaceholder={({ response }) => (
						<div className="emm-block-preview-placeholder">
							<p>
								{__('Preview error: ', 'easy-mega-menu')}
								{response?.errorMsg || __('Unknown error', 'easy-mega-menu')}
							</p>
						</div>
					)}
				/>
			)}
		</div>
	);
}