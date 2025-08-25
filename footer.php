    </div>
    <div class="footer shadow">
        <div class="footer-nav">
            <div class="nav-container">
                <div class="footer-brand">
                    <a href="index.php">Login System</a>
                </div>
                <p class="copyright">&copy; Copyright <?=date('Y')?> Company, Inc. All rights reserved.</p>
            </div>
        </div>
    </div>

    <script src="js/main.js"></script>

        <script>
            // Common JavaScript functions can be placed here
            <?php if (isset($pageScript)): ?>
            <?php echo $pageScript; ?>
            <?php endif; ?>
        </script>
        <script>
            const faviconTag = document.getElementById("faviconTag");
            const isDark = window.matchMedia("(prefers-color-scheme: dark)");

            const changeFavicon = () => {
                if (isDark.matches) faviconTag.href = "images/favicon/favicon_dark_theme.svg";
                else faviconTag.href = "images/favicon/favicon_light_theme.svg";
            };

            changeFavicon();

            setInterval(changeFavicon, 1000);

        </script>
</body>
</html>