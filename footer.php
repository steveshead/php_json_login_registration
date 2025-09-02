    </div>
    <div class="footer shadow">
        <div class="footer-nav">
            <div class="nav-container">
                <div class="footer-brand">
                    <a href="index.php" class="uppercase">Login System</a>
                </div>
                <p class="copyright">&copy; Copyright <?=date('Y')?> Company, Inc. All rights reserved.</p>
            </div>
        </div>
    </div>

    <script src="js/main.js"></script>

        <script>
            // Common JavaScript functions can be placed here

            // Custom file input label update
            document.addEventListener('DOMContentLoaded', function() {
                const fileInputs = document.querySelectorAll('.custom-file-input input[type="file"]');

                fileInputs.forEach(input => {
                    input.addEventListener('change', function() {
                        const label = this.nextElementSibling;
                        if (this.files && this.files.length > 0) {
                            label.textContent = this.files[0].name;
                        } else {
                            label.textContent = 'Choose file';
                        }
                    });
                });
            });

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
