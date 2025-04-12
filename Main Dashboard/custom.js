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
        $("#shopping-cart span").text(`🛒(${itemCount})`);
    }

    // Search Functionality
    $("#search-form").submit(function (e) {
        e.preventDefault();
        const searchTerm = $("#search-input").val().toLowerCase().trim();
        let found = false;

        // Define menu sections mapping
        const menuSections = {
            'snack': '#snack-pack',
            'snack pack': '#snack-pack',
            'chatpate': '#snack-pack',
            'laphing': '#snack-pack',
            'nimkin': '#snack-pack',
            'momo': '#momo',
            'veg momo': '#momo',
            'chicken momo': '#momo',
            'buff momo': '#momo',
            'chowmein': '#chowmein',
            'chicken chowmein': '#chowmein',
            'veg chowmein': '#chowmein',
            'khanaset': '#khanaset',
            'choila': '#khanaset',
            'goat bhutan': '#khanaset',
            'drinks': '#drinks',
            'lassi': '#drinks',
            'mango lassi': '#drinks',
            'juice': '#drinks',
            'watermelon juice': '#drinks',
            'beer': '#drinks',
            'soft drink': '#drinks'
        };

        // Check if search term matches any menu section
        for (let key in menuSections) {
            if (searchTerm.includes(key)) {
                const targetOffset = $(menuSections[key]).offset().top - 80;
                $("html, body").animate({ scrollTop: targetOffset }, 800);
                found = true;
                break;
            }
        }

        // If no match found, check individual menu items
        if (!found) {
            $('.food-menu-box').each(function () {
                const itemName = $(this).find('h4').text().toLowerCase();
                if (itemName.includes(searchTerm)) {
                    const targetOffset = $(this).offset().top - 80;
                    $("html, body").animate({ scrollTop: targetOffset }, 800);
                    found = true;
                    return false; // Break the loop
                }
            });
        }

        // If still not found, display message
        if (!found) {
            $('.search-message').remove();
            const $message = $('<p class="search-message" style="color: #fff; margin-top: 10px;">No menu item found matching "' + searchTerm + '"</p>');
            $('#search-form').after($message);
            setTimeout(() => {
                $message.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 3000);
        }
    });

    // Clear search input on focus
    $("#search-input").focus(function() {
        $('.search-message').remove();
    });

    // Smooth scroll to menu sections
    $(".category-link").click(function (e) {
        e.preventDefault();
        const targetId = $(this).attr("href");
        const targetOffset = $(targetId).offset().top - 80;
        $("html, body").animate({ scrollTop: targetOffset }, 800);
    });
});

if (localStorage.getItem("userRole") === "admin") {
    document.getElementById("admin-link").style.display = "block";
}