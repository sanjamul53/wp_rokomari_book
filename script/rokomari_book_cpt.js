window.addEventListener('load', () => {
	if (!author_list || !db_saved_data) return null;

	const price_input = document.getElementById('rokomari_book_price');
	const discount_input = document.getElementById('rokomari_book_discount');
	const main_author_input = document.getElementById(
		'rokomari_book_main_author'
	);
	const other_author_input = document.getElementById(
		'rokomari_book_other_author'
	);
	const language_input = document.getElementById('rokomari_book_language');
	const checkbox_input = document.querySelectorAll('.rokomari_book_category');
	const modified_fileds_input = document.getElementById(
		'rokomari_book_modified_fields'
	);

	let selected_main_author = null;
	let selected_other_author = {};
	let selected_category = {};
	let modified_fields = {};

	if (db_saved_data.main_author) {
		selected_main_author = db_saved_data.main_author;
	}

	if (db_saved_data.categories) {
		db_saved_data.categories.forEach(el => {
			selected_category = {
				...selected_category,
				[el]: true,
			};
		});
	}

	// =========================== err and modified handler ===========================

	const renderModifiedInput = () => {

		let innterTxt = '';

		Object.keys(modified_fields).forEach(el => {

			if (modified_fields[el]) {
				innterTxt = `${innterTxt} 
        <option value="${el}" selected="selected" >
          ${el} 
        </option>`;
			}
		});

		modified_fileds_input.innerHTML = innterTxt;

	};

	const publish_btn_handler = () => {

		const publish_btn = document.querySelector(
			'.editor-post-publish-button__button'
		);

		if (!publish_btn) return null;

		publish_btn.disabled = true;

		if (!price_input.value || Number(price_input.value) < 0) return null;

		if (
			!discount_input.value ||
			Number(discount_input.value) < 0 ||
			Number(discount_input.value) > 100
		) {
			return null;
		}

		if (!selected_main_author) return null;

		if (!language_input.value) return null;

		// get total selected category
		let selected_cat = 0;
		Object.values(selected_category).forEach(el => {
			if (el) selected_cat++;
		});

		if (selected_cat < 1) return null;

		// enable the button
		publish_btn.disabled = false;

	};

	const handle_functions = () => {

		renderModifiedInput();
		publish_btn_handler();

	};

	// =========================== render main author ===========================
	const renderMainAuthor = () => {

		let innerTxt = '';

		innerTxt = `${innerTxt} <option value> Select an Author </option>`;

		Object.keys(author_list).forEach(el => {
			let currentTxt = `<option value="${el}"`;

			if (`${selected_main_author}` === `${el}`) {
				currentTxt = `${currentTxt} selected="selected"`;
			}

			currentTxt = `${currentTxt} > ${author_list[el]} </option>`;

			innerTxt = `${innerTxt} ${currentTxt}`;
		});

		main_author_input.innerHTML = innerTxt;

		// check if author is selected
		if (!selected_main_author) {
			other_author_input.disabled = true;
		} else {
			other_author_input.disabled = false;
		}
	};

	const renderOtherAuthor = () => {
		let innerTxt = '';

		Object.keys(author_list).forEach(el => {
			let currentTxt = `<option value="${el}"`;

			if (`${selected_main_author}` === `${el}`) {
				currentTxt = `${currentTxt} disabled="disabled"`;
			} else if (selected_other_author[`${el}`]) {
				currentTxt = `${currentTxt} selected="selected"`;
			}

			currentTxt = `${currentTxt} > ${author_list[el]} </option>`;

			innerTxt = `${innerTxt} ${currentTxt}`;
		});

		other_author_input.innerHTML = innerTxt;

	};

	// ================= event handler functions =================

	// =========== main author input hanlder
	const main_author_input_hanlder = e => {

		const value = e.target.value;

		if (!value) {
			selected_main_author = null;
		} else {
			selected_main_author = `${value}`;
		}

		modified_fields = {
			...modified_fields,
			main_author: db_saved_data.main_author != value ? true : false,
		};

		renderMainAuthor();
		renderOtherAuthor();
		handle_functions();

	};

	// =========== other author input hanlder
	const other_author_input_hanlder = e => {
		const target = e.target;

		if (target.tagName !== 'OPTION') return null;

		const currentValue = target.value;

		if (!selected_other_author[`${currentValue}`]) {
			selected_other_author[`${currentValue}`] = true;
		} else {
			selected_other_author[`${currentValue}`] = false;
		}

		if (selected_main_author) {
			selected_other_author[`${selected_main_author}`] = false;
		}

		modified_fields = {
			...modified_fields,
			other_author: true,
		};

		renderOtherAuthor();
		handle_functions();
	};

	// =========== price event hanlder
	const price_input_handler = e => {

		let value = e.target.value;

		modified_fields = {
			...modified_fields,
			price: db_saved_data.price == value ? false : true,
		};

		handle_functions();
	};

	// =========== discount event hanlder
	const discount_input_handler = e => {

		const value = e.target.value;

		modified_fields = {
			...modified_fields,
			discount: db_saved_data.discount == value ? false : true,
		};

		handle_functions();
	};

	// =========== language event hanlder
	const language_input_handler = e => {

		const value = e.target.value;

		modified_fields = {
			...modified_fields,
			language: db_saved_data.language == value ? false : true,
		};

		handle_functions();
	};

	// =========== checkbox event hanldler
	const checkbox_input_hanlder = e => {

		const saved_category = ['10', '15', '20'];

		const value = `${e.target.value}`;

		selected_category = {
			...selected_category,
			[value]: selected_category[value] ? false : true,
		};

		// get currently selected category
		let current_category = Object.keys(selected_category).filter(el => {
			if (selected_category[el]) {
				return el;
			}
		});

		// check if saved_category and current_category is modified
		let is_modified = false;

		if (current_category.length !== saved_category.length) {
			is_modified = true;
		} else {
			const isNotFound = saved_category.find(
				el => !current_category.includes(el)
			);

			if (isNotFound) {
				is_modified = true;
			}
		}

		modified_fields = {
			...modified_fields,
			category: is_modified ? true : false,
		};

		handle_functions();
	};

	// ========================== event hanlder ==========================
	price_input.addEventListener('input', price_input_handler);
	discount_input.addEventListener('input', discount_input_handler);
	language_input.addEventListener('change', language_input_handler);
	main_author_input.addEventListener('change', main_author_input_hanlder);
	other_author_input.addEventListener('click', other_author_input_hanlder);

	checkbox_input.forEach(el => {
		el.addEventListener('change', checkbox_input_hanlder);
	});

	// ========================== render inputs ==========================
	renderMainAuthor();
	renderOtherAuthor();

	setTimeout(() => {
		const publish_btn = document.querySelector(
			'.editor-post-publish-button__button'
		);
		publish_btn.disabled = true;

		publish_btn_handler();
	}, 1000);

});
