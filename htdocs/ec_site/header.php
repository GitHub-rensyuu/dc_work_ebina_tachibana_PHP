<?php
    if (!isset($show_header_menu)) {
        $show_header_menu = false;
    }
?>

<header class="site-header">

    <!-- EC SITE -->
    <a href="products.php" class="site-title">
        EC SITE
    </a>

    <?php if ($show_header_menu): ?>

        <!-- 右側メニュー -->
        <nav class="header-menu">

            <a href="cart.php">
                カート
            </a>

            <form action="logout.php" method="post">
                <input
                    type="submit"
                    value="ログアウト"
                >
            </form>

        </nav>

    <?php endif; ?>

</header>
