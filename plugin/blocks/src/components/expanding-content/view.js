((wpcloud) => {
	wpcloud.hooks.addAction('wpcloud_expanding_section_toggle', 'wpcloud', (button) => {
		const section = button.closest('.wpcloud-block-expanding-section');
		const wrapper = section.querySelector('.wpcloud-block-expanding-section__content-wrapper');
		wrapper.classList.toggle('section-toggled');
		section.classList.toggle('section-toggled');
		wrapper.ontransitionend = () => {
			wrapper.classList.toggle('is-open');
			section.classList.toggle('is-open');
			wpcloud.hooks.doAction('wpcloud_expanding_section_toggle_end', wrapper.classList.contains('is-open'), section);
		};
	});

})( window.wpcloud );