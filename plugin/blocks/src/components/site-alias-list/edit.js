/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

/**
 * Internal dependencies
 */
import './editor.scss';

/*
 *
 * @return {Element} Element to render.
 */
export default function Edit() {
	const blockProps = useBlockProps();

	const template = [
		[
			'core/group',
			{
				className: 'wpcloud-block-site-alias-list',
				metadata: {
					name: 'Site Alias List',
				},
			},
			[
				[
					'core/group',
					{
						metadata: { name: 'Primary Domain Row' },
						className:
							'wpcloud-block-site-alias-list__row wpcloud-block-site-alias-list__row--primary',
						layout: {
							type: 'flex',
							flexWrap: 'nowrap',
							justifyContent: 'space-between',
						},
					},
					[
						[
							'wpcloud/site-detail',
							{
								label: __('Primary Domain'),
								name: 'domain_name',
								inline: true,
								hideLabel: true,
								className:
									'wpcloud-block-site-alias-list__item--primary',
								metadata: {
									name: __('Primary Domain'),
								},
							},
						],
						[ 'core/group',
							{
								layout: {
									type: 'flex',
									flexWrap: 'wrap',
								}
							},
							[
								[ 'wpcloud/form',
									{
										wpcloudAction: 'retry_ssl',
										className: 'display-none wpcloud-block-form-retry-ssl'
									},
									[
										[ 'wpcloud/button',
											{
												label: __('Retry SSL'),
												style: 'text',
												type: 'submit',
												isPrimary: false
											}
										]
									]
								],
								[ 'core/paragraph',
									{
										metadata: { name: 'Primary Domain Badge' },
										content: __( 'Primary' ),
										className:
											'wpcloud-block-site-alias-list__item--primary-badge',
									},
								]
							]
						]
					],
				],
				[
					'core/group',
					{
						className:
							'wpcloud-block-site-alias-list__row wpcloud-block-dynamic-row',
						layout: {
							type: 'flex',
							flexWrap: 'wrap',
							justifyContent: 'space-between',
						},
						metadata: {
							name: 'Alias Row',
						},
					},
					[
						[
							'wpcloud/site-detail',
							{
								label: __( 'Alias' ),
								name: 'alias',
								inline: true,
								hideLabel: true,
								metadata: {
									name: __( 'Site Alias' ),
								},
							},
						],
						['core/group',
							{
								className: 'wpcloud-alias-actions',
								style: {
									layout: {
										selfStretch: 'fit',
										flexSize: null,
									},
									spacing: {
										padding: { right: '0', left: '0' },
										margin: { top: '0', bottom: '0' },
										blockGap: '0',
									},
								},
								layout: {
									type: 'flex',
									flexWrap: 'wrap',
									justifyContent: 'left',
								},
								metadata: {
									name: 'Alias Actions',
								},
							},
							[
								[
									'wpcloud/site-detail',
									{
										label: 'Verification Code',
										hideLabel: true,
										showCopyButton: true,
										metadata: {
											name: 'Verification Code',
										},
										className:
											'site-alias-verification-code display-none',
									},
								],
								['wpcloud/icon',
									{
										icon: 'warning',
										className: 'alias-warning display-none',
									},
								],
								[ 'wpcloud/more-menu',
									{
										showMenu: false,
									},
									[
										[
											'core/heading',
											{
												level: 4,
												className:
													'wpcloud-site-list-menu__title',
												content: __( 'Domain Options' ),
											},
										],
										[
											'wpcloud/form',
											{
												ajax: true,
												wpcloudAction:
													'site_alias_make_primary',
												inline: true,
												className:
													'wpcloud-block-site-alias-list--make-primary wpcloud-more-menu__row',
												metadata: {
													name: __( 'Make Primary' ),
												}
											},
											[
												[
													'wpcloud/icon',
													{ icon: 'starEmpty' },
												],
												[
													'wpcloud/form-input',
													{
														type: 'hidden',
														name: 'site_alias',
													},
												],
												[
													'wpcloud/button',
													{
														label: __( 'Make Primary' ),
														style: 'text',
														type: 'submit'
													},
												],
											],
										],
										[
											'wpcloud/form',
											{
												ajax: true,
												wpcloudAction:
													'site_alias_remove',
												inline: true,
												className:
													'wpcloud-block-site-alias-list--remove wpcloud-more-menu__row',
												metadata: {
													name: __( 'Remove Domain' ),
												}
											},
											[
												[ 'wpcloud/icon', { icon: 'trash' } ],
												[
													'wpcloud/form-input',
													{
														type: 'hidden',
														name: 'site_alias',
													},
												],
												[
													'wpcloud/button',
													{
														label: __( 'Remove Domain' ),
														style: 'text',
														type: 'submit'
													},
												],
											],
										],
										[
											'wpcloud/form',
											{
												wpcloudAction: 'request_txt_verification',
												inline: true,
												className:
													'display-none wpcloud-block-form-request_txt_verification wpcloud-more-menu__row',
												metadata: {
													name: __('Request TXT'),
												},
											},
											[
												[
													'wpcloud/icon',
													{ icon: 'warning' },
												],
												[
													'wpcloud/button',
													{
														label: __( 'Request TXT' ),
														style: 'text',
														type: 'submit',
														isPrimary: false
													},
												],
											],
										],
										[
											'wpcloud/form',
											{
												ajax: true,
												wpcloudAction: 'retry_ssl',
												inline: true,
												className:
													'display-none wpcloud-block-form-retry-ssl wpcloud-more-menu__row',
												metadata: {
													name: __('Retry SSL'),
												},
											},
											[
												[
													'wpcloud/icon',
													{ icon: 'warning' },
												],
												[
													'wpcloud/button',
													{
														label: __( 'Retry SSL' ),
														style: 'text',
														type: 'submit',
														isPrimary: false
													},
												],
											],
										]
									]
								],
							]
						]
					],
				],
			],
		],
	];

	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		template,
	} );

	return (
		<div className="wpcloud-block-site-alias-list--wrapper">
			<div
				{ ...innerBlocksProps }
				className={ classNames(
					innerBlocksProps.className,
					'wpcloud-block-site-alias-list'
				) }
			/>
		</div>
	);
}
