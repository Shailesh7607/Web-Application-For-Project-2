$(function () {
    // Navigation Scroll Effect
    $(window).scroll(function () {
        if ($(this).scrollTop() > 50) {
            $("#site-top-nav").addClass("site-top-nav");
            $("#back-to-top").fadeIn();
        } else {
            $("#site-top-nav").removeClass("site-top-nav");
            $("#back-to-top").fadeOut();
        }
    });

    // Hamburger Menu Toggle
    $(".hamburger").click(function () {
        $(".menu").toggleClass("active");
    });

    // Shopping Cart Toggle
    $("#shopping-cart").click(function (e) {
        e.preventDefault();
        $("#cart-content").toggle("blind", "", 300);
    });

    // Back to Top
    $("#back-to-top").click(function (e) {
        e.preventDefault();
        $("html, body").animate({ scrollTop: 0 }, 800);
    });

    // Add to Cart Functionality
    $(".add-to-cart-form").submit(function (e) {
        e.preventDefault();
        const $form = $(this);
        const name = $form.find("h4").text();
        const price = parseFloat($form.find(".food-price").text().replace("$", ""));
        const qty = parseInt($form.find(".quantity-input").val());
        const total = price * qty;
        const imgSrc = $form.find("img").attr("src");

        const $cartTable = $("#cart-content .cart-table");
        const $orderTable = $(".tbl-full");
        $cartTable.find(".empty-cart").remove();
        $orderTable.find(".empty-order").remove();

        const $cartRow = $(`
            <tr>
                <td><img src="${imgSrc}" alt="${name}"></td>
                <td>${name}</td>
                <td>$${price.toFixed(2)}</td>
                <td>${qty}</td>
                <td>$${total.toFixed(2)}</td>
                <td><a href="#" class="btn-delete">×</a></td>
            </tr>
        `);

        const $orderRow = $(`
            <tr>
                <td>${$orderTable.find("tr").length}</td>
                <td><img src="${imgSrc}" alt="${name}"></td>
                <td>${name}</td>
                <td>$${price.toFixed(2)}</td>
                <td>${qty}</td>
                <td>$${total.toFixed(2)}</td>
                <td><a href="#" class="btn-delete">×</a></td>
            </tr>
        `);

        $cartTable.append($cartRow);
        $orderTable.append($orderRow);
        updateCartTotal();
        updateBadge();
    });

    // Delete Cart Item
    $(document).on("click", ".btn-delete", function (e) {
        e.preventDefault();
        const $row = $(this).closest("tr");
        const isOrderPage = $row.closest(".tbl-full").length > 0;
        $row.remove();
        if (isOrderPage) {
            $("#cart-content .cart-table").find(`tr:contains(${$row.find("td:nth-child(3)").text()})`).remove();
        } else {
            $(".tbl-full").find(`tr:contains(${$row.find("td:nth-child(2)").text()})`).remove();
        }
        updateCartTotal();
        updateBadge();
    });

    // Update Cart Total
    function updateCartTotal() {
        let total = 0;
        $("#cart-content tr").each(function () {
            const rowTotal = parseFloat($(this).find("td:nth-child(5)").text().replace("$", ""));
            if (!isNaN(rowTotal)) total += rowTotal;
        });
        $("#cart-content th:nth-child(5)").text(`$${total.toFixed(2)}`);
        $(".tbl-full th:nth-child(6)").text(`$${total.toFixed(2)}`);

        if ($("#cart-content tr").length === 1) {
            $("#cart-content .cart-table").append('<tr class="empty-cart"><td colspan="6">Cart is empty</td></tr>');
        }
        if ($(".tbl-full tr").length === 1) {
            $(".tbl-full").append('<tr class="empty-order"><td colspan="7">No items in cart</td></tr>');
        }
    }

    // Update Cart Badge
    function updateBadge() {
        const itemCount = $("#cart-content tr").length - 1; // Subtract header row
        $("#shopping-cart span").text(`🛒(${itemCount})`); // Update the cart icon text
    }
});