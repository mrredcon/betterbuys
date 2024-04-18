let response = null;
let query = '';
let sortBy = 'name';
let direction = 'asc';
let pageNum = 1;

function changeToPage(newPageNum) {
	if (pageNum == newPageNum) {
		return;
	}

	pageNum = newPageNum;

	$.getJSON('/products.php?query=' + query + '&page=' + pageNum + '&sortBy=' + sortBy + '&direction=' + direction)
		.done(function(data, textStatus, jqXHR) {
			response = data;
			if (sortBy === 'name') {
				sortName(direction);
			} else {
				sortPrice(direction)
			}

			drawPaginator();
		})
		.fail(function(jqXHR, textStatus, errorThrown) {
			$("#productcontainer").html('<h1>No products found.</h1>');
		});
}

function drawPaginator() {
	const BUTTONS = 3;

	const pages = Math.floor(response.total / response.entriesPerPage) + 1;

	const current = response.currentPage;
	let previous = response.currentPage - 1;
	if (previous < 1) {
		previous = 1;
	}

	let next = response.currentPage + 1;
	if (next > pages) {
		next = pages;
	}

	let pageHtml = '';
	pageHtml += `
		<li class="page-item">
			<a class="page-link" aria-label="Previous" onclick="changeToPage(${previous})">
				<span aria-hidden="true">&laquo;</span>
			</a>
		</li>`;

	for (let i = 1; i <= BUTTONS; i++) {
		const label = current + i - 1;

		if (i == BUTTONS && pages > BUTTONS) {
			pageHtml += `<li class="page-item"><a class="page-link" onclick="changeToPage(${pages})">${pages}</a></li>`;
		} else if (label == current) {
			pageHtml += `<li class="page-item active"><a class="page-link" onclick="changeToPage(${label})">${label}</a></li>`;
		} else if (i < BUTTONS) {
			pageHtml += `<li class="page-item"><a class="page-link" onclick="changeToPage(${label})">${label}</a></li>`;
		}

		if (i == pages || label == pages) {
			break;
		}

		if (i == (BUTTONS - 1) && pages > BUTTONS) {
			pageHtml += `
				<li class="page-item">
					<span class="page-link">...</span>
				</li>`;
		}
	}


	pageHtml += `
		<li class="page-item">
			<a class="page-link" aria-label="Next" onclick="changeToPage(${next})">
				<span aria-hidden="true">&raquo;</span>
			</a>
		</li>`;

	$("#paginatorcontainer1").html(pageHtml);
	$("#paginatorcontainer2").html(pageHtml);
}

function drawProducts() {
	let cardHtml = '';
	for (const product of response.data) {
		cardHtml += `
		<div class="col-6 col-lg-3 mb-3 ps-0">
			<div class="card" style="height: 28rem;">
				<img class="card-img-top object-fit-contain mb-auto" style="height: 60%;" src="${product.imagePath}">
				<div class="card-body d-flex flex-column align-items-center" style="height: 40%;">
					<p class="card-text flex-grow-1" style="overflow: hidden; display: -webkit-box; -webkit-line-clamp:2; line-clamp: 2; -webkit-box-orient: vertical;">${product.name}</p>
		  			<br>
		          		<p class="card-text">\$${product.price}</p>
		  			<br>
		
		  			<form method="post" action="shopping_cart.php">
		  				<input type="hidden" name="product_id" value="${product.id}">
		  				<input class="btn btn-primary px-2" type="submit" value="Add to Cart">
		  			</form>
				</div>
			</div>
		</div>`;
	}

	$("#productcontainer").html(cardHtml);
}

function sortPrice(direction) {
	if (direction === "asc") {
		response.data.sort((a, b) => {
			return parseFloat(a.price) - parseFloat(b.price);
		});
	} else if (direction === "desc") {
		response.data.sort((a, b) => {
			return parseFloat(b.price) - parseFloat(a.price);
		});
	}

	drawProducts();
}

function sortName(direction) {
	if (direction === "asc") {
		response.data.sort((a, b) => {
			let aName = a.name.toLowerCase();
			let bName = b.name.toLowerCase();

			if (aName < bName) {
				return -1;
			}

			if (aName > bName) {
				return 1;
			}

			return 0;
		});
	} else if (direction === "desc") {
		response.data.sort((a, b) => {
			let aName = a.name.toLowerCase();
			let bName = b.name.toLowerCase();

			if (aName < bName) {
				return 1;
			}

			if (aName > bName) {
				return -1;
			}

			return 0;
		});
	}

	drawProducts();
}

const btnSortName = document.querySelector("#btnSortName");
btnSortName.addEventListener("click", () => {
	direction = btnSortName.value;
	sortBy = 'name';

	$.getJSON('/products.php?query=' + query + '&page=' + pageNum + '&sortBy=' + sortBy + '&direction=' + direction)
		.done(function(data, textStatus, jqXHR) {
			response = data;
			sortName(btnSortName.value);
			drawPaginator();

			if (btnSortName.value === "asc") {
				btnSortName.innerHTML = 'Sort by name <i class="icon-sort-by-alphabet"></i>';
				btnSortPrice.innerHTML = "Sort by price";
				btnSortName.value = "desc";
			} else if (btnSortName.value === "desc") {
				btnSortName.innerHTML = 'Sort by name <i class="icon-sort-by-alphabet-alt"></i>';
				btnSortPrice.innerHTML = "Sort by price";
				btnSortName.value = "asc";
			}
		})
		.fail(function(jqXHR, textStatus, errorThrown) {
			$("#productcontainer").html('<h1>No products found.</h1>');
		});
});

const btnSortPrice = document.querySelector("#btnSortPrice");
btnSortPrice.addEventListener("click", () => {
	direction = btnSortPrice.value;
	sortBy = 'price';

	$.getJSON('/products.php?query=' + query + '&page=' + pageNum + '&sortBy=' + sortBy + '&direction=' + direction)
		.done(function(data, textStatus, jqXHR) {
			response = data;
			sortPrice(direction)

			drawPaginator();

			if (btnSortPrice.value === "asc") {
				btnSortPrice.innerHTML = 'Sort by price <i class="icon-sort-by-order"></i>';
				btnSortName.innerHTML = "Sort by name";
				btnSortPrice.value = "desc";
			} else if (btnSortPrice.value === "desc") {
				btnSortPrice.innerHTML = 'Sort by price <i class="icon-sort-by-order-alt"></i>';
				btnSortName.innerHTML = "Sort by name";
				btnSortPrice.value = "asc";
			}
		})
		.fail(function(jqXHR, textStatus, errorThrown) {
			$("#productcontainer").html('<h1>No products found.</h1>');
		});
});

$("#formSearch").submit(function(e){
	e.preventDefault();
	const inputSearch = document.querySelector("#inputSearch");
	query = inputSearch.value;

	$.getJSON('/products.php?query=' + query + '&page=' + pageNum + '&sortBy=' + sortBy + '&direction=' + direction)
		.done(function(data, textStatus, jqXHR) {
			response = data;
			if (sortBy === 'name') {
				sortName(direction);
			} else {
				sortPrice(direction)
			}

			drawPaginator();
		})
		.fail(function(jqXHR, textStatus, errorThrown) {
			$("#productcontainer").html('<h1>No products found.</h1>');
		});
});

$.getJSON('/products.php')
	.done(function(data, textStatus, jqXHR) {
		response = data;
		sortName("asc");
		drawPaginator();
	})
	.fail(function(jqXHR, textStatus, errorThrown) {
		alert('Get failed! Error: ' + jqXHR.status + ' ' + errorThrown);
	});
