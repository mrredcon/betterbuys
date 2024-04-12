function drawProducts(products) {
	let cardHtml = '';
	for (const product of products) {
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

let products = null;

function sortPrice(direction) {
	if (direction === "asc") {
		products.sort((a, b) => {
			return parseFloat(a.price) - parseFloat(b.price);
		});
	} else if (direction === "dsc") {
		products.sort((a, b) => {
			return parseFloat(b.price) - parseFloat(a.price);
		});
	}

	drawProducts(products);
}

function sortName(direction) {
	if (direction === "asc") {
		products.sort((a, b) => {
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
	} else if (direction === "dsc") {
		products.sort((a, b) => {
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

	drawProducts(products);
}

const btnSortName = document.querySelector("#btnSortName");
btnSortName.addEventListener("click", () => {
	sortName(btnSortName.value);

	if (btnSortName.value === "asc") {
		btnSortName.innerHTML = 'Sort by name <i class="icon-sort-by-alphabet"></i>';
		btnSortPrice.innerHTML = "Sort by price";
		btnSortName.value = "dsc";
	} else if (btnSortName.value === "dsc") {
		btnSortName.innerHTML = 'Sort by name <i class="icon-sort-by-alphabet-alt"></i>';
		btnSortPrice.innerHTML = "Sort by price";
		btnSortName.value = "asc";
	}
});

const btnSortPrice = document.querySelector("#btnSortPrice");
btnSortPrice.addEventListener("click", () => {
	sortPrice(btnSortPrice.value);

	if (btnSortPrice.value === "asc") {
		btnSortPrice.innerHTML = 'Sort by price <i class="icon-sort-by-order"></i>';
		btnSortName.innerHTML = "Sort by name";
		btnSortPrice.value = "dsc";
	} else if (btnSortPrice.value === "dsc") {
		btnSortPrice.innerHTML = 'Sort by price <i class="icon-sort-by-order-alt"></i>';
		btnSortName.innerHTML = "Sort by name";
		btnSortPrice.value = "asc";
	}
});

$.getJSON('/products.php')
	.done(function(data, textStatus, jqXHR) {
		products = data;
		//drawProducts(data);
		sortName("asc");
	})
	.fail(function(jqXHR, textStatus, errorThrown) {
		alert('Get failed! Error: ' + jqXHR.status + ' ' + errorThrown);
	});
