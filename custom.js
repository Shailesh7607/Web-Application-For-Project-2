$(document).ready(function() {
    // Toggle cart visibility
    $('#shopping-cart').click(function(e) {
        e.preventDefault();
        $('#cart-content').toggle();
    });

    // Add to cart
    $('.add-to-cart-form').submit(function(e) {
        e.preventDefault();
        const product_id = $(this).data('product-id');
        const quantity = $(this).find('.quantity-input').val();

        $.ajax({
            url: 'add_to_cart.php',
            method: 'POST',
            data: { product_id: product_id, quantity: quantity },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#shopping-cart span').text(`🛒(${response.cart_count})`);
                    alert('Item added to cart!');
                    location.reload(); // Refresh to update cart display
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert('Error adding item to cart.');
            }
        });
    });

    // Remove from cart
    $('.btn-delete').click(function(e) {
        e.preventDefault();
        const cart_id = $(this).data('cart-id');

        $.ajax({
            url: 'remove_from_cart.php',
            method: 'POST',
            data: { cart_id: cart_id },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#shopping-cart span').text(`🛒(${response.cart_count})`);
                    alert('Item removed from cart!');
                    location.reload(); // Refresh to update cart display
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert('Error removing item from cart.');
            }
        });
    });

    // Search functionality with scroll-to-section and friendly message
    $('#search-form').submit(function(e) {
        e.preventDefault();
        const query = $('#search-input').val().toLowerCase().trim();
        $('#search-message').text(''); // Clear any previous message

        let targetSectionId = null;

        $('.food-menu-box').each(function() {
            const name = $(this).find('.food-menu-desc h4').text().toLowerCase().trim();
            if (name.includes(query)) {
                const section = $(this).closest('.menu-section');
                targetSectionId = section.attr('id');
                return false; // Stop after first match
            }
        });

        if (targetSectionId) {
            const targetSection = $('#' + targetSectionId);
            if (targetSection.length) {
                const sectionTop = targetSection.offset().top;
                const sectionHeight = targetSection.outerHeight();
                const windowHeight = $(window).height();
                const scrollTo = sectionTop - (windowHeight / 2) + (sectionHeight / 2);

                $('html, body').animate({
                    scrollTop: scrollTo
                }, 800);

                $('#search-message').text('');
            }
        } else {
            $('#search-message').text("🍽️ Sorry, we couldn't find that dish. Please try a different name.");
        }
    });

    // Category links scroll to section and center it
    $('.category-link').click(function(e) {
        e.preventDefault();

        const href = $(this).attr('href');
        if (!href.startsWith('#')) return;

        const targetSection = $(href);
        if (targetSection.length) {
            const sectionTop = targetSection.offset().top;
            const sectionHeight = targetSection.outerHeight();
            const windowHeight = $(window).height();
            const scrollTo = sectionTop - (windowHeight / 2) + (sectionHeight / 2);

            $('html, body').animate({
                scrollTop: scrollTo
            }, 800);
        }
    });
});
